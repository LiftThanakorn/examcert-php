<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';

function logAnswer(int $sessionId, int $questionId, string $answer): bool
{
    try {
        $db = getDB();
        
        // Check if already answered
        $stmt = $db->prepare('SELECT id FROM answer_logs WHERE session_id = ? AND question_id = ?');
        $stmt->execute([$sessionId, $questionId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $db->prepare('UPDATE answer_logs SET given_answer = ?, answered_at = NOW() WHERE id = ?');
            $stmt->execute([$answer, (int) $existing['id']]);
        } else {
            $stmt = $db->prepare('INSERT INTO answer_logs (session_id, question_id, given_answer) VALUES (?, ?, ?)');
            $stmt->execute([$sessionId, $questionId, $answer]);
        }
        
        return true;
    } catch (Throwable $e) {
        logError('Log answer failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return false;
    }
}

function getSessionAnswers(int $sessionId): array
{
    $stmt = getDB()->prepare('SELECT question_id, given_answer FROM answer_logs WHERE session_id = ?');
    $stmt->execute([$sessionId]);
    $rows = $stmt->fetchAll();
    
    $answers = [];
    foreach ($rows as $row) {
        $answers[(int) $row['question_id']] = $row['given_answer'];
    }
    return $answers;
}
