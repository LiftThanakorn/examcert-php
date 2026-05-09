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
            COUNT(DISTINCT pt.id) AS participant_count,
            COUNT(DISTINCT q.id) AS question_count,
            COUNT(DISTINCT es.id) AS session_count,
            SUM(CASE WHEN es.status = "submitted" AND es.result = "pass" THEN 1 ELSE 0 END) AS pass_count,
            AVG(CASE WHEN es.status IN ("submitted","expired") THEN es.percent ELSE NULL END) AS avg_percent
        FROM projects p
        LEFT JOIN participants pt ON pt.project_id = p.id
        LEFT JOIN questions q ON q.project_id = p.id
        LEFT JOIN exam_sessions es ON es.project_id = p.id
        GROUP BY p.id
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
        fputcsv($out, ['โครงการ', 'รหัส', 'สถานะ', 'ผู้มีสิทธิ์', 'จำนวนข้อสอบ', 'การเข้าสอบ', 'สอบผ่าน', 'คะแนนเฉลี่ย', 'อัตราการผ่าน']);

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
                $passes,
                $row['avg_percent'] !== null ? round((float) $row['avg_percent'], 2) : '',
                $passRate . '%',
            ]);
        }
    }
}
