<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';

function projectReportRows(): array
{
    $stmt = getDB()->query('
        SELECT
            p.id,
            p.name,
            p.code,
            p.status,
            (SELECT COUNT(*) FROM participants WHERE project_id = p.id) AS participant_count,
            (SELECT COUNT(*) FROM questions WHERE project_id = p.id) AS question_count,
            (SELECT COUNT(*) FROM exam_sessions WHERE project_id = p.id AND status IN ("submitted", "expired")) AS session_count,
            (SELECT COUNT(*) FROM exam_sessions WHERE project_id = p.id AND status = "in_progress") AS in_progress_count,
            (SELECT COUNT(*) FROM exam_sessions WHERE project_id = p.id AND status IN ("submitted", "expired") AND result = "pass") AS pass_count,
            (SELECT AVG(percent) FROM exam_sessions WHERE project_id = p.id AND status IN ("submitted", "expired")) AS avg_percent
        FROM projects p
        ORDER BY p.created_at DESC
    ');

    return $stmt->fetchAll();
}

class ReportController
{
    public function index(): void
    {
        requireLogin();

        $rows = projectReportRows();
        $pageTitle = 'รายงานสรุปภาพรวม';
        $breadcrumb = ['Dashboard', 'รายงาน'];
        $viewFile = VIEWS_PATH . '/reports/index.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function export(): void
    {
        requireLogin();

        $rows = projectReportRows();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="project-reports.csv"');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            http_response_code(500);
            exit('Unable to open output stream.');
        }

        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['โครงการ', 'รหัส', 'สถานะ', 'ผู้มีสิทธิ์', 'จำนวนข้อสอบ', 'ส่งแล้ว', 'กำลังสอบ', 'สอบผ่าน', 'คะแนนเฉลี่ย', 'อัตราการผ่าน']);

        foreach ($rows as $row) {
            $sessions = (int) $row['session_count'];
            $passes = (int) $row['pass_count'];
            $passRate = $sessions > 0 ? round(($passes / $sessions) * 100, 2) : 0;

            fputcsv($out, [
                $row['name'],
                $row['code'],
                $row['status'],
                $row['participant_count'],
                $row['question_count'],
                $sessions,
                (int) $row['in_progress_count'],
                $passes,
                $row['avg_percent'] !== null ? round((float) $row['avg_percent'], 2) : '',
                $passRate . '%',
            ]);
        }
    }
}
