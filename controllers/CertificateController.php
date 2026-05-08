<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/Certificate.php';

class CertificateController
{
    public function index(): void
    {
        requireLogin();

        $certificates = getCertificates();
        $flash = getFlash();
        $pageTitle = 'ใบเซอร์';
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
}
