<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Certificate.php';

class CertificateController
{
    public function index(): void
    {
        requireLogin();

        $certificates = getCertificates();
        $pageTitle = 'จัดการใบเกียรติบัตร';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร'];
        $viewFile = VIEWS_PATH . '/certificates/index.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function issue(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Bad request.');
        }

        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $result = issueCertificateFromSession($sessionId, currentAdminId());
        setFlash($result['success'] ? 'success' : 'error', $result['message']);

        redirect('admin/certificates/');
    }

    public function download(): void
    {
        requireLogin();
        $token = trim((string) ($_GET['token'] ?? ''));
        $certificate = $token !== '' ? getCertificateByToken($token) : null;
        if (!$certificate || (int) $certificate['is_revoked'] === 1) {
            http_response_code(404);
            echo 'Certificate not available.';
            exit;
        }

        $path = ROOT_PATH . '/' . $certificate['file_path'];
        if (!is_file($path)) {
            writeCertificatePdf($token);
        }
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Certificate file not found.';
            exit;
        }

        incrementCertificateDownload((int) $certificate['id']);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function revoke(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Bad request.';
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $revoked = (string) ($_POST['action'] ?? 'revoke') === 'revoke';
        $reason = trim((string) ($_POST['reason'] ?? ''));
        $ok = markCertificateRevoked($id, $revoked, $reason ?: null);
        setFlash($ok ? 'success' : 'error', $revoked ? 'ยกเลิกใบเกียรติบัตรเรียบร้อยแล้ว' : 'คืนสถานะใบเกียรติบัตรเรียบร้อยแล้ว');
        redirect('admin/certificates/');
    }
}
