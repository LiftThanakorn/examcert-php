<?php
declare(strict_types=1);

function templateDefaults(): array
{
    return [
        'name' => '',
        'description' => '',
        'orientation' => 'L',
        'font_name' => 'Sarabun',
        'color_primary' => '#E87722',
        'is_active' => 1,
        'show_score' => 0,
        'show_qr' => 1,
        'show_date' => 1,
        'layout_json' => '',
        'bg_image' => null,
        'logo_path' => null,
        'signature_paths' => '[]',
        'show_name' => 1,
        'show_course' => 1,
        'show_certno' => 1,
    ];
}

function getCertificateTemplates(): array
{
    $stmt = getDB()->query('SELECT * FROM cert_templates ORDER BY is_active DESC, name ASC');
    return $stmt->fetchAll();
}

function getActiveCertificateTemplates(): array
{
    $stmt = getDB()->query('SELECT * FROM cert_templates WHERE is_active = 1 ORDER BY name ASC');
    return $stmt->fetchAll();
}

function getCertificateTemplate(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM cert_templates WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function saveCertificateTemplate(array $data, ?int $adminId, ?int $id = null): array
{
    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        return ['success' => false, 'errors' => ['กรุณากรอกชื่อเทมเพลต']];
    }

    $payload = [
        'name' => $name,
        'description' => trim((string) ($data['description'] ?? '')) ?: null,
        'orientation' => in_array($data['orientation'] ?? 'L', ['L', 'P'], true) ? $data['orientation'] : 'L',
        'font_name' => trim((string) ($data['font_name'] ?? 'Sarabun')) ?: 'Sarabun',
        'show_score' => !empty($data['show_score']) ? 1 : 0,
        'show_qr' => !empty($data['show_qr']) ? 1 : 0,
        'show_date' => !empty($data['show_date']) ? 1 : 0,
        'color_primary' => preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($data['color_primary'] ?? '')) ? $data['color_primary'] : '#E87722',
        'is_active' => !empty($data['is_active']) ? 1 : 0,
        'show_name' => !empty($data['show_name']) ? 1 : 0,
        'show_course' => !empty($data['show_course']) ? 1 : 0,
        'show_certno' => !empty($data['show_certno']) ? 1 : 0,
        'layout_json' => trim((string) ($data['layout_json'] ?? '')) ?: null,
    ];

    // Handle background image
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['tmp_name']) {
        $uploaded = uploadTemplateFile($_FILES['bg_image'], 'bg');
        if ($uploaded === null) {
            return ['success' => false, 'errors' => ['ไฟล์พื้นหลังไม่ถูกต้อง กรุณาอัปโหลด PNG, JPG หรือ WEBP ขนาดไม่เกิน 5MB']];
        }
        $payload['bg_image'] = $uploaded;
    }

    // Handle Logo upload
    if (isset($_FILES['logo_image']) && $_FILES['logo_image']['tmp_name']) {
        $uploaded = uploadTemplateFile($_FILES['logo_image'], 'logo');
        if ($uploaded === null) {
            return ['success' => false, 'errors' => ['ไฟล์โลโก้ไม่ถูกต้อง กรุณาอัปโหลด PNG, JPG หรือ WEBP ขนาดไม่เกิน 5MB']];
        }
        $payload['logo_path'] = $uploaded;
    }

    // Handle Signatures
    $existingSignatures = [];
    if ($id !== null) {
        $tmpl = getCertificateTemplate($id);
        $existingSignatures = json_decode((string)($tmpl['signature_paths'] ?? '[]'), true);
    }
    
    $signatures = [];
    for ($i = 1; $i <= 2; $i++) {
        $signPath = $existingSignatures[$i-1]['path'] ?? null;
        
        // Check for removal
        if (!empty($data['remove_sign_' . $i])) {
            $signPath = null;
        }
        
        // Check for new upload
        if (isset($_FILES['sign_image_' . $i]) && $_FILES['sign_image_' . $i]['tmp_name']) {
            $uploaded = uploadTemplateFile($_FILES['sign_image_' . $i], 'sign' . $i);
            if ($uploaded === null) {
                return ['success' => false, 'errors' => ['ไฟล์ลายเซ็นไม่ถูกต้อง กรุณาอัปโหลด PNG, JPG หรือ WEBP ขนาดไม่เกิน 5MB']];
            }
            $signPath = $uploaded;
        }
        
        $signatures[] = [
            'path' => $signPath,
            'name' => trim((string)($data['sign_name_' . $i] ?? ''))
        ];
    }
    $payload['signature_paths'] = json_encode($signatures, JSON_UNESCAPED_UNICODE);

    try {
        if ($id === null) {
            $stmt = getDB()->prepare('
                INSERT INTO cert_templates (
                    name, description, orientation, font_name, show_score, show_qr,
                    show_date, show_name, show_course, show_certno,
                    color_primary, is_active, layout_json, bg_image, 
                    logo_path, signature_paths, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $payload['name'], $payload['description'], $payload['orientation'], $payload['font_name'],
                $payload['show_score'], $payload['show_qr'], $payload['show_date'],
                $payload['show_name'], $payload['show_course'], $payload['show_certno'],
                $payload['color_primary'], $payload['is_active'], $payload['layout_json'], 
                $payload['bg_image'] ?? null, $payload['logo_path'] ?? null, 
                $payload['signature_paths'], $adminId,
            ]);
            return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
        }

        // For update
        $sql = 'UPDATE cert_templates SET name = ?, description = ?, orientation = ?, font_name = ?, 
                show_score = ?, show_qr = ?, show_date = ?, 
                show_name = ?, show_course = ?, show_certno = ?,
                color_primary = ?, is_active = ?, layout_json = ?, signature_paths = ?';
        $params = [
            $payload['name'], $payload['description'], $payload['orientation'], $payload['font_name'],
            $payload['show_score'], $payload['show_qr'], $payload['show_date'],
            $payload['show_name'], $payload['show_course'], $payload['show_certno'],
            $payload['color_primary'], $payload['is_active'], $payload['layout_json'], $payload['signature_paths']
        ];

        // Background image
        if (!empty($data['remove_bg'])) {
            $sql .= ', bg_image = NULL';
        } elseif (isset($payload['bg_image'])) {
            $sql .= ', bg_image = ?';
            $params[] = $payload['bg_image'];
        }

        // Logo image
        if (!empty($data['remove_logo'])) {
            $sql .= ', logo_path = NULL';
        } elseif (isset($payload['logo_path'])) {
            $sql .= ', logo_path = ?';
            $params[] = $payload['logo_path'];
        }
 
        $sql .= ' WHERE id = ?';
        $params[] = $id;
 
        $stmt = getDB()->prepare($sql);
        $stmt->execute($params);
        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Save certificate template failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['ไม่สามารถบันทึกเทมเพลตได้']];
    }
}

/**
 * Helper to upload template assets
 */
function uploadTemplateFile(array $file, string $prefix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (($file['size'] ?? 0) <= 0 || (int) $file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName) || getimagesize($tmpName) === false) {
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);
    $extensions = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];
    if (!isset($extensions[$mime])) {
        return null;
    }

    if (!is_dir(TEMPLATE_UPLOAD_PATH)) {
        mkdir(TEMPLATE_UPLOAD_PATH, 0755, true);
    }
    $uploadDir = 'uploads/templates';
    $fileName = $prefix . '_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $dest = $uploadDir . '/' . $fileName;
    if (move_uploaded_file($tmpName, ROOT_PATH . '/' . $dest)) {
        return $dest;
    }
    return null;
}

function deleteCertificateTemplate(int $id): bool
{
    try {
        $stmt = getDB()->prepare('DELETE FROM cert_templates WHERE id = ?');
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        logError('Delete certificate template failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}
