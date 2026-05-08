<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Exam.php';
require_once ROOT_PATH . '/src/Certificate.php';

class PublicExamController
{
    public function entry(): void
    {
        $code = trim((string) ($_GET['project'] ?? ''));
        $project = $code !== '' ? getProjectByCodeOrId($code) : null;
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $error = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $code = trim((string) ($_POST['project'] ?? ''));
                $project = getProjectByCodeOrId($code);
                $participant = $project ? getParticipantByToken((int) $project['id'], trim((string) ($_POST['access_token'] ?? ''))) : null;

                if (!$project || !$participant) {
                    $error = 'ไม่พบโครงการหรือ token ไม่ถูกต้อง';
                } else {
                    $result = startExamSession($project, $participant);
                    if ($result['success']) {
                        redirect('public/take-exam.php?session_id=' . (int) $result['session_id']);
                    }
                    $error = $result['message'];
                }
            }
        }

        $pageTitle = 'เข้าสอบ';
        require VIEWS_PATH . '/public/exam.php';
    }

    public function take(): void
    {
        $sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
        $session = getExamSession($sessionId);
        if (!$session) {
            http_response_code(404);
            exit('Session not found.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                exit('Bad request.');
            }

            $result = submitExamSession($sessionId, $_POST['answers'] ?? []);
            if ($result['success']) {
                redirect('public/result.php?session_id=' . $sessionId);
            }
            exit(e($result['message']));
        }

        if ($session['status'] !== 'in_progress') {
            redirect('public/result.php?session_id=' . $sessionId);
        }

        $project = getProject((int) $session['project_id']);
        $questions = getSessionQuestions($session);
        $secondsLeft = max(0, strtotime((string) $session['expires_at']) - time());
        $pageTitle = 'ทำข้อสอบ';

        require VIEWS_PATH . '/public/take-exam.php';
    }

    public function result(): void
    {
        $sessionId = (int) ($_GET['session_id'] ?? 0);
        $session = getExamSession($sessionId);
        if (!$session) {
            http_response_code(404);
            exit('Session not found.');
        }

        $project = getProject((int) $session['project_id']);
        $pageTitle = 'ผลสอบ';

        require VIEWS_PATH . '/public/result.php';
    }

    public function verify(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $certificate = $token !== '' ? getCertificateByToken($token) : null;
        $pageTitle = 'Verify Certificate';

        require VIEWS_PATH . '/public/verify.php';
    }
}
