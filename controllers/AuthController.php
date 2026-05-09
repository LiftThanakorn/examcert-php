<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';

class AuthController
{
    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('admin/dashboard.php');
        }

        $error = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $error = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = authenticateAdmin(
                    (string) ($_POST['username'] ?? ''),
                    (string) ($_POST['password'] ?? '')
                );

                if ($result['success']) {
                    setFlash('success', $result['message']);
                    redirect('admin/dashboard.php');
                }

                setFlash('error', $result['message']);
                $error = $result['message'];
            }
        }

        $pageTitle = 'เข้าสู่ระบบ';
        require VIEWS_PATH . '/auth/login.php';
    }
}
