<?php
declare(strict_types=1);

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

function generateParticipantAccessToken(): string
{
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM participants WHERE access_token = ? LIMIT 1');

    for ($attempt = 0; $attempt < 50; $attempt++) {
        $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt->execute([$token]);

        if (!$stmt->fetch()) {
            return $token;
        }
    }

    throw new RuntimeException('Unable to generate a unique participant access token.');
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

function searchParticipantsByName(int $projectId, string $term, int $limit = 10): array
{
    $term = trim($term);
    if ($projectId <= 0 || textLength($term) < 2) {
        return [];
    }

    $like = '%' . $term . '%';
    $stmt = getDB()->prepare('
        SELECT id, title, first_name, last_name
        FROM participants
        WHERE project_id = ?
        AND (
            first_name LIKE ?
            OR last_name LIKE ?
            OR CONCAT(first_name, " ", last_name) LIKE ?
            OR CONCAT(COALESCE(title, ""), " ", first_name, " ", last_name) LIKE ?
        )
        ORDER BY first_name ASC, last_name ASC
        LIMIT ?
    ');
    $stmt->bindValue(1, $projectId, PDO::PARAM_INT);
    $stmt->bindValue(2, $like, PDO::PARAM_STR);
    $stmt->bindValue(3, $like, PDO::PARAM_STR);
    $stmt->bindValue(4, $like, PDO::PARAM_STR);
    $stmt->bindValue(5, $like, PDO::PARAM_STR);
    $stmt->bindValue(6, max(1, min(20, $limit)), PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function getParticipant(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM participants WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $participant = $stmt->fetch();

    return $participant ?: null;
}

function getParticipantByAuth(int $projectId, string $firstName, string $lastName, string $token): ?array
{
    $stmt = getDB()->prepare('
        SELECT * FROM participants 
        WHERE project_id = ? 
        AND first_name = ? 
        AND last_name = ? 
        AND access_token = ? 
        LIMIT 1
    ');
    $stmt->execute([$projectId, $firstName, $lastName, $token]);
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
            $reason = duplicateParticipantMessage($duplicate);
            return ['success' => false, 'errors' => [$reason]];
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
            generateParticipantAccessToken(),
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

function importParticipants(int $projectId, array $rows, int $adminId): array
{
    $created  = 0;
    $skipped  = 0;
    $results  = [];
    $batch    = 'IMPORT-' . date('Ymd-His');
    $db       = getDB();


    try {
        $db->beginTransaction();

        foreach ($rows as $index => $row) {
            $rowNum    = $index + 1;
            $title     = trim((string) ($row['คำนำหน้า'] ?? $row['title'] ?? $row[0] ?? ''));
            $firstName = trim((string) ($row['ชื่อ'] ?? $row['first_name'] ?? $row[1] ?? ''));
            $lastName  = trim((string) ($row['นามสกุล'] ?? $row['last_name'] ?? $row[2] ?? ''));
            $email     = trim((string) ($row['อีเมล'] ?? $row['email'] ?? $row[3] ?? ''));

            if ($firstName === '' || $lastName === '') {
                $skipped++;
                $results[] = ['row' => $rowNum, 'status' => 'skipped', 'message' => 'ชื่อหรือนามสกุลว่าง'];
                continue;
            }

            $savepoint = 'participant_import_row_' . $rowNum;
            $db->exec('SAVEPOINT ' . $savepoint);

            try {
                $stmt = $db->prepare('
                    INSERT INTO participants (
                        project_id, title, first_name, last_name, email,
                        organization, position, phone, id_card, note,
                        access_token, import_batch, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ');

                $stmt->execute([
                    $projectId, $title ?: null, $firstName, $lastName, $email ?: null,
                    trim((string) ($row['องค์กร'] ?? $row['organization'] ?? $row[4] ?? '')) ?: null,
                    trim((string) ($row['ตำแหน่ง'] ?? $row['position'] ?? $row[5] ?? '')) ?: null,
                    trim((string) ($row['โทรศัพท์'] ?? $row['phone'] ?? $row[6] ?? '')) ?: null,
                    trim((string) ($row['เลขบัตรประชาชน'] ?? $row['id_card'] ?? $row[7] ?? '')) ?: null,
                    trim((string) ($row['หมายเหตุ'] ?? $row['note'] ?? $row[8] ?? '')) ?: null,
                    generateParticipantAccessToken(),
                    $batch,
                    $adminId,
                ]);

                $db->exec('RELEASE SAVEPOINT ' . $savepoint);

                $created++;
                $results[] = ['row' => $rowNum, 'status' => 'created', 'message' => 'สำเร็จ'];
            } catch (Throwable $rowErr) {
                $db->exec('ROLLBACK TO SAVEPOINT ' . $savepoint);
                $db->exec('RELEASE SAVEPOINT ' . $savepoint);
                $skipped++;
                $results[] = ['row' => $rowNum, 'status' => 'error', 'message' => 'ซ้ำหรือข้อมูลไม่ถูกต้อง'];
            }
        }

        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        logError('Import participants failed', ['project_id' => $projectId, 'error' => $e->getMessage()]);
        return ['success' => false, 'created' => 0, 'skipped' => count($rows), 'batch' => $batch, 'rows' => []];
    }

    return [
        'success' => true,
        'created' => $created,
        'skipped' => $skipped,
        'batch' => $batch,
        'rows' => $results
    ];
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
            $reason = duplicateParticipantMessage($duplicate);
            return ['success' => false, 'errors' => [$reason]];
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

function duplicateParticipantMessage(array $duplicate): string
{
    return match ($duplicate['reason'] ?? '') {
        'email'   => 'อีเมลนี้ถูกใช้งานแล้วในโครงการนี้',
        'id_card' => 'เลขบัตรประชาชนนี้ถูกใช้งานแล้วในโครงการนี้',
        default   => 'พบชื่อ-นามสกุลซ้ำในโครงการนี้',
    };
}

function findDuplicateParticipant(int $projectId, array $payload, ?int $excludeId = null): ?array
{
    $db = getDB();
    $excludeSql = $excludeId !== null ? ' AND id <> ?' : '';

    $params = [$projectId, $payload['first_name'], $payload['last_name']];
    if ($excludeId !== null) {
        $params[] = $excludeId;
    }

    $stmt = $db->prepare("
        SELECT id FROM participants
        WHERE project_id = ?
          AND first_name = ?
          AND last_name = ?
          {$excludeSql}
        LIMIT 1
    ");
    $stmt->execute($params);
    if ($stmt->fetch()) {
        return ['reason' => 'name'];
    }

    if (!empty($payload['email'])) {
        $emailParams = [$projectId, $payload['email']];
        if ($excludeId !== null) {
            $emailParams[] = $excludeId;
        }
        $stmt = $db->prepare("
            SELECT id FROM participants
            WHERE project_id = ?
              AND email = ?
              {$excludeSql}
            LIMIT 1
        ");
        $stmt->execute($emailParams);
        if ($stmt->fetch()) {
            return ['reason' => 'email'];
        }
    }

    if (!empty($payload['id_card'])) {
        $idParams = [$projectId, $payload['id_card']];
        if ($excludeId !== null) {
            $idParams[] = $excludeId;
        }
        $stmt = $db->prepare("
            SELECT id FROM participants
            WHERE project_id = ?
              AND id_card = ?
              {$excludeSql}
            LIMIT 1
        ");
        $stmt->execute($idParams);
        if ($stmt->fetch()) {
            return ['reason' => 'id_card'];
        }
    }

    return null;
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
