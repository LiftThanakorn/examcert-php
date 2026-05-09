<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function currentAdminName(): string
{
    return (string) ($_SESSION['admin_name'] ?? '');
}

function currentAdminId(): ?int
{
    return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('admin/login.php');
    }
}

function authenticateAdmin(string $username, string $password): array
{
    $username = trim($username);

    if ($username === '' || $password === '') {
        return ['success' => false, 'message' => 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน'];
    }

    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, (string) $admin['password_hash'])) {
            logError('Admin login failed', [
                'username' => $username,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
            return ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = (string) $admin['username'];
        $_SESSION['admin_name'] = (string) $admin['full_name'];
        $_SESSION['admin_role'] = (string) $admin['role'];

        $stmt = $db->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
        $stmt->execute([(int) $admin['id']]);

        return ['success' => true, 'message' => 'เข้าสู่ระบบสำเร็จ'];
    } catch (Throwable $e) {
        logError('Admin login error', ['error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'];
    }
}

function logoutAdmin(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

class Admin extends BaseModel
{
}
