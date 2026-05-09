<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/Question.php';
require_once ROOT_PATH . '/models/AnswerLog.php';

function getProjectByCodeOrId(string $codeOrId): ?array
{
    if (ctype_digit($codeOrId)) {
        return getProject((int) $codeOrId);
    }

    $stmt = getDB()->prepare('SELECT * FROM projects WHERE code = ? LIMIT 1');
    $stmt->execute([$codeOrId]);
    $project = $stmt->fetch();
    return $project ?: null;
}

function getParticipantByToken(int $projectId, string $token): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM participants WHERE project_id = ? AND access_token = ? LIMIT 1');
    $stmt->execute([$projectId, $token]);
    $participant = $stmt->fetch();
    return $participant ?: null;
}

function canStartExam(array $project): array
{
    $status = function_exists('getProjectRuntimeStatus')
        ? getProjectRuntimeStatus($project)
        : ['allowed' => false, 'message' => 'Exam status helper is unavailable.'];

    return ['allowed' => (bool) $status['allowed'], 'message' => (string) ($status['message'] ?? '')];
}

function countSubmittedAttempts(int $participantId, int $projectId): int
{
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM exam_sessions WHERE participant_id = ? AND project_id = ? AND status IN ('submitted','expired')");
    $stmt->execute([$participantId, $projectId]);
    return (int) $stmt->fetchColumn();
}

