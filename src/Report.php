<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

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

