<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ExamSession.php';
require_once ROOT_PATH . '/models/Certificate.php';
require_once ROOT_PATH . '/models/Participant.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/CertTemplate.php';

class PublicExamController
{
    public function entry(): void
    {
        $code = trim((string) ($_GET['project'] ?? ''));
        $project = $code !== '' ? getProjectByCodeOrId($code) : null;
        
        if (!$project) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $projectCode = $code;
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
        $bodyClass = 'bg-mesh min-h-screen flex items-center justify-center p-6 font-sans';
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/exam/entry.php';
        require VIEWS_PATH . '/layout/footer.php';
    }

    public function take(): void
    {
        $sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
        $session = getExamSession($sessionId);
        if (!$session) {
            abortResponse(404, 'Session not found.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                abortResponse(400, 'Bad request.');
            }

            $result = submitExamSession($sessionId, $_POST['answers'] ?? []);
            if ($result['success']) {
                if ($result['result'] === 'pass') {
                    issueCertificateFromSession($sessionId, null);
                }
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

        // Calculate seconds left: Min of (session expires_at) and (project exam_end)
        $now = time();
        $sessionEnd = strtotime((string) $session['expires_at']);
        $effectiveEnd = $sessionEnd;

        if (!empty($project['exam_end'])) {
            $projectEnd = strtotime((string) $project['exam_end']);
            if ($projectEnd < $sessionEnd) {
                $effectiveEnd = $projectEnd;
            }
        }
        
        $secondsLeft = max(0, $effectiveEnd - $now);

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
            abortResponse(404, 'Session not found.');
        }

        $project = getProject((int) $session['project_id']);
        $participant = getParticipant((int) $session['participant_id']);
        $certificate = $session['result'] === 'pass' ? getCertificateBySession($sessionId) : null;
        $answerLogs = getSessionAnswerLogs($sessionId);
        
        $pageTitle = 'ผลสอบ';
        $bodyClass = 'bg-mesh min-h-screen flex flex-col items-center justify-start p-6 font-sans'; 
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/exam/result.php';
        require VIEWS_PATH . '/layout/footer.php';
    }

    public function verify(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $certificate = $token !== '' ? getCertificateByToken($token) : null;
        $pageTitle = 'ตรวจสอบเกียรติบัตร';
        $bodyClass = 'bg-mesh min-h-screen flex flex-col items-center justify-center p-6 font-sans';
        require VIEWS_PATH . '/layout/header.php';
        require VIEWS_PATH . '/certificates/verify.php';
        require VIEWS_PATH . '/layout/footer.php';
    }

    public function renderCertificate(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $certificate = $token !== '' ? getCertificateByToken($token) : null;
        if (!$certificate) {
            abortResponse(404, 'Certificate not found.');
        }
        $template = getCertificateTemplate((int) ($certificate['template_id'] ?: 1));
        if (!$template) {
            abortResponse(404, 'Certificate template not found.');
        }

        $viewPath = VIEWS_PATH . '/certificates/render.php';
        if (!file_exists($viewPath)) {
            logError('Certificate render view missing', ['path' => $viewPath]);
            abortResponse(500, 'Certificate render view is missing.');
        }

        require $viewPath;
    }

    public function downloadCertificate(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        if ($token === '') {
            abortResponse(400, 'Invalid token.');
        }

        $certificate = getCertificateByToken($token);
        if (!$certificate || (int) $certificate['is_revoked'] === 1) {
            abortResponse(404, 'Certificate not found or revoked.');
        }

        $filePath = ROOT_PATH . '/' . $certificate['file_path'];
        if (!is_file($filePath)) {
            writeCertificatePdf($token);
            if (!is_file($filePath)) {
                abortResponse(404, 'Certificate file not found.');
            }
        }

        incrementCertificateDownload((int) $certificate['id']);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $certificate['cert_number'] . '.pdf"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