function startExamSession(array $project, array $participant): array
{
    $access = canStartExam($project);
    if (!$access['allowed']) {
        return ['success' => false, 'message' => $access['message']];
    }

    $submitted = countSubmittedAttempts((int) $participant['id'], (int) $project['id']);
    if ($submitted >= (int) $project['max_attempts']) {
        return ['success' => false, 'message' => 'คุณใช้สิทธิ์สอบครบแล้ว'];
    }

    $questions = getQuestionsByProject((int) $project['id'], true);
    if (!$questions) {
        return ['success' => false, 'message' => 'ยังไม่มีข้อสอบในโครงการนี้'];
    }

    if ((int) $project['randomize_questions'] === 1) {
        shuffle($questions);
    }
    if ((int) $project['question_count'] > 0) {
        $questions = array_slice($questions, 0, (int) $project['question_count']);
    }

    $questionIds = array_map(static fn(array $q): int => (int) $q['id'], $questions);
    $attemptNo = $submitted + 1;
    $expiresAt = (new DateTimeImmutable())->modify('+' . (int) $project['time_limit_min'] . ' minutes')->format('Y-m-d H:i:s');

    $stmt = getDB()->prepare('
        INSERT INTO exam_sessions (participant_id, project_id, attempt_no, question_order, expires_at, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        (int) $participant['id'],
        (int) $project['id'],
        $attemptNo,
        json_encode($questionIds),
        $expiresAt,
        $_SERVER['REMOTE_ADDR'] ?? null,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    return ['success' => true, 'session_id' => (int) getDB()->lastInsertId()];
}

function getExamSession(int $sessionId): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM exam_sessions WHERE id = ? LIMIT 1');
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch();
    return $session ?: null;
}

function getAdminExamSessions(): array
{
    $stmt = getDB()->query('
        SELECT es.*, p.name AS project_name, pt.first_name, pt.last_name,
               CONCAT(pt.first_name, " ", pt.last_name) AS participant_name
        FROM exam_sessions es
        JOIN projects p ON p.id = es.project_id
        JOIN participants pt ON pt.id = es.participant_id
        ORDER BY es.started_at DESC
    ');

    return $stmt->fetchAll();
}

function getSessionQuestions(array $session): array
{
    $ids = json_decode((string) $session['question_order'], true);
    if (!is_array($ids) || !$ids) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = getDB()->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    $byId = [];
    foreach ($rows as $row) {
        $byId[(int) $row['id']] = $row;
    }

    $ordered = [];
    foreach ($ids as $id) {
        if (isset($byId[(int) $id])) {
            $ordered[] = $byId[(int) $id];
        }
    }

    return $ordered;
}

function isAnswerCorrect(array $question, string $answer): bool
{
    $expected = mb_strtolower(trim((string) $question['correct_answer']), 'UTF-8');
    $given = mb_strtolower(trim((string) $answer), 'UTF-8');
    
    return $given === $expected;
}

function submitExamSession(int $sessionId, array $answers): array
{
    $db = getDB();
    $session = getExamSession($sessionId);
    if (!$session || $session['status'] !== 'in_progress') {
        return ['success' => false, 'message' => 'ไม่พบ session หรือส่งข้อสอบแล้ว'];
    }

    $project = getProject((int) $session['project_id']);
    if (!$project) {
        return ['success' => false, 'message' => 'ไม่พบโครงการสอบ'];
    }

    $expired = !empty($session['expires_at']) && new DateTimeImmutable() > new DateTimeImmutable($session['expires_at']);
    $questions = getSessionQuestions($session);
    $savedAnswers = getSessionAnswers($sessionId); // Fetch auto-saved answers from DB
    $score = 0.0;
    $total = 0.0;

    try {
        $db->beginTransaction();
        
        // Delete existing logs to re-insert with correct status and score
        $delete = $db->prepare('DELETE FROM answer_logs WHERE session_id = ?');
        $delete->execute([$sessionId]);

        $insert = $db->prepare('
            INSERT INTO answer_logs (session_id, question_id, given_answer, is_correct, score_earned)
            VALUES (?, ?, ?, ?, ?)
        ');

        foreach ($questions as $question) {
            $qid = (int) $question['id'];
            
            // PRIORITY: Use DB answers first as they are more reliable across refreshes
            // Only use POST answers if DB answer is missing for that question
            $given = ($savedAnswers[$qid] ?? '');
            if (empty($given) && isset($answers[$qid])) {
                $given = trim((string)$answers[$qid]);
            }
            
            $weight = (float) $question['score_weight'];
            $total += $weight;
            
            $correct = $given !== '' && isAnswerCorrect($question, $given);
            $earned = $correct ? $weight : 0.0;
            $score += $earned;
            
            $insert->execute([$sessionId, $qid, $given ?: null, $correct ? 1 : 0, $earned]);
        }

        $percent = $total > 0 ? round(($score / $total) * 100, 2) : 0.0;
        $result = (!$expired && $percent >= (float) $project['pass_score']) ? 'pass' : 'fail';
        $status = $expired ? 'expired' : 'submitted';

        $update = $db->prepare('
            UPDATE exam_sessions
            SET score = ?, total_score = ?, percent = ?, status = ?, result = ?, submitted_at = NOW()
            WHERE id = ?
        ');
        $update->execute([$score, $total, $percent, $status, $result, $sessionId]);
        $db->commit();

        return ['success' => true, 'session_id' => $sessionId, 'result' => $result];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        logError('Submit exam failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการส่งข้อสอบ'];
    }
}

function deleteExamSession(int $sessionId): bool
{
    try {
        $db = getDB();
        $db->beginTransaction();
        
        // Delete associated answer logs first
        $stmt = $db->prepare('DELETE FROM answer_logs WHERE session_id = ?');
        $stmt->execute([$sessionId]);
        
        // Delete certificates if any
        $stmt = $db->prepare('DELETE FROM certificates WHERE session_id = ?');
        $stmt->execute([$sessionId]);
        
        // Delete the session
        $stmt = $db->prepare('DELETE FROM exam_sessions WHERE id = ?');
        $stmt->execute([$sessionId]);
        
        $db->commit();
        return true;
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        logError('Delete session failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return false;
    }
}

function getSessionAnswerLogs(int $sessionId): array
{
    $stmt = getDB()->prepare('
        SELECT al.*, q.question_text, q.choices, q.correct_answer, q.explanation, q.type
        FROM answer_logs al
        JOIN questions q ON q.id = al.question_id
        WHERE al.session_id = ?
        ORDER BY al.id ASC
    ');
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll();
}

class ExamSession extends BaseModel
{
}
