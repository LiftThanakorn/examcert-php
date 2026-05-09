<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';

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
        'warning_before' => '5',
        'allow_early_login' => '0',
        'auto_submit_on_close' => '1',
        'manual_override' => '0',
        'cert_template_id' => '',
        'cert_number_prefix' => 'CERT',
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
        $errors[] = 'กรุณากรอกชื่อโครงการ';
    }
    $passScore = (float) ($data['pass_score'] ?? 0);
    if ($passScore < 0 || $passScore > 100) {
        $errors[] = 'คะแนนที่ผ่านต้องอยู่ระหว่าง 0 ถึง 100';
    }
    if ((int) ($data['max_attempts'] ?? 0) < 1) {
        $errors[] = 'จำนวนสิทธิ์การสอบต้องไม่น้อยกว่า 1';
    }
    if ((int) ($data['time_limit_min'] ?? 0) < 1) {
        $errors[] = 'เวลาในการทำข้อสอบต้องไม่น้อยกว่า 1 นาที';
    }
    if ((int) ($data['warning_before'] ?? 0) < 0) {
        $errors[] = 'เวลาแจ้งเตือนต้องไม่น้อยกว่า 0 นาที';
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
        'warning_before' => max(0, (int) ($data['warning_before'] ?? 5)),
        'allow_early_login' => !empty($data['allow_early_login']) ? 1 : 0,
        'auto_submit_on_close' => !empty($data['auto_submit_on_close']) ? 1 : 0,
        'manual_override' => !empty($data['manual_override']) ? 1 : 0,
        'cert_template_id' => (int) ($data['cert_template_id'] ?? 0) ?: null,
        'cert_number_prefix' => trim((string) ($data['cert_number_prefix'] ?? 'CERT')) ?: 'CERT',
        'status' => in_array($data['status'] ?? 'draft', ['draft', 'active', 'closed'], true) ? $data['status'] : 'draft',
        'created_by' => $adminId,
    ];
}

