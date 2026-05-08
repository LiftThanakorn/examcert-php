<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function dashboardStats(): array
{
    $db = getDB();

    return [
        'projects' => (int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
        'active_projects' => (int) $db->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn(),
        'participants' => (int) $db->query('SELECT COUNT(*) FROM participants')->fetchColumn(),
        'questions' => (int) $db->query('SELECT COUNT(*) FROM questions')->fetchColumn(),
        'submitted_sessions' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE status IN ('submitted','expired')")->fetchColumn(),
        'passed_sessions' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE result = 'pass' AND status = 'submitted'")->fetchColumn(),
        'certificates' => (int) $db->query('SELECT COUNT(*) FROM certificates')->fetchColumn(),
    ];
}

function recentProjects(int $limit = 5): array
{
    $stmt = getDB()->prepare('
        SELECT id, name, code, status, created_at
        FROM projects
        ORDER BY created_at DESC
        LIMIT ?
    ');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

