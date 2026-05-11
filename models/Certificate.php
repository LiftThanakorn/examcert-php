<?php
declare(strict_types=1);

// ── Issue Certificate ────────────────────────────────────────────
function issueCertificateFromSession(int $sessionId, ?int $adminId = null): array {
    $db = getDB();

    // ตรวจว่าออกไปแล้วหรือยัง
    $exist = $db->prepare('SELECT id FROM certificates WHERE session_id=? LIMIT 1');
    $exist->execute([$sessionId]);
    if ($row = $exist->fetch()) {
        return [
            'success' => true, 
            'certificate_id' => (int)$row['id'], 
            'already' => true, 
            'message' => 'เกียรติบัตรนี้เคยออกไปแล้ว'
        ];
    }

    $sess = $db->prepare('SELECT * FROM exam_sessions WHERE id=? AND status="submitted" AND passed=1 LIMIT 1');
    $sess->execute([$sessionId]);
    $session = $sess->fetch();
    if (!$session) {
        return ['success' => false, 'message' => 'ไม่พบข้อมูลการสอบ หรือผู้สอบยังไม่ผ่านเกณฑ์'];
    }

    $db->beginTransaction();
    try {
        // LOCK project row เพื่อป้องกัน cert_number ซ้ำ
        $ps = $db->prepare('SELECT * FROM projects WHERE id=? LIMIT 1 FOR UPDATE');
        $ps->execute([(int)$session['project_id']]);
        $project = $ps->fetch();

        if (!$project) {
            throw new Exception("ไม่พบโครงการที่เกี่ยวข้อง");
        }

        $prefix  = $project['cert_number_prefix'] ?: 'CERT';
        $year    = (int)date('Y') + 543;
        $certNum = sprintf('%s-%d-%05d', $prefix, $year, (int)$project['cert_sequence']);
        $token   = bin2hex(random_bytes(32));

        $db->prepare('INSERT INTO certificates (cert_number, participant_id, project_id, session_id, template_id, issued_date, verify_token)
            VALUES (?, ?, ?, ?, ?, CURDATE(), ?)')
            ->execute([
                $certNum, 
                (int)$session['participant_id'], 
                (int)$session['project_id'], 
                $sessionId, 
                (int)($project['cert_template_id'] ?? 1), 
                $token
            ]);
        $certId = (int)$db->lastInsertId();

        $db->prepare('UPDATE projects SET cert_sequence = cert_sequence + 1 WHERE id = ?')
           ->execute([(int)$project['id']]);

        $db->commit();
        return [
            'success' => true, 
            'certificate_id' => $certId, 
            'verify_token' => $token, 
            'cert_number' => $certNum, 
            'message' => 'ออกเกียรติบัตรเรียบร้อยแล้ว'
        ];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        logError('issueCertificate failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'ไม่สามารถออกเกียรติบัตรได้: ' . $e->getMessage()];
    }
}

// ── Get certificate data (รวม participant + project + template) ──
function getCertificateData(string $verifyToken): ?array {
    $s = getDB()->prepare('
        SELECT c.*, p.title, p.first_name, p.last_name, p.organization,
               pr.name AS project_name, pr.organizer, pr.start_date, pr.end_date,
               es.score, es.percent,
               ct.elements, ct.bg_color, ct.bg_image, ct.bg_type, ct.orientation
        FROM certificates c
        JOIN participants p   ON p.id  = c.participant_id
        JOIN projects pr      ON pr.id = c.project_id
        JOIN exam_sessions es ON es.id = c.session_id
        LEFT JOIN cert_templates ct ON ct.id = c.template_id
        WHERE c.verify_token = ?
        LIMIT 1
    ');
    $s->execute([$verifyToken]);
    return $s->fetch() ?: null;
}

// ── Replace variables ────────────────────────────────────────────
function resolveVars(string $content, array $data): string {
    $months = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    $d      = date_create($data['issued_date'] ?? date('Y-m-d'));
    $issued = $d ? ((int)$d->format('j') . ' ' . $months[(int)$d->format('n')] . ' ' . ((int)$d->format('Y') + 543)) : '—';

    $map = [
        '{{participant_name}}' => trim(($data['title'] ?? '') . ' ' . ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
        '{{first_name}}'       => $data['first_name'] ?? '',
        '{{last_name}}'        => $data['last_name'] ?? '',
        '{{project_name}}'     => $data['project_name'] ?? '',
        '{{organizer}}'        => $data['organizer'] ?? '',
        '{{issued_date}}'      => $issued,
        '{{score}}'            => number_format((float)($data['percent'] ?? 0), 1) . '%',
        '{{cert_number}}'      => $data['cert_number'] ?? '',
        '{{verify_url}}'       => BASE_URL . '/verify.php?t=' . ($data['verify_token'] ?? ''),
    ];
    return str_replace(array_keys($map), array_values($map), (string)$content);
}
function getCertificateByToken(string $token): ?array {
    return getCertificateData($token);
}

function getCertificateByNumber(string $certNumber): ?array {
    $s = getDB()->prepare('SELECT verify_token FROM certificates WHERE cert_number = ? LIMIT 1');
    $s->execute([$certNumber]);
    $row = $s->fetch();
    return $row ? getCertificateData($row['verify_token']) : null;
}

function searchCertificatesByName(string $name): array {
    $name = '%' . $name . '%';
    $s = getDB()->prepare('
        SELECT c.verify_token, c.cert_number, c.issued_date, c.is_revoked,
               p.title, p.first_name, p.last_name, 
               pr.name AS project_name
        FROM certificates c
        JOIN participants p ON p.id = c.participant_id
        JOIN projects pr ON pr.id = c.project_id
        WHERE CONCAT(p.first_name, " ", p.last_name) LIKE ?
           OR p.first_name LIKE ?
           OR p.last_name LIKE ?
        ORDER BY c.issued_date DESC
    ');
    $s->execute([$name, $name, $name]);
    return $s->fetchAll();
}

function getCertificateBySession(int $sessionId): ?array {
    $s = getDB()->prepare('SELECT verify_token FROM certificates WHERE session_id = ? LIMIT 1');
    $s->execute([$sessionId]);
    $row = $s->fetch();
    return $row ? getCertificateData($row['verify_token']) : null;
}

function getCertificates(): array {
    $s = getDB()->query('
        SELECT c.*, p.title, p.first_name, p.last_name, pr.name AS project_name 
        FROM certificates c
        JOIN participants p ON p.id = c.participant_id
        JOIN projects pr ON pr.id = c.project_id
        ORDER BY c.issued_date DESC, c.id DESC
    ');
    $rows = $s->fetchAll();
    foreach ($rows as &$row) {
        $row['participant_name'] = trim(($row['title'] ?? '') . ' ' . $row['first_name'] . ' ' . $row['last_name']);
        $row['verify_url'] = BASE_URL . '/verify?t=' . $row['verify_token'];
    }
    return $rows;
}

function incrementCertificateDownload(int $id): void {
    $s = getDB()->prepare('UPDATE certificates SET download_count = download_count + 1, last_downloaded_at = NOW() WHERE id = ?');
    $s->execute([$id]);
}

function markCertificateRevoked(int $id, bool $revoked, ?string $reason): bool {
    $s = getDB()->prepare('UPDATE certificates SET is_revoked = ?, revoke_reason = ? WHERE id = ?');
    return $s->execute([(int)$revoked, $reason, $id]);
}
