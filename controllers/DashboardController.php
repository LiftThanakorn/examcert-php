<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';

function dashboardStats(): array
{
    $db = getDB();
    $today = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01');

    return [
        'projects' => (int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
        'active_projects' => (int) $db->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn(),
        'participants' => (int) $db->query('SELECT COUNT(*) FROM participants')->fetchColumn(),
        'participants_month' => (int) $db->query("SELECT COUNT(*) FROM participants WHERE created_at >= '$firstDayOfMonth'")->fetchColumn(),
        'certificates' => (int) $db->query('SELECT COUNT(*) FROM certificates')->fetchColumn(),
        'certificates_today' => (int) $db->query("SELECT COUNT(*) FROM certificates WHERE issued_date = '$today'")->fetchColumn(),
        'submitted_sessions' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE status IN ('submitted','expired')")->fetchColumn(),
        'passed_sessions' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE result = 'pass' AND status = 'submitted'")->fetchColumn(),
        'failed_sessions' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE result = 'fail' AND status IN ('submitted','expired')")->fetchColumn(),
        'participants_today' => (int) $db->query("SELECT COUNT(*) FROM exam_sessions WHERE started_at >= '$today 00:00:00'")->fetchColumn(),
    ];
}

function getPassRateByProject(int $limit = 5): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT p.name, 
               (SELECT COUNT(*) FROM exam_sessions es WHERE es.project_id = p.id AND es.status IN ('submitted','expired')) as total,
               (SELECT COUNT(*) FROM exam_sessions es WHERE es.project_id = p.id AND es.result = 'pass' AND es.status = 'submitted') as passed
        FROM projects p
        WHERE p.status != 'draft'
        ORDER BY p.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $results = $stmt->fetchAll();
    foreach ($results as &$r) {
        $r['rate'] = $r['total'] > 0 ? round(($r['passed'] / $r['total']) * 100, 1) : 0;
    }
    return $results;
}

function getRecentExamResults(int $limit = 5): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT es.*, p.first_name, p.last_name, pr.name as project_name
        FROM exam_sessions es
        JOIN participants p ON es.participant_id = p.id
        JOIN projects pr ON es.project_id = pr.id
        WHERE es.status IN ('submitted','expired')
        ORDER BY es.submitted_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

class DashboardController
{
    public function index(): void
    {
        requireLogin();

        $stats = dashboardStats();
        $avgPassRate = $stats['submitted_sessions'] > 0 ? round(($stats['passed_sessions'] / $stats['submitted_sessions']) * 100, 1) : 0;
        
        $db = getDB();
        $recentProjects = $db->query("
            SELECT p.*, 
                (SELECT COUNT(*) FROM participants pp WHERE pp.project_id = p.id) as participant_count,
                (SELECT COUNT(*) FROM exam_sessions es WHERE es.project_id = p.id AND es.status IN ('submitted','expired')) as total_exams,
                (SELECT COUNT(*) FROM exam_sessions es WHERE es.project_id = p.id AND es.result = 'pass' AND es.status = 'submitted') as passed_exams
            FROM projects p
            ORDER BY p.created_at DESC
            LIMIT 5
        ")->fetchAll();

        foreach ($recentProjects as &$rp) {
            $rp['pass_rate'] = $rp['total_exams'] > 0 ? round(($rp['passed_exams'] / $rp['total_exams']) * 100, 1) : null;
        }

        $passRateChart = getPassRateByProject(5);
        $recentResults = getRecentExamResults(5);
        
        // Schedule status
        $todaySchedule = $db->query("
            SELECT name, status, exam_start, exam_end, manual_override
            FROM projects
            WHERE status != 'closed' 
            AND (
                (exam_start <= NOW() AND exam_end >= NOW())
                OR (exam_start >= NOW() AND exam_start <= DATE_ADD(NOW(), INTERVAL 7 DAY))
                OR manual_override = 1
            )
            ORDER BY exam_start ASC
            LIMIT 5
        ")->fetchAll();

        $pageTitle = 'แผงควบคุม';
        $breadcrumb = ['Admin', 'Dashboard'];
        $viewFile = VIEWS_PATH . '/dashboard/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }
}
