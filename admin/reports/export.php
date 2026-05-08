<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Report.php';

requireLogin();

$rows = projectReportRows();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="project-reports.csv"');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
fputcsv($out, ['Project', 'Code', 'Status', 'Participants', 'Questions', 'Sessions', 'Passes', 'Average percent', 'Pass rate']);
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
        $passRate,
    ]);
}

