<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/ExamSession.php';
require_once ROOT_PATH . '/models/AnswerLog.php';

$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

if ($action === 'save_answer') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
        jsonResponse(false, 'Invalid request.', [], 400);
    }

    $sessionId = (int) ($_POST['session_id'] ?? 0);
    $questionId = (int) ($_POST['question_id'] ?? 0);
    $answer = trim((string) ($_POST['answer'] ?? ''));

    if ($sessionId <= 0 || $questionId <= 0) {
        jsonResponse(false, 'Invalid session or question.', [], 400);
    }

    $session = getExamSession($sessionId);
    if (!$session || $session['status'] !== 'in_progress') {
        jsonResponse(false, 'Session is not active.', [], 403);
    }

    if (!sessionHasQuestion($session, $questionId)) {
        jsonResponse(false, 'Question does not belong to this session.', [], 403);
    }

    $success = logAnswer($sessionId, $questionId, $answer);
    jsonResponse($success, $success ? 'Answer saved.' : 'Failed to save answer.');
}

if ($action === 'check_time') {
    $sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
    $session = $sessionId > 0 ? getExamSession($sessionId) : null;
    if (!$session) {
        jsonResponse(false, 'Session not found.', ['should_submit' => false], 404);
    }

    $project = getProject((int) $session['project_id']);
    if (!$project) {
        jsonResponse(false, 'Project not found.', ['should_submit' => false], 404);
    }

    $projectStatus = getProjectRuntimeStatus($project);
    $autoSubmitOnProjectClose = (int) ($project['auto_submit_on_close'] ?? 1) === 1;
    $expiresLeft = null;
    if (!empty($session['expires_at'])) {
        $expiresLeft = max(0, (new DateTimeImmutable((string) $session['expires_at']))->getTimestamp() - time());
    }
    $secondsLeft = $expiresLeft;
    if ($autoSubmitOnProjectClose && $projectStatus['seconds_left'] !== null) {
        $secondsLeft = $secondsLeft === null ? (int) $projectStatus['seconds_left'] : min($secondsLeft, (int) $projectStatus['seconds_left']);
    }
    $shouldSubmit = $session['status'] !== 'in_progress'
        || $secondsLeft === 0
        || ($autoSubmitOnProjectClose && !$projectStatus['allowed']);

    jsonResponse(true, 'OK', [
        'session_status' => $session['status'],
        'project_status' => $projectStatus['status'],
        'seconds_left' => $secondsLeft,
        'warning' => (bool) $projectStatus['warning'],
        'should_submit' => $shouldSubmit,
        'message' => $projectStatus['message'],
    ]);
}

if ($action === 'search_participants') {
    $projectCode = trim((string) ($_GET['project_code'] ?? ''));
    $query = trim((string) ($_GET['query'] ?? ''));

    if ($projectCode === '' || $query === '') {
        jsonResponse(false, 'Missing project code or query.', [], 400);
    }

    $project = getProjectByCodeOrId($projectCode);
    if (!$project) {
        jsonResponse(false, 'Project not found.', [], 404);
    }

    $runtimeStatus = getProjectRuntimeStatus($project);
    if (!$runtimeStatus['allowed']) {
        jsonResponse(false, (string) ($runtimeStatus['message'] ?? 'Exam is not open.'), [], 403);
    }

    $stmt = getDB()->prepare('
        SELECT id, first_name, last_name, title
        FROM participants 
        WHERE project_id = ? 
        AND (first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, " ", last_name) LIKE ?)
        LIMIT 10
    ');
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$project['id'], $searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
    foreach ($results as &$participant) {
        $participant['attempt_status'] = getParticipantAttemptStatus(
            (int) $participant['id'],
            (int) $project['id'],
            $project
        );
    }
    unset($participant);

    jsonResponse(true, 'Success', $results);
}

jsonResponse(false, 'Unknown exam API action.', [], 400);
