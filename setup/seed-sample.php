<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script is CLI-only.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

$db = getDB();

try {
    $adminId = (int) ($db->query('SELECT id FROM admins ORDER BY id ASC LIMIT 1')->fetchColumn() ?: 0);
    if ($adminId === 0) {
        echo "Create an admin first with setup/create-admin.php\n";
        exit(1);
    }

    $stmt = $db->prepare('
        INSERT INTO projects (
            name, code, description, organizer, exam_start, exam_end, pass_score,
            max_attempts, time_limit_min, question_count, status, created_by
        ) VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 1 DAY), 50, 2, 30, 0, "active", ?)
        ON DUPLICATE KEY UPDATE status = "active", exam_end = DATE_ADD(NOW(), INTERVAL 1 DAY)
    ');
    $stmt->execute(['Local Test Exam', 'TEST-001', 'Sample exam for local testing', 'ExamCert', $adminId]);

    $projectId = (int) $db->query("SELECT id FROM projects WHERE code = 'TEST-001' LIMIT 1")->fetchColumn();

    $token = 'testtoken1234567890testtoken1234567890testtoken1234567890abcd';
    $stmt = $db->prepare('
        INSERT INTO participants (project_id, first_name, last_name, email, access_token, created_by)
        VALUES (?, "Test", "Participant", "test@example.com", ?, ?)
        ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name)
    ');
    $stmt->execute([$projectId, $token, $adminId]);

    $questions = [
        ['2 + 2 = ?', 'multiple_choice', '[{"key":"a","text":"3"},{"key":"b","text":"4"},{"key":"c","text":"5"},{"key":"d","text":"6"}]', 'b'],
        ['PHP สามารถใช้ PDO เชื่อมต่อฐานข้อมูลได้', 'true_false', '[{"key":"true","text":"ถูก"},{"key":"false","text":"ผิด"}]', 'true'],
    ];

    foreach ($questions as $index => $q) {
        $stmt = $db->prepare('
            INSERT INTO questions (project_id, question_text, type, choices, correct_answer, score_weight, difficulty, order_num, is_active, created_by)
            SELECT ?, ?, ?, ?, ?, 1, "easy", ?, 1, ?
            WHERE NOT EXISTS (
                SELECT 1 FROM questions WHERE project_id = ? AND question_text = ?
            )
        ');
        $stmt->execute([$projectId, $q[0], $q[1], $q[2], $q[3], $index + 1, $adminId, $projectId, $q[0]]);
    }

    echo "Sample data ready.\n";
    echo "Project: TEST-001\n";
    echo "Token: {$token}\n";
    echo "Exam URL: " . BASE_URL . "/public/exam.php?project=TEST-001\n";
} catch (Throwable $e) {
    logError('Seed sample failed', ['error' => $e->getMessage()]);
    echo "Seed sample failed.\n";
    exit(1);
}
