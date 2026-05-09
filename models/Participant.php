<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';

function participantDefaults(): array
{
    return [
        'title' => '',
        'first_name' => '',
        'last_name' => '',
        'organization' => '',
        'position' => '',
        'email' => '',
        'phone' => '',
        'id_card' => '',
        'note' => '',
        'import_batch' => '',
    ];
}

function validateParticipantInput(array $data): array
{
    $errors = [];

    if (trim((string) ($data['first_name'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกชื่อ';
    }

    if (trim((string) ($data['last_name'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกนามสกุล';
    }

    $email = trim((string) ($data['email'] ?? ''));
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
    }

    $idCard = trim((string) ($data['id_card'] ?? ''));
    if ($idCard !== '' && !preg_match('/^[0-9]{13}$/', $idCard)) {
        $errors[] = 'เลขบัตรประชาชนต้องเป็นตัวเลข 13 หลัก';
    }

    return $errors;
}

function participantPayload(array $data): array
{
    return [
        'title' => trim((string) ($data['title'] ?? '')) ?: null,
        'first_name' => trim((string) $data['first_name']),
        'last_name' => trim((string) $data['last_name']),
        'organization' => trim((string) ($data['organization'] ?? '')) ?: null,
        'position' => trim((string) ($data['position'] ?? '')) ?: null,
        'email' => trim((string) ($data['email'] ?? '')) ?: null,
        'phone' => trim((string) ($data['phone'] ?? '')) ?: null,
        'id_card' => trim((string) ($data['id_card'] ?? '')) ?: null,
        'note' => trim((string) ($data['note'] ?? '')) ?: null,
        'import_batch' => trim((string) ($data['import_batch'] ?? '')) ?: null,
    ];
}

function getParticipantsByProject(int $projectId): array
{
    $stmt = getDB()->prepare('
        SELECT *
        FROM participants
        WHERE project_id = ?
        ORDER BY created_at DESC, first_name ASC, last_name ASC
    ');
    $stmt->execute([$projectId]);

    return $stmt->fetchAll();
}

function getParticipant(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM participants WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $participant = $stmt->fetch();

    return $participant ?: null;
}

function createParticipant(int $projectId, array $data, ?int $adminId): array
{
    $errors = validateParticipantInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = participantPayload($data);

    try {
        $duplicate = findDuplicateParticipant($projectId, $payload);
        if ($duplicate) {
            return ['success' => false, 'errors' => ['พบรายชื่อหรือข้อมูลอ้างอิงซ้ำในโครงการนี้']];
        }

        $stmt = getDB()->prepare('
            INSERT INTO participants (
                project_id, title, first_name, last_name, organization, position,
                email, phone, id_card, access_token, note, import_batch, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $projectId,
            $payload['title'],
            $payload['first_name'],
            $payload['last_name'],
            $payload['organization'],
            $payload['position'],
            $payload['email'],
            $payload['phone'],
            $payload['id_card'],
            generateToken(32),
            $payload['note'],
            $payload['import_batch'],
            $adminId,
        ]);

        return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
    } catch (Throwable $e) {
        logError('Create participant failed', ['project_id' => $projectId, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการเพิ่มผู้มีสิทธิ์สอบ']];
    }
}

function updateParticipant(int $id, array $data): array
{
    $errors = validateParticipantInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = participantPayload($data);

    try {
        $participant = getParticipant($id);
        if (!$participant) {
            return ['success' => false, 'errors' => ['ไม่พบรายชื่อผู้มีสิทธิ์สอบ']];
        }

        $duplicate = findDuplicateParticipant((int) $participant['project_id'], $payload, $id);
        if ($duplicate) {
            return ['success' => false, 'errors' => ['พบรายชื่อหรือข้อมูลอ้างอิงซ้ำในโครงการนี้']];
        }

        $stmt = getDB()->prepare('
            UPDATE participants SET
                title = ?, first_name = ?, last_name = ?, organization = ?, position = ?,
                email = ?, phone = ?, id_card = ?, note = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $payload['title'],
            $payload['first_name'],
            $payload['last_name'],
            $payload['organization'],
            $payload['position'],
            $payload['email'],
            $payload['phone'],
            $payload['id_card'],
            $payload['note'],
            $id,
        ]);

        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Update participant failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการแก้ไขผู้มีสิทธิ์สอบ']];
    }
}

function findDuplicateParticipant(int $projectId, array $payload, ?int $excludeId = null): ?array
{
    $conditions = ['project_id = ?'];
    $params = [$projectId];
    $duplicateRules = [];

    // Always check name/lastname combination
    $duplicateRules[] = '(first_name = ? AND last_name = ?)';
    $params[] = $payload['first_name'];
    $params[] = $payload['last_name'];
 
    if (!empty($payload['email'])) {
        $duplicateRules[] = 'email = ?';
        $params[] = $payload['email'];
    }
    if (!empty($payload['id_card'])) {
        $duplicateRules[] = 'id_card = ?';
        $params[] = $payload['id_card'];
    }
 
    $conditions[] = '(' . implode(' OR ', $duplicateRules) . ')';
    if ($excludeId !== null) {
        $conditions[] = 'id <> ?';
        $params[] = $excludeId;
    }

    $stmt = getDB()->prepare('SELECT * FROM participants WHERE ' . implode(' AND ', $conditions) . ' LIMIT 1');
    $stmt->execute($params);
    $row = $stmt->fetch();

    return $row ?: null;
}

function deleteParticipant(int $id): bool
{
    try {
        $stmt = getDB()->prepare('DELETE FROM participants WHERE id = ?');
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        logError('Delete participant failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}

class Participant extends BaseModel
{
}
