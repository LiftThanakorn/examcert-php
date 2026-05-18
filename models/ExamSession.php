<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/Question.php';
require_once ROOT_PATH . '/models/AnswerLog.php';

function getProjectByCodeOrId(string $codeOrId): ?array
{
    if (ctype_digit($codeOrId)) {
        return getProject((int) $codeOrId);
    }

    $stmt = getDB()->prepare('
        SELECT p.*,
            (SELECT COUNT(*) FROM participants pp WHERE pp.project_id = p.id) AS participant_count,
            (SELECT COUNT(*) FROM questions q WHERE q.project_id = p.id) AS question_count_total
        FROM projects p
        WHERE p.code = ? LIMIT 1
    ');
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

function getParticipantPassingSession(int $participantId, int $projectId): ?array
{
    $stmt = getDB()->prepare("
        SELECT id FROM exam_sessions 
        WHERE participant_id = ? AND project_id = ? AND result = 'pass' AND status IN ('submitted', 'expired')
        LIMIT 1
    ");
    $stmt->execute([$participantId, $projectId]);
    $session = $stmt->fetch();
    return $session ?: null;
}

function countSubmittedAttempts(int $participantId, int $projectId): int
{
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM exam_sessions WHERE participant_id = ? AND project_id = ? AND status IN ('submitted','expired')");
    $stmt->execute([$participantId, $projectId]);
    return (int) $stmt->fetchColumn();
}

function getParticipantAttemptStatus(int $participantId, int $projectId, ?array $project = null): array
{
    $project = $project ?: getProject($projectId);
    $maxAttempts = max(1, (int) ($project['max_attempts'] ?? 1));
    $usedAttempts = countSubmittedAttempts($participantId, $projectId);

    $stmt = getDB()->prepare('
        SELECT id, started_at, expires_at
        FROM exam_sessions
        WHERE participant_id = ? AND project_id = ? AND status = "in_progress"
        ORDER BY id DESC
        LIMIT 1
    ');
    $stmt->execute([$participantId, $projectId]);
    $activeSession = $stmt->fetch() ?: null;

    $hasActiveSession = false;
    $activeSessionExpired = false;
    if ($activeSession) {
        $activeSessionExpired = !empty($activeSession['expires_at'])
            && new DateTimeImmutable() > new DateTimeImmutable((string) $activeSession['expires_at']);
        $hasActiveSession = !$activeSessionExpired;
    }

    $displayUsedAttempts = $usedAttempts + ($activeSessionExpired ? 1 : 0);
    $remainingAttempts = max(0, $maxAttempts - $displayUsedAttempts);
    $canStart = $hasActiveSession || $remainingAttempts > 0;

    $status = 'available';
    $message = sprintf('สอบแล้ว %d จาก %d ครั้ง เหลือ %d ครั้ง', $displayUsedAttempts, $maxAttempts, $remainingAttempts);
    if ($hasActiveSession) {
        $status = 'in_progress';
        $message = 'กำลังทำข้อสอบอยู่ สามารถกลับไปทำต่อได้';
    } elseif ($remainingAttempts <= 0) {
        $status = 'exhausted';
        $message = sprintf('ใช้สิทธิ์สอบครบแล้ว (%d/%d ครั้ง)', $displayUsedAttempts, $maxAttempts);
    } elseif ($displayUsedAttempts === 0) {
        $message = sprintf('ยังไม่เคยสอบ เหลือสิทธิ์ %d ครั้ง', $remainingAttempts);
    }

    return [
        'max_attempts' => $maxAttempts,
        'used_attempts' => $displayUsedAttempts,
        'finalized_attempts' => $usedAttempts,
        'remaining_attempts' => $remainingAttempts,
        'has_active_session' => $hasActiveSession,
        'active_session_id' => $hasActiveSession ? (int) $activeSession['id'] : null,
        'active_session_expired' => $activeSessionExpired,
        'can_start' => $canStart,
        'status' => $status,
        'message' => $message,
    ];
}

function startExamSession(array $project, array $participant): array
{
    $now = new DateTimeImmutable();
    
    // Check for existing in-progress session to resume
    $stmt = getDB()->prepare("SELECT id, expires_at FROM exam_sessions WHERE participant_id = ? AND project_id = ? AND status = 'in_progress' LIMIT 1");
    $stmt->execute([(int) $participant['id'], (int) $project['id']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $sessionExpired = !empty($existing['expires_at']) && $now > new DateTimeImmutable((string) $existing['expires_at']);
        if ($sessionExpired) {
            submitExamSession((int) $existing['id'], getSessionAnswers((int) $existing['id']));
        } else {
            $access = canStartExam($project);
            if ($access['allowed'] || (int) ($project['auto_submit_on_close'] ?? 1) === 0) {
                return ['success' => true, 'session_id' => (int) $existing['id'], 'resumed' => true];
            }

            return ['success' => false, 'message' => $access['message']];
        }
    }

    $access = canStartExam($project);
    if (!$access['allowed']) {
        return ['success' => false, 'message' => $access['message']];
    }

    $attemptStatus = getParticipantAttemptStatus((int) $participant['id'], (int) $project['id'], $project);
    if ($attemptStatus['remaining_attempts'] <= 0) {
        return ['success' => false, 'message' => $attemptStatus['message']];
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
    $attemptNo = (int) $attemptStatus['finalized_attempts'] + 1;
    
    // Calculate session expiration based on time limit
    $sessionExpire = $now->modify('+' . (int) $project['time_limit_min'] . ' minutes');
    
    // If project has a hard end time, cap the session expiration
    if (!empty($project['exam_end'])) {
        $projectEnd = new DateTimeImmutable((string) $project['exam_end']);
        if ($sessionExpire > $projectEnd) {
            $sessionExpire = $projectEnd;
        }
    }
    
    $expiresAt = $sessionExpire->format('Y-m-d H:i:s');

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

function sessionHasQuestion(array $session, int $questionId): bool
{
    $ids = json_decode((string) $session['question_order'], true);
    if (!is_array($ids)) {
        return false;
    }

    return in_array($questionId, array_map('intval', $ids), true);
}

function isAnswerCorrect(array $question, string $answer): bool
{
    $type = (string) ($question['type'] ?? 'multiple_choice');
    $expected = trim((string) $question['correct_answer']);
    $given = trim((string) $answer);

    if ($type === 'multiple_choice') {
        $expected = normalizeChoiceKey($expected);
        $given = normalizeChoiceKey($given);
    }

    $expected = textLower($expected);
    $given = textLower($given);
    
    return $given === $expected;
}

function normalizeSubmittedAnswer(mixed $answer): string
{
    if (is_array($answer) || is_object($answer)) {
        return '';
    }

    return trim((string) $answer);
}

function normalizeRatingScaleAnswer(string $answer): string
{
    $value = filter_var($answer, FILTER_VALIDATE_INT);
    if ($value === false || $value < 1 || $value > 5) {
        return '';
    }

    return (string) $value;
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
    $savedAnswers = getSessionAnswers($sessionId);
    $score = 0.0;
    $total = 0.0;

    try {
        $db->beginTransaction();
        
        // Delete existing logs to re-insert with correct status and score
        $delete = $db->prepare('DELETE FROM answer_logs WHERE session_id = ?');
        $delete->execute([$sessionId]);

        $insert = $db->prepare('
            INSERT INTO answer_logs (session_id, question_id, given_answer, is_correct, score_earned, grading_status)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        foreach ($questions as $question) {
            $qid = (int) $question['id'];
            $type = (string) ($question['type'] ?? 'multiple_choice');
            
            $postAnswer = $answers[$qid] ?? $answers[(string) $qid] ?? null;
            $given = normalizeSubmittedAnswer($postAnswer);
            if ($given === '') {
                $given = normalizeSubmittedAnswer($savedAnswers[$qid] ?? '');
            }
            if ($type === 'multiple_choice' && $given !== '') {
                $given = normalizeChoiceKey($given);
            }
            if ($type === 'rating_scale' && $given !== '') {
                $given = normalizeRatingScaleAnswer($given);
            }
            
            $isSubjective = $type === 'subjective';
            $isRatingScale = $type === 'rating_scale';
            $weight = $isSubjective ? 0.0 : ($isRatingScale ? 5.0 : (float) $question['score_weight']);
            $total += $weight;

            if ($isSubjective) {
                $correct = null;
                $earned = 0.0;
                $gradingStatus = 'pending_manual';
            } elseif ($isRatingScale) {
                $correct = null;
                $earned = $given !== '' ? (float) $given : 0.0;
                $gradingStatus = 'auto';
            } else {
                $correct = $given !== '' && isAnswerCorrect($question, $given);
                $earned = $correct ? $weight : 0.0;
                $gradingStatus = 'auto';
            }
            $score += $earned;
            
            $insert->execute([$sessionId, $qid, $given === '' ? null : $given, $correct === null ? null : ($correct ? 1 : 0), $earned, $gradingStatus]);
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
        SELECT al.*, q.question_text, q.choices, q.correct_answer, q.explanation, q.type, q.category
        FROM answer_logs al
        JOIN questions q ON q.id = al.question_id
        WHERE al.session_id = ?
        ORDER BY al.id ASC
    ');
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll();
}

function getSessionCategoryScores(int $sessionId): array
{
    $defaultCategory = 'ทั่วไป';
    $stmt = getDB()->prepare('
        SELECT
            COALESCE(NULLIF(TRIM(q.category), ""), ?) AS category,
            SUM(al.score_earned) AS score,
            COUNT(*) AS answer_count
        FROM answer_logs al
        JOIN questions q ON q.id = al.question_id
        WHERE al.session_id = ?
        GROUP BY COALESCE(NULLIF(TRIM(q.category), ""), ?)
        ORDER BY category ASC
    ');
    $stmt->execute([$defaultCategory, $sessionId, $defaultCategory]);
    return $stmt->fetchAll();
}
