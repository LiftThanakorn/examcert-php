<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/Dashboard.php';

class DashboardController
{
    public function index(): void
    {
        requireLogin();

        $flash = getFlash();
        $stats = dashboardStats();
        $passRate = $stats['submitted_sessions'] > 0 ? round(($stats['passed_sessions'] / $stats['submitted_sessions']) * 100, 2) : 0;
        $projects = recentProjects();
        $cards = [
            'โครงการทั้งหมด' => $stats['projects'],
            'โครงการที่เปิด' => $stats['active_projects'],
            'ผู้มีสิทธิ์สอบ' => $stats['participants'],
            'ข้อสอบ' => $stats['questions'],
            'ส่งข้อสอบแล้ว' => $stats['submitted_sessions'],
            'สอบผ่าน' => $stats['passed_sessions'],
            'Pass rate' => $passRate . '%',
            'ใบเซอร์' => $stats['certificates'],
        ];

        $pageTitle = 'Dashboard';
        $viewFile = VIEWS_PATH . '/dashboard/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }
}

