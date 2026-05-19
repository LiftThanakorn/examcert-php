<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Project.php';

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
            (SELECT COUNT(*) FROM certificates WHERE project_id = p.id) AS certificate_count,
            (SELECT AVG(percent) FROM exam_sessions WHERE project_id = p.id AND status IN ("submitted", "expired")) AS avg_percent
        FROM projects p
        ORDER BY p.created_at DESC
    ');

    return $stmt->fetchAll();
}

function ratingScaleProjectOptions(): array
{
    $stmt = getDB()->query('
        SELECT p.id, p.name, p.code, p.created_at
        FROM projects p
        JOIN questions q ON q.project_id = p.id
        WHERE q.type = "rating_scale"
        GROUP BY p.id, p.name, p.code, p.created_at
        ORDER BY p.created_at DESC
    ');
    return $stmt->fetchAll();
}

function ratingScaleSummaryRows(int $projectId): array
{
    $stmt = getDB()->prepare('
        SELECT
            q.id,
            q.question_text,
            COALESCE(NULLIF(TRIM(q.category), ""), "ทั่วไป") AS category,
            COUNT(es.id) AS response_count,
            AVG(CASE WHEN es.id IS NOT NULL THEN CAST(al.given_answer AS DECIMAL(5,2)) ELSE NULL END) AS average_score,
            SUM(CASE WHEN es.id IS NOT NULL AND al.given_answer = "5" THEN 1 ELSE 0 END) AS score_5,
            SUM(CASE WHEN es.id IS NOT NULL AND al.given_answer = "4" THEN 1 ELSE 0 END) AS score_4,
            SUM(CASE WHEN es.id IS NOT NULL AND al.given_answer = "3" THEN 1 ELSE 0 END) AS score_3,
            SUM(CASE WHEN es.id IS NOT NULL AND al.given_answer = "2" THEN 1 ELSE 0 END) AS score_2,
            SUM(CASE WHEN es.id IS NOT NULL AND al.given_answer = "1" THEN 1 ELSE 0 END) AS score_1
        FROM questions q
        LEFT JOIN answer_logs al ON al.question_id = q.id AND al.given_answer REGEXP "^[1-5]$"
        LEFT JOIN exam_sessions es ON es.id = al.session_id AND es.project_id = q.project_id AND es.status IN ("submitted", "expired")
        WHERE q.project_id = ? AND q.type = "rating_scale"
        GROUP BY q.id, q.question_text, category
        ORDER BY category ASC, q.order_num ASC, q.id ASC
    ');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

function ratingScaleCategoryRows(int $projectId): array
{
    $stmt = getDB()->prepare('
        SELECT
            category,
            COUNT(*) AS response_count,
            AVG(answer_value) AS average_score
        FROM (
            SELECT
                COALESCE(NULLIF(TRIM(q.category), ""), "ทั่วไป") AS category,
                CAST(al.given_answer AS DECIMAL(5,2)) AS answer_value
            FROM answer_logs al
            JOIN questions q ON q.id = al.question_id
            JOIN exam_sessions es ON es.id = al.session_id
            WHERE es.project_id = ?
              AND es.status IN ("submitted", "expired")
              AND q.type = "rating_scale"
              AND al.given_answer REGEXP "^[1-5]$"
        ) rating_answers
        GROUP BY category
        ORDER BY category ASC
    ');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

function ratingScaleResponseRows(int $projectId): array
{
    $stmt = getDB()->prepare('
        SELECT
            es.id AS session_id,
            es.submitted_at,
            CONCAT(COALESCE(pt.title, ""), pt.first_name, " ", pt.last_name) AS participant_name,
            pt.organization,
            q.id AS question_id,
            q.question_text,
            COALESCE(NULLIF(TRIM(q.category), ""), "ทั่วไป") AS category,
            al.given_answer,
            al.score_earned
        FROM answer_logs al
        JOIN questions q ON q.id = al.question_id
        JOIN exam_sessions es ON es.id = al.session_id
        JOIN participants pt ON pt.id = es.participant_id
        WHERE es.project_id = ?
          AND es.status IN ("submitted", "expired")
          AND q.type = "rating_scale"
        ORDER BY es.submitted_at DESC, es.id DESC, q.order_num ASC, q.id ASC
    ');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

function ratingScaleResponseMatrixRows(array $responseRows): array
{
    $matrix = [];

    foreach ($responseRows as $row) {
        $sessionId = (int) $row['session_id'];
        if (!isset($matrix[$sessionId])) {
            $matrix[$sessionId] = [
                'session_id' => $sessionId,
                'submitted_at' => $row['submitted_at'],
                'participant_name' => $row['participant_name'],
                'organization' => $row['organization'],
                'answers' => [],
            ];
        }

        $matrix[$sessionId]['answers'][(int) $row['question_id']] = $row['given_answer'];
    }

    return array_values($matrix);
}

function ratingScaleMeaning(float $average): string
{
    if ($average >= 4.51) return 'มากที่สุด';
    if ($average >= 3.51) return 'มาก';
    if ($average >= 2.51) return 'ปานกลาง';
    if ($average >= 1.51) return 'น้อย';
    return 'น้อยที่สุด';
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
        fputcsv($out, ['โครงการ', 'รหัส', 'สถานะ', 'ผู้มีสิทธิ์', 'จำนวนข้อสอบ', 'ส่งแล้ว', 'กำลังสอบ', 'สอบผ่าน', 'ใบเกียรติบัตร', 'คะแนนเฉลี่ย', 'อัตราการผ่าน']);

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
                (int) $row['certificate_count'],
                $row['avg_percent'] !== null ? round((float) $row['avg_percent'], 2) : '',
                $passRate . '%',
            ]);
        }
    }

    public function ratingScale(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $projectOptions = ratingScaleProjectOptions();
        if ($projectId <= 0 && $projectOptions) {
            $projectId = (int) $projectOptions[0]['id'];
        }

        $project = $projectId > 0 ? getProject($projectId) : null;
        $summaryRows = $project ? ratingScaleSummaryRows($projectId) : [];
        $categoryRows = $project ? ratingScaleCategoryRows($projectId) : [];
        $responseRows = $project ? ratingScaleResponseRows($projectId) : [];
        $responseMatrixRows = $project ? ratingScaleResponseMatrixRows($responseRows) : [];

        $pageTitle = 'Rating Scale Report';
        $breadcrumb = ['Dashboard', 'Reports', 'Rating Scale'];
        $viewFile = VIEWS_PATH . '/reports/rating_scale.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function ratingScaleExport(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = $projectId > 0 ? getProject($projectId) : null;
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $summaryRows = ratingScaleSummaryRows($projectId);
        $responseRows = ratingScaleResponseRows($projectId);
        $responseMatrixRows = ratingScaleResponseMatrixRows($responseRows);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rating-scale-project-' . $projectId . '.csv"');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            http_response_code(500);
            exit('Unable to open output stream.');
        }

        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['Summary']);
        fputcsv($out, ['Category', 'Question', 'Responses', 'Average', 'Meaning', '5', '4', '3', '2', '1']);
        foreach ($summaryRows as $row) {
            $avg = $row['average_score'] !== null ? round((float) $row['average_score'], 2) : 0.0;
            fputcsv($out, [
                $row['category'],
                $row['question_text'],
                (int) $row['response_count'],
                $avg,
                $avg > 0 ? ratingScaleMeaning($avg) : '',
                (int) $row['score_5'],
                (int) $row['score_4'],
                (int) $row['score_3'],
                (int) $row['score_2'],
                (int) $row['score_1'],
            ]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Responses by Participant']);

        $headers = ['Session ID', 'Submitted At', 'Participant', 'Organization'];
        foreach ($summaryRows as $row) {
            $headers[] = $row['question_text'];
        }
        fputcsv($out, $headers);

        foreach ($responseMatrixRows as $row) {
            $csvRow = [
                $row['session_id'],
                $row['submitted_at'],
                $row['participant_name'],
                $row['organization'],
            ];

            foreach ($summaryRows as $question) {
                $questionId = (int) $question['id'];
                $csvRow[] = $row['answers'][$questionId] ?? '';
            }

            fputcsv($out, $csvRow);
        }
    }
}
