<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function projectDefaults(): array
{
    return [
        'name' => '',
        'code' => '',
        'description' => '',
        'organizer' => '',
        'location' => '',
        'start_date' => '',
        'end_date' => '',
        'exam_start' => '',
        'exam_end' => '',
        'pass_score' => '70.00',
        'max_attempts' => '1',
        'time_limit_min' => '60',
        'question_count' => '0',
        'randomize_questions' => '1',
        'randomize_choices' => '1',
        'show_result_immediately' => '1',
        'status' => 'draft',
    ];
}

function normalizeDateTime(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    return str_replace('T', ' ', $value) . (strlen($value) === 16 ? ':00' : '');
}

function normalizeDate(?string $value): ?string
{
    $value = trim((string) $value);
    return $value === '' ? null : $value;
}

function validateProjectInput(array $data): array
{
    $errors = [];

    if (trim((string) ($data['name'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกชื่อโครงการสอบ';
    }

    $passScore = (float) ($data['pass_score'] ?? 0);
    if ($passScore < 0 || $passScore > 100) {
        $errors[] = 'คะแนนผ่านต้องอยู่ระหว่าง 0 ถึง 100';
    }

    if ((int) ($data['max_attempts'] ?? 0) < 1) {
        $errors[] = 'จำนวนครั้งที่สอบได้ต้องอย่างน้อย 1 ครั้ง';
    }

    if ((int) ($data['time_limit_min'] ?? 0) < 1) {
        $errors[] = 'เวลาทำข้อสอบต้องอย่างน้อย 1 นาที';
    }

    if (!in_array($data['status'] ?? 'draft', ['draft', 'active', 'closed'], true)) {
        $errors[] = 'สถานะโครงการไม่ถูกต้อง';
    }

    return $errors;
}

function projectPayload(array $data, ?int $adminId): array
{
    return [
        'name' => trim((string) $data['name']),
        'code' => trim((string) ($data['code'] ?? '')) ?: null,
        'description' => trim((string) ($data['description'] ?? '')) ?: null,
        'organizer' => trim((string) ($data['organizer'] ?? '')) ?: null,
        'location' => trim((string) ($data['location'] ?? '')) ?: null,
        'start_date' => normalizeDate($data['start_date'] ?? null),
        'end_date' => normalizeDate($data['end_date'] ?? null),
        'exam_start' => normalizeDateTime($data['exam_start'] ?? null),
        'exam_end' => normalizeDateTime($data['exam_end'] ?? null),
        'pass_score' => (float) ($data['pass_score'] ?? 70),
        'max_attempts' => max(1, (int) ($data['max_attempts'] ?? 1)),
        'time_limit_min' => max(1, (int) ($data['time_limit_min'] ?? 60)),
        'question_count' => max(0, (int) ($data['question_count'] ?? 0)),
        'randomize_questions' => !empty($data['randomize_questions']) ? 1 : 0,
        'randomize_choices' => !empty($data['randomize_choices']) ? 1 : 0,
        'show_result_immediately' => !empty($data['show_result_immediately']) ? 1 : 0,
        'status' => in_array($data['status'] ?? 'draft', ['draft', 'active', 'closed'], true) ? $data['status'] : 'draft',
        'created_by' => $adminId,
    ];
}

function getAllProjects(): array
{
    $stmt = getDB()->query("
        SELECT p.*,
            (SELECT COUNT(*) FROM participants pp WHERE pp.project_id = p.id) AS participant_count,
            (SELECT COUNT(*) FROM questions q WHERE q.project_id = p.id) AS question_count_total
        FROM projects p
        ORDER BY p.created_at DESC
    ");

    return $stmt->fetchAll();
}

function getProject(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    return $project ?: null;
}

function createProject(array $data, ?int $adminId): array
{
    $errors = validateProjectInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = projectPayload($data, $adminId);

    try {
        $stmt = getDB()->prepare("
            INSERT INTO projects (
                name, code, description, organizer, location, start_date, end_date,
                exam_start, exam_end, pass_score, max_attempts, time_limit_min,
                question_count, randomize_questions, randomize_choices,
                show_result_immediately, status, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $payload['name'], $payload['code'], $payload['description'], $payload['organizer'], $payload['location'],
            $payload['start_date'], $payload['end_date'], $payload['exam_start'], $payload['exam_end'],
            $payload['pass_score'], $payload['max_attempts'], $payload['time_limit_min'], $payload['question_count'],
            $payload['randomize_questions'], $payload['randomize_choices'], $payload['show_result_immediately'],
            $payload['status'], $payload['created_by'],
        ]);

        return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
    } catch (Throwable $e) {
        logError('Create project failed', ['error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการสร้างโครงการสอบ']];
    }
}

function updateProject(int $id, array $data): array
{
    $errors = validateProjectInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = projectPayload($data, null);

    try {
        $stmt = getDB()->prepare("
            UPDATE projects SET
                name = ?, code = ?, description = ?, organizer = ?, location = ?,
                start_date = ?, end_date = ?, exam_start = ?, exam_end = ?,
                pass_score = ?, max_attempts = ?, time_limit_min = ?, question_count = ?,
                randomize_questions = ?, randomize_choices = ?, show_result_immediately = ?,
                status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $payload['name'], $payload['code'], $payload['description'], $payload['organizer'], $payload['location'],
            $payload['start_date'], $payload['end_date'], $payload['exam_start'], $payload['exam_end'],
            $payload['pass_score'], $payload['max_attempts'], $payload['time_limit_min'], $payload['question_count'],
            $payload['randomize_questions'], $payload['randomize_choices'], $payload['show_result_immediately'],
            $payload['status'], $id,
        ]);

        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Update project failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการแก้ไขโครงการสอบ']];
    }
}

function deleteProject(int $id): bool
{
    try {
        $stmt = getDB()->prepare('DELETE FROM projects WHERE id = ?');
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        logError('Delete project failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}

