<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Exam.php';

function getCertificates(): array
{
    $stmt = getDB()->query('
        SELECT c.*, p.name AS project_name, CONCAT(pt.first_name, " ", pt.last_name) AS participant_name
        FROM certificates c
        JOIN projects p ON p.id = c.project_id
        JOIN participants pt ON pt.id = c.participant_id
        ORDER BY c.created_at DESC
    ');
    return $stmt->fetchAll();
}

function getCertificateByToken(string $token): ?array
{
    $stmt = getDB()->prepare('
        SELECT c.*, p.name AS project_name, p.organizer,
               pt.title, pt.first_name, pt.last_name, pt.organization, pt.position,
               es.percent, es.score, es.total_score
        FROM certificates c
        JOIN projects p ON p.id = c.project_id
        JOIN participants pt ON pt.id = c.participant_id
        JOIN exam_sessions es ON es.id = c.session_id
        WHERE c.verify_token = ?
        LIMIT 1
    ');
    $stmt->execute([$token]);
    $certificate = $stmt->fetch();
    return $certificate ?: null;
}

function getCertificateBySession(int $sessionId): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM certificates WHERE session_id = ? LIMIT 1');
    $stmt->execute([$sessionId]);
    $certificate = $stmt->fetch();
    return $certificate ?: null;
}

function nextCertificateNumber(array $project): string
{
    $prefix = $project['cert_number_prefix'] ?: 'CERT';
    return sprintf('%s-%s-%05d', $prefix, date('Y'), (int) $project['cert_sequence']);
}

function issueCertificateFromSession(int $sessionId, ?int $adminId): array
{
    $existing = getCertificateBySession($sessionId);
    if ($existing) {
        return ['success' => true, 'certificate_id' => (int) $existing['id'], 'message' => 'มีใบเซอร์แล้ว'];
    }

    $session = getExamSession($sessionId);
    if (!$session || $session['status'] !== 'submitted' || $session['result'] !== 'pass') {
        return ['success' => false, 'message' => 'ออกใบเซอร์ได้เฉพาะ session ที่สอบผ่านแล้ว'];
    }

    $project = getProject((int) $session['project_id']);
    if (!$project) {
        return ['success' => false, 'message' => 'ไม่พบโครงการ'];
    }

    $db = getDB();
    $certNumber = nextCertificateNumber($project);
    $verifyToken = generateToken(32);
    $verifyUrl = BASE_URL . '/public/verify.php?token=' . $verifyToken;
    $filePath = 'uploads/certificates/' . $certNumber . '.html';

    try {
        $db->beginTransaction();
        $stmt = $db->prepare('
            INSERT INTO certificates (
                cert_number, participant_id, project_id, session_id, template_id,
                issued_date, issued_by, file_path, verify_token, verify_url
            ) VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)
        ');
        $stmt->execute([
            $certNumber,
            (int) $session['participant_id'],
            (int) $session['project_id'],
            $sessionId,
            (int) ($project['cert_template_id'] ?: 1),
            $adminId,
            $filePath,
            $verifyToken,
            $verifyUrl,
        ]);
        $certificateId = (int) $db->lastInsertId();

        $update = $db->prepare('UPDATE projects SET cert_sequence = cert_sequence + 1 WHERE id = ?');
        $update->execute([(int) $project['id']]);
        $db->commit();

        writeCertificateHtml($verifyToken);
        return ['success' => true, 'certificate_id' => $certificateId, 'message' => 'ออกใบเซอร์สำเร็จ'];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        logError('Issue certificate failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการออกใบเซอร์'];
    }
}

function writeCertificateHtml(string $token): void
{
    $certificate = getCertificateByToken($token);
    if (!$certificate) {
        return;
    }

    if (!is_dir(CERT_UPLOAD_PATH)) {
        mkdir(CERT_UPLOAD_PATH, 0755, true);
    }

    $name = trim(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']);
    $html = '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8"><title>' . e($certificate['cert_number']) . '</title></head><body style="font-family:Sarabun,Arial,sans-serif;text-align:center;padding:64px"><h1>Certificate</h1><p>มอบให้</p><h2>' . e($name) . '</h2><p>สำหรับโครงการ ' . e($certificate['project_name']) . '</p><p>เลขที่ ' . e($certificate['cert_number']) . '</p><p>Verify: ' . e($certificate['verify_url']) . '</p></body></html>';
    file_put_contents(ROOT_PATH . '/' . $certificate['file_path'], $html);
}

