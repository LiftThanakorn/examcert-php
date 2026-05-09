<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ExamSession.php';
require_once ROOT_PATH . '/models/Certificate.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/Project.php';

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
                $code = trim((string) ($_POST['project_code'] ?? ''));
                $project = getProjectByCodeOrId($code);
                
                $firstName = trim((string) ($_POST['first_name'] ?? ''));
                $lastName = trim((string) ($_POST['last_name'] ?? ''));
                $token = trim((string) ($_POST['access_token'] ?? ''));

                $participant = $project ? getParticipantByAuth((int) $project['id'], $firstName, $lastName, $token) : null;

                if (!$project || !$participant) {
                    $error = 'ไม่พบโครงการหรือข้อมูลยืนยันตัวตนไม่ถูกต้อง';
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
        $bodyClass = 'bg-gray-900 font-sans'; // Dark background for the entry screen
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/exam/entry.php';
        require VIEWS_PATH . '/layout/footer.php';
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
                http_response_code(400);
                echo 'Bad request.';
                exit;
            }

            $result = submitExamSession($sessionId, $_POST['answers'] ?? []);
            if ($result['success']) {
                redirect('public/result.php?session_id=' . $sessionId);
            }
            echo e($result['message']);
            exit;
        }

        if ($session['status'] !== 'in_progress') {
            redirect('public/result.php?session_id=' . $sessionId);
        }

        $project = getProject((int) $session['project_id']);
        $participant = getParticipant((int) $session['participant_id']);
        $questions = getSessionQuestions($session);
        $secondsLeft = max(0, strtotime((string) $session['expires_at']) - time());
        $pageTitle = 'ทำข้อสอบ';
        $bodyClass = 'bg-[#F9F8F6] font-sans';
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/exam/start.php';
        require VIEWS_PATH . '/layout/footer.php';
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
        $participant = getParticipant((int) $session['participant_id']);
        $certificate = $session['result'] === 'pass' ? getCertificateBySession($sessionId) : null;
        $pageTitle = 'ผลสอบ';
        $bodyClass = 'bg-gray-900 font-sans'; // Dark background for result screen
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/exam/result.php';
        require VIEWS_PATH . '/layout/footer.php';
    }

    public function verify(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $certificate = $token !== '' ? getCertificateByToken($token) : null;
        $pageTitle = 'ตรวจสอบเกียรติบัตร';
        $bodyClass = 'bg-gray-50 font-sans';
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/certificates/verify.php';
        require VIEWS_PATH . '/layout/footer.php';
    }
}