function getAllProjects(): array
{
    $stmt = getDB()->query('
        SELECT p.*,
            (SELECT COUNT(*) FROM participants pp WHERE pp.project_id = p.id) AS participant_count,
            (SELECT COUNT(*) FROM questions q WHERE q.project_id = p.id) AS question_count_total
        FROM projects p
        ORDER BY p.created_at DESC
    ');

    return $stmt->fetchAll();
}

function getProject(int $id): ?array
{
    $stmt = getDB()->prepare('
        SELECT p.*,
            (SELECT COUNT(*) FROM participants pp WHERE pp.project_id = p.id) AS participant_count,
            (SELECT COUNT(*) FROM questions q WHERE q.project_id = p.id) AS question_count_total
        FROM projects p
        WHERE p.id = ? LIMIT 1
    ');
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
        $stmt = getDB()->prepare('
            INSERT INTO projects (
                name, code, description, organizer, location, start_date, end_date,
                exam_start, exam_end, pass_score, max_attempts, time_limit_min,
                question_count, randomize_questions, randomize_choices,
                show_result_immediately, warning_before, allow_early_login,
                auto_submit_on_close, manual_override, cert_template_id,
                cert_number_prefix, status, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $payload['name'], $payload['code'], $payload['description'], $payload['organizer'], $payload['location'],
            $payload['start_date'], $payload['end_date'], $payload['exam_start'], $payload['exam_end'],
            $payload['pass_score'], $payload['max_attempts'], $payload['time_limit_min'], $payload['question_count'],
            $payload['randomize_questions'], $payload['randomize_choices'], $payload['show_result_immediately'],
            $payload['warning_before'], $payload['allow_early_login'], $payload['auto_submit_on_close'],
            $payload['manual_override'], $payload['cert_template_id'], $payload['cert_number_prefix'],
            $payload['status'], $payload['created_by'],
        ]);

        return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
    } catch (Throwable $e) {
        logError('Create project failed', ['error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['ไม่สามารถสร้างโครงการได้']];
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
        $stmt = getDB()->prepare('
            UPDATE projects SET
                name = ?, code = ?, description = ?, organizer = ?, location = ?,
                start_date = ?, end_date = ?, exam_start = ?, exam_end = ?,
                pass_score = ?, max_attempts = ?, time_limit_min = ?, question_count = ?,
                randomize_questions = ?, randomize_choices = ?, show_result_immediately = ?,
                warning_before = ?, allow_early_login = ?, auto_submit_on_close = ?,
                manual_override = ?, cert_template_id = ?, cert_number_prefix = ?,
                status = ?, updated_at = NOW()
            WHERE id = ?
        ');
        $stmt->execute([
            $payload['name'], $payload['code'], $payload['description'], $payload['organizer'], $payload['location'],
            $payload['start_date'], $payload['end_date'], $payload['exam_start'], $payload['exam_end'],
            $payload['pass_score'], $payload['max_attempts'], $payload['time_limit_min'], $payload['question_count'],
            $payload['randomize_questions'], $payload['randomize_choices'], $payload['show_result_immediately'],
            $payload['warning_before'], $payload['allow_early_login'], $payload['auto_submit_on_close'],
            $payload['manual_override'], $payload['cert_template_id'], $payload['cert_number_prefix'],
            $payload['status'], $id,
        ]);

        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Update project failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['ไม่สามารถแก้ไขโครงการได้']];
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

function getProjectRuntimeStatus(array $project, ?DateTimeImmutable $now = null): array
{
    $now = $now ?? new DateTimeImmutable();
    $status = (string) ($project['status'] ?? 'draft');
    $manual = (int) ($project['manual_override'] ?? 0) === 1;

    // 1. Manual Override Check (Highest Priority)
    if ($manual) {
        $allowed = $status === 'active';
        return [
            'allowed' => $allowed,
            'status' => $status,
            'message' => $allowed ? 'เปิดใช้งาน (Manual)' : 'ปิดการเข้าสอบชั่วคราว',
            'seconds_left' => null,
            'warning' => false,
        ];
    }

    // 2. Base Status Check
    if ($status !== 'active') {
        return [
            'allowed' => false,
            'status' => $status,
            'message' => $status === 'closed' ? 'โครงการนี้ปิดการสอบแล้ว' : 'โครงการยังไม่เปิดใช้งาน',
            'seconds_left' => null,
            'warning' => false
        ];
    }

    // 3. Exam Start Time Check
    if (!empty($project['exam_start']) && (int) ($project['allow_early_login'] ?? 0) !== 1) {
        $start = new DateTimeImmutable((string) $project['exam_start']);
        if ($now < $start) {
            return [
                'allowed' => false,
                'status' => 'scheduled',
                'message' => 'ยังไม่ถึงเวลาเปิดสอบ (เริ่ม ' . $start->format('H:i') . ')',
                'seconds_left' => $start->getTimestamp() - $now->getTimestamp(),
                'warning' => false,
            ];
        }
    }

    // 4. Exam End Time Check
    $secondsLeft = null;
    if (!empty($project['exam_end'])) {
        $end = new DateTimeImmutable((string) $project['exam_end']);
        $secondsLeft = $end->getTimestamp() - $now->getTimestamp();
        
        if ($secondsLeft <= 0) {
            return [
                'allowed' => false, 
                'status' => 'closed', 
                'message' => 'หมดเวลาเข้าสอบแล้ว', 
                'seconds_left' => 0, 
                'warning' => false
            ];
        }
    }

    // 5. Warning Logic
    $warningSeconds = max(0, (int) ($project['warning_before'] ?? 5)) * 60;
    return [
        'allowed' => true,
        'status' => 'open',
        'message' => '',
        'seconds_left' => $secondsLeft,
        'warning' => $secondsLeft !== null && $warningSeconds > 0 && $secondsLeft <= $warningSeconds,
    ];
}

function updateProjectSchedule(int $id, array $data): bool
{
    $stmt = getDB()->prepare('
        UPDATE projects
        SET exam_start = ?, exam_end = ?, warning_before = ?, allow_early_login = ?,
            auto_submit_on_close = ?, manual_override = ?, updated_at = NOW()
        WHERE id = ?
    ');

    return $stmt->execute([
        normalizeDateTime($data['exam_start'] ?? null),
        normalizeDateTime($data['exam_end'] ?? null),
        max(0, (int) ($data['warning_before'] ?? 5)),
        !empty($data['allow_early_login']) ? 1 : 0,
        !empty($data['auto_submit_on_close']) ? 1 : 0,
        !empty($data['manual_override']) ? 1 : 0,
        $id,
    ]);
}

function forceProjectStatus(int $id, string $status): bool
{
    if (!in_array($status, ['draft', 'active', 'closed'], true)) {
        return false;
    }

    $stmt = getDB()->prepare('UPDATE projects SET status = ?, manual_override = 1, updated_at = NOW() WHERE id = ?');
    return $stmt->execute([$status, $id]);
}

function extendProjectExamEnd(int $id, int $minutes): bool
{
    $minutes = max(1, min(1440, $minutes));
    $db = getDB();
    
    try {
        $db->beginTransaction();
        
        // 1. Update Project End Time
        $stmt = $db->prepare('
            UPDATE projects 
            SET exam_end = DATE_ADD(COALESCE(exam_end, NOW()), INTERVAL ? MINUTE), 
                updated_at = NOW() 
            WHERE id = ?
        ');
        $stmt->execute([$minutes, $id]);

        // 2. Update all active sessions for this project
        // This ensures students currently taking the exam get the extra time immediately
        $stmtSession = $db->prepare('
            UPDATE exam_sessions 
            SET expires_at = DATE_ADD(expires_at, INTERVAL ? MINUTE) 
            WHERE project_id = ? AND status = "in_progress"
        ');
        $stmtSession->execute([$minutes, $id]);

        $db->commit();
        return true;
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        logError('Extend project failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}

class Project extends BaseModel
{
}
