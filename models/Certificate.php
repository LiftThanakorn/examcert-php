<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ExamSession.php';

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

function getCertificateByNumber(string $number): ?array
{
    $stmt = getDB()->prepare('
        SELECT c.*, p.name AS project_name, p.organizer,
               pt.title, pt.first_name, pt.last_name, pt.organization, pt.position,
               es.percent, es.score, es.total_score
        FROM certificates c
        JOIN projects p ON p.id = c.project_id
        JOIN participants pt ON pt.id = c.participant_id
        JOIN exam_sessions es ON es.id = c.session_id
        WHERE c.cert_number = ?
        LIMIT 1
    ');
    $stmt->execute([$number]);
    $certificate = $stmt->fetch();
    return $certificate ?: null;
}

function searchCertificatesByName(string $name): array
{
    $name = '%' . $name . '%';
    $stmt = getDB()->prepare('
        SELECT c.*, p.name AS project_name, p.organizer,
               pt.title, pt.first_name, pt.last_name, pt.organization, pt.position,
               es.percent, es.score, es.total_score
        FROM certificates c
        JOIN projects p ON p.id = c.project_id
        JOIN participants pt ON pt.id = c.participant_id
        JOIN exam_sessions es ON es.id = c.session_id
        WHERE CONCAT(pt.first_name, " ", pt.last_name) LIKE ?
           OR pt.first_name LIKE ?
           OR pt.last_name LIKE ?
        ORDER BY c.issued_date DESC
    ');
    $stmt->execute([$name, $name, $name]);
    return $stmt->fetchAll();
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
        return ['success' => true, 'certificate_id' => (int) $existing['id'], 'message' => 'ออกใบเกียรติบัตรไปแล้ว'];
    }

    $session = getExamSession($sessionId);
    if (!$session || $session['status'] !== 'submitted' || $session['result'] !== 'pass') {
        return ['success' => false, 'message' => 'เฉพาะผู้ที่สอบผ่านเท่านั้นจึงจะออกใบเกียรติบัตรได้'];
    }

    $project = getProject((int) $session['project_id']);
    if (!$project) {
        return ['success' => false, 'message' => 'ไม่พบโครงการสอบ'];
    }

    $db = getDB();

    try {
        $db->beginTransaction();

        $stmtProject = $db->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1 FOR UPDATE');
        $stmtProject->execute([(int) $session['project_id']]);
        $project = $stmtProject->fetch();

        if (!$project) {
            $db->rollBack();
            return ['success' => false, 'message' => 'ไม่พบโครงการสอบ'];
        }

        $certNumber = nextCertificateNumber($project);
        $verifyToken = bin2hex(random_bytes(32));
        $verifyUrl = BASE_URL . '/public/verify.php?token=' . $verifyToken;
        $filePath = 'uploads/certificates/' . preg_replace('/[^A-Za-z0-9._-]/', '-', $certNumber) . '.pdf';

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

        try {
            writeCertificatePdf($verifyToken);
        } catch (Throwable $pdfErr) {
            logError('PDF generation failed (cert still issued)', [
                'cert_number' => $certNumber,
                'error' => $pdfErr->getMessage(),
            ]);
        }
        return ['success' => true, 'certificate_id' => $certificateId, 'message' => 'ออกใบเกียรติบัตรสำเร็จ'];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        logError('Issue certificate failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        return ['success' => false, 'message' => 'ไม่สามารถออกใบเกียรติบัตรได้'];
    }
}

function writeCertificatePdf(string $token): void
{
    $certificate = getCertificateByToken($token);
    if (!$certificate) return;

    $project = getProject((int) $certificate['project_id']);
    $template = getCertificateTemplate((int) ($certificate['template_id'] ?: $project['cert_template_id'] ?: 1));
    if (!$template) return;

    $layout = json_decode((string) ($template['layout_json'] ?? ''), true) ?: [
        "name"   => ["x" => 148.5, "y" => 100, "align" => "C", "size" => 38, "bold" => true],
        "course" => ["x" => 148.5, "y" => 125, "align" => "C", "size" => 22, "bold" => false],
        "date"   => ["x" => 148.5, "y" => 145, "align" => "C", "size" => 16, "bold" => false],
        "certno" => ["x" => 250,   "y" => 185, "align" => "R", "size" => 11, "bold" => false],
        "qrcode" => ["x" => 255,   "y" => 168, "w" => 28, "h" => 28]
    ];

    if (!is_dir(CERT_UPLOAD_PATH)) mkdir(CERT_UPLOAD_PATH, 0755, true);
    $absolutePath = ROOT_PATH . '/' . $certificate['file_path'];
    $activeFont = ($font = strtolower($template['font_name'] ?: 'sarabun')) === 'sarabun' ? 'sarabun' : $font;
    
    $tcpdfFile = ROOT_PATH . '/lib/tcpdf/tcpdf.php';
    if (!is_file($tcpdfFile)) {
        writeMinimalPdf($absolutePath, ["TCPDF library missing"]);
        return;
    }

    require_once $tcpdfFile;
    $pdf = new TCPDF($template['orientation'] ?: 'L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(APP_NAME);
    $pdf->SetTitle($certificate['cert_number']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->AddPage();

    // Background Image
    if ($template['bg_image'] && is_file(ROOT_PATH . '/' . $template['bg_image'])) {
        $pdf->Image(ROOT_PATH . '/' . $template['bg_image'], 0, 0, ($template['orientation'] === 'P' ? 210 : 297), ($template['orientation'] === 'P' ? 297 : 210), '', '', '', false, 300, '', false, false, 0);
    }

    // Font Configuration (Google Fonts: Sarabun)
    $color = $template['color_primary'] ?: '#E87722';
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");

    // Helper to draw text
    $drawText = function($field, $text) use ($pdf, $layout, $activeFont, $r, $g, $b) {
        if (!isset($layout[$field])) return;
        $cfg = $layout[$field];

        $pdf->SetFont($activeFont, ($cfg['bold'] ?? false) ? 'B' : '', $cfg['size'] ?? 20);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Text($cfg['x'], $cfg['y'], $text, false, false, true, 0, 0, $cfg['align'] ?? 'L');
    };

    $fullName = trim(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']);
    
    $drawText('name', $fullName);
    $drawText('course', $certificate['project_name']);
    
    if (!empty($template['show_date'])) {
        $drawText('date', date('d/m/Y', strtotime($certificate['issued_date'])));
    }
    
    $drawText('certno', $certificate['cert_number']);

    // Draw Logo
    if (!empty($template['logo_path']) && isset($layout['logo'])) {
        $l = $layout['logo'];
        $logoFile = ROOT_PATH . '/' . $template['logo_path'];
        if (is_file($logoFile)) {
            $pdf->Image($logoFile, $l['x'], $l['y'], $l['w'] ?? 0, $l['h'] ?? 0);
        }
    }

    // Draw Signatures
    $signatures = json_decode((string)($template['signature_paths'] ?? '[]'), true);
    if (is_array($signatures)) {
        foreach ($signatures as $index => $sign) {
            $key = 'sign' . ($index + 1);
            if (isset($layout[$key]) && !empty($sign['path'])) {
                $s = $layout[$key];
                $signFile = ROOT_PATH . '/' . $sign['path'];
                if (is_file($signFile)) {
                    $pdf->Image($signFile, $s['x'], $s['y'], $s['w'] ?? 40);
                    // Draw Label (Name/Position) below signature
                    if (isset($s['label_y'])) {
                        $pdf->SetFont($activeFont, '', 11);
                        $pdf->SetTextColor(0, 0, 0); 
                        $pdf->Text($s['x'] + (($s['w'] ?? 40) / 2), $s['label_y'], $sign['name'] ?? '', false, false, true, 0, 0, 'C');
                    }
                }
            }
        }
    }

    // QR Code
    if (!empty($template['show_qr']) && isset($layout['qrcode'])) {
        $qrLib = ROOT_PATH . '/lib/phpqrcode/qrlib.php';
        if (is_file($qrLib)) {
            require_once $qrLib;
            $qrPath = CERT_UPLOAD_PATH . '/' . $certificate['verify_token'] . '.png';
            QRcode::png($certificate['verify_url'], $qrPath, QR_ECLEVEL_L, 4);
            $q = $layout['qrcode'];
            $pdf->Image($qrPath, $q['x'], $q['y'], $q['w'], $q['h']);
        }
    }

    $pdf->Output($absolutePath, 'F');
}

function writeMinimalPdf(string $path, array $lines): void
{
    $content = "BT\n/F1 24 Tf\n72 520 Td\n";
    foreach ($lines as $index => $line) {
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
        $content .= $index === 0 ? '(' . $escaped . ") Tj\n" : "0 -36 Td\n(" . $escaped . ") Tj\n";
    }
    $content .= 'ET';
    $objects = [
        '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
        '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
        '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj',
        '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
        '5 0 obj << /Length ' . strlen($content) . " >> stream\n" . $content . "\nendstream endobj",
    ];
    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $object) {
        $offsets[] = strlen($pdf);
        $pdf .= $object . "\n";
    }
    $xref = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
    file_put_contents($path, $pdf);
}

function markCertificateRevoked(int $id, bool $revoked, ?string $reason = null): bool
{
    $stmt = getDB()->prepare('UPDATE certificates SET is_revoked = ?, revoke_reason = ? WHERE id = ?');
    return $stmt->execute([$revoked ? 1 : 0, $revoked ? $reason : null, $id]);
}

function incrementCertificateDownload(int $id): void
{
    $stmt = getDB()->prepare('UPDATE certificates SET download_count = download_count + 1, last_downloaded_at = NOW() WHERE id = ?');
    $stmt->execute([$id]);
}
