<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/ExamSession.php';

class ExamController
{
    public function sessions(): void
    {
        requireLogin();

        $sessions = getAdminExamSessions();
        $pageTitle = 'ประวัติการเข้าสอบ';
        $breadcrumb = ['Dashboard', 'ผลการสอบ'];
        $viewFile = VIEWS_PATH . '/exam-sessions/index.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function exportSessions(): void
    {
        requireLogin();

        $rows = getAdminExamSessions();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="exam-sessions.csv"');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            http_response_code(500);
            exit('Unable to open output stream.');
        }

        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Project', 'First name', 'Last name', 'Attempt', 'Score', 'Total', 'Percent', 'Status', 'Result', 'Started at', 'Submitted at']);

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['project_name'],
                $row['first_name'],
                $row['last_name'],
                $row['attempt_no'],
                $row['score'],
                $row['total_score'],
                $row['percent'],
                $row['status'],
                $row['result'],
                $row['started_at'],
                $row['submitted_at'],
            ]);
        }
    }
}
