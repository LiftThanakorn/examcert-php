<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Project.php';
require_once __DIR__ . '/Participant.php';
require_once __DIR__ . '/Question.php';

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
    if ($project['status'] !== 'active') {
        return ['allowed' => false, 'message' => 'โครงการสอบยังไม่เปิดใช้งาน'];
    }

    $now = new DateTimeImmutable();
    if (!empty($project['exam_start']) && $now < new DateTimeImmutable($project['exam_start'])) {
        return ['allowed' => false, 'message' => 'ยังไม่ถึงเวลาเปิดสอบ'];
    }
    if (!empty($project['exam_end']) && $now > new DateTimeImmutable($project['exam_end'])) {
        return ['allowed' => false, 'message' => 'หมดเวลาการสอบแล้ว'];
    }

    return ['allowed' => true, 'message' => ''];
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
    $expected = trim((string) $question['correct_answer']);
    if ($question['type'] === 'fill_blank') {
        return mb_strtolower(trim($answer)) === mb_strtolower($expected);
    }

    return trim($answer) === $expected;
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
    $score = 0.0;
    $total = 0.0;

    try {
        $db->beginTransaction();
        $delete = $db->prepare('DELETE FROM answer_logs WHERE session_id = ?');
        $delete->execute([$sessionId]);

        $insert = $db->prepare('
            INSERT INTO answer_logs (session_id, question_id, given_answer, is_correct, score_earned)
            VALUES (?, ?, ?, ?, ?)
        ');

        foreach ($questions as $question) {
            $qid = (int) $question['id'];
            $given = trim((string) ($answers[$qid] ?? ''));
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

