<?php
define('ROOT_PATH', __DIR__);
require_once 'config/config.php';
require_once 'models/BaseModel.php';

$projectCode = 'RERU-2569-001';

echo "--- Diagnostic for Project: $projectCode ---\n";

$db = getDB();
$stmt = $db->prepare('SELECT id, name, status FROM projects WHERE code = ?');
$stmt->execute([$projectCode]);
$project = $stmt->fetch();

if (!$project) {
    echo "ERROR: Project not found!\n";
    exit;
}

$projectId = (int)$project['id'];
echo "Project Found: ID=$projectId, Name={$project['name']}, Status={$project['status']}\n";

$stmt = $db->prepare('SELECT COUNT(*) FROM questions WHERE project_id = ?');
$stmt->execute([$projectId]);
$totalQuestions = $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) FROM questions WHERE project_id = ? AND is_active = 1');
$stmt->execute([$projectId]);
$activeQuestions = $stmt->fetchColumn();

echo "Total Questions in DB: $totalQuestions\n";
echo "Active Questions (is_active=1): $activeQuestions\n";

if ($totalQuestions > 0 && $activeQuestions == 0) {
    echo "HINT: Questions exist but they are NOT ACTIVE (is_active=0). Please check the 'is_active' column in the questions table.\n";
}

$stmt = $db->prepare('SELECT id, question_text, is_active FROM questions WHERE project_id = ? LIMIT 5');
$stmt->execute([$projectId]);
$rows = $stmt->fetchAll();

echo "Sample Questions:\n";
foreach ($rows as $row) {
    echo "- ID: {$row['id']}, Active: {$row['is_active']}, Text: " . mb_substr($row['question_text'], 0, 50) . "...\n";
}
