<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../config/database.php';

requireLogin();

$stmt = getDB()->query('
    SELECT es.id, p.name AS project_name, pt.first_name, pt.last_name,
           es.attempt_no, es.score, es.total_score, es.percent, es.status, es.result,
           es.started_at, es.submitted_at
    FROM exam_sessions es
    JOIN projects p ON p.id = es.project_id
    JOIN participants pt ON pt.id = es.participant_id
    ORDER BY es.started_at DESC
');
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="exam-sessions.csv"');

$out = fopen('php://output', 'w');
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

