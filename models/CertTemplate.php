<?php
declare(strict_types=1);

function templateDefaults(): array
{
    return [
        'name' => '',
        'orientation' => 'L',
        'width_mm' => '297.00',
        'height_mm' => '210.00',
        'bg_type' => 'color',
        'bg_color' => '#FFFFFF',
        'bg_image' => '',
        'elements' => '[]',
        'is_active' => 1,
    ];
}

function getCertificateTemplates(): array
{
    ensureCertificateTemplateColumns();
    $stmt = getDB()->query('SELECT * FROM cert_templates ORDER BY is_active DESC, name ASC');
    return $stmt->fetchAll();
}

function getActiveCertificateTemplates(): array
{
    ensureCertificateTemplateColumns();
    $stmt = getDB()->query('SELECT * FROM cert_templates WHERE is_active = 1 ORDER BY name ASC');
    return $stmt->fetchAll();
}

function getCertificateTemplate(int $id): ?array
{
    ensureCertificateTemplateColumns();
    $stmt = getDB()->prepare('SELECT * FROM cert_templates WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function saveCertificateTemplate(array $data, ?int $adminId = null, ?int $id = null): array
{
    ensureCertificateTemplateColumns();

    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        return ['success' => false, 'errors' => ['กรุณากรอกชื่อเทมเพลต']];
    }

    $orientation = in_array(($data['orientation'] ?? 'L'), ['L', 'P'], true) ? (string) $data['orientation'] : 'L';
    $width = $orientation === 'P' ? 210.00 : 297.00;
    $height = $orientation === 'P' ? 297.00 : 210.00;
    $bgColor = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($data['bg_color'] ?? '')) ? strtoupper((string) $data['bg_color']) : '#FFFFFF';
    $bgImage = trim((string) ($data['bg_image'] ?? ''));
    $bgType = $bgImage !== '' ? 'image' : 'color';
    $elements = normalizeTemplateElements($data['elements'] ?? '[]');

    try {
        if ($id === null || $id <= 0) {
            $stmt = getDB()->prepare('
                INSERT INTO cert_templates (
                    name, orientation, width_mm, height_mm, bg_type,
                    bg_color, bg_image, elements, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $name,
                $orientation,
                $width,
                $height,
                $bgType,
                $bgColor,
                $bgImage !== '' ? $bgImage : null,
                $elements,
                !empty($data['is_active']) ? 1 : 1,
            ]);

            return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
        }

        $stmt = getDB()->prepare('
            UPDATE cert_templates
            SET name = ?, orientation = ?, width_mm = ?, height_mm = ?,
                bg_type = ?, bg_color = ?, bg_image = ?, elements = ?, is_active = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $name,
            $orientation,
            $width,
            $height,
            $bgType,
            $bgColor,
            $bgImage !== '' ? $bgImage : null,
            $elements,
            !empty($data['is_active']) ? 1 : 1,
            $id,
        ]);

        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Save certificate template failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['ไม่สามารถบันทึกเทมเพลตได้']];
    }
}

function normalizeTemplateElements(mixed $elements): string
{
    if (is_array($elements)) {
        return json_encode(array_values($elements), JSON_UNESCAPED_UNICODE);
    }

    $raw = trim((string) $elements);
    if ($raw === '') {
        return '[]';
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return '[]';
    }

    return json_encode(array_values($decoded), JSON_UNESCAPED_UNICODE);
}

function uploadTemplateFile(array $file, string $prefix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        logError('Template upload failed: upload error', ['prefix' => $prefix, 'error' => $file['error'] ?? null]);
        return null;
    }

    if (($file['size'] ?? 0) <= 0 || (int) $file['size'] > 5 * 1024 * 1024) {
        logError('Template upload failed: invalid size', ['prefix' => $prefix, 'size' => $file['size'] ?? null]);
        return null;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName) || getimagesize($tmpName) === false) {
        logError('Template upload failed: invalid temporary image', ['prefix' => $prefix, 'tmp_name' => $tmpName]);
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName);
    $extensions = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];
    if (!isset($extensions[$mime])) {
        logError('Template upload failed: unsupported MIME type', ['prefix' => $prefix, 'mime' => $mime]);
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

    logError('Template upload failed: move_uploaded_file failed', ['prefix' => $prefix, 'destination' => $dest]);
    return null;
}

function deleteCertificateTemplate(int $id): bool
{
    try {
        ensureCertificateTemplateColumns();
        $stmt = getDB()->prepare('DELETE FROM cert_templates WHERE id = ?');
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        logError('Delete certificate template failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}

function ensureCertificateTemplateColumns(): void
{
    static $checked = false;
    if ($checked) {
        return;
    }

    $stmt = getDB()->query('SHOW COLUMNS FROM cert_templates');
    $columns = array_map(static fn(array $row): string => (string) $row['Field'], $stmt->fetchAll());
    $required = [
        'width_mm' => 'ALTER TABLE cert_templates ADD COLUMN width_mm DECIMAL(6,2) DEFAULT 297.00 AFTER orientation',
        'height_mm' => 'ALTER TABLE cert_templates ADD COLUMN height_mm DECIMAL(6,2) DEFAULT 210.00 AFTER width_mm',
        'bg_type' => "ALTER TABLE cert_templates ADD COLUMN bg_type ENUM('color','image') DEFAULT 'color' AFTER height_mm",
        'bg_color' => "ALTER TABLE cert_templates ADD COLUMN bg_color VARCHAR(7) DEFAULT '#FFFFFF' AFTER bg_type",
        'elements' => "ALTER TABLE cert_templates ADD COLUMN elements JSON COMMENT 'array of {id,type,x,y,w,h,content,style}' AFTER bg_image",
        'updated_at' => 'ALTER TABLE cert_templates ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    ];

    foreach ($required as $column => $sql) {
        if (!in_array($column, $columns, true)) {
            getDB()->exec($sql);
        }
    }

    $checked = true;
}
