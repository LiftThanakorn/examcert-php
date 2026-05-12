<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Certificate.php';
require_once ROOT_PATH . '/models/CertTemplate.php';

class CertificateController
{
    public function index(): void
    {
        requireLogin();

        $certificates = getCertificates();
        $pageTitle = 'จัดการใบเกียรติบัตร';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร'];
        $viewFile = VIEWS_PATH . '/certificates/index.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function issue(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Bad request.');
        }

        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $result = issueCertificateFromSession($sessionId, currentAdminId());
        setFlash($result['success'] ? 'success' : 'error', $result['message']);

        redirect('admin/certificates/');
    }

    public function download(): void
    {
        requireLogin();
        $this->exportPDF(trim((string) ($_GET['token'] ?? '')));
    }

    public function exportRoute(): void
    {
        $this->exportPDF(trim((string) ($_GET['token'] ?? $_GET['t'] ?? '')));
    }

    public function exportPDF(string $verifyToken): void
    {
        require_once ROOT_PATH . '/lib/tcpdf_helper.php';

        $data = $verifyToken !== '' ? getCertificateData($verifyToken) : null;
        if (!$data || (int) ($data['is_revoked'] ?? 0) === 1) {
            http_response_code(404);
            echo 'ไม่พบเกียรติบัตรหรือถูกเพิกถอนแล้ว';
            exit;
        }

        $orientation = in_array(($data['orientation'] ?? 'L'), ['L', 'P'], true) ? (string) $data['orientation'] : 'L';
        $elements = json_decode((string) ($data['elements'] ?? '[]'), true);
        $elements = is_array($elements) ? $elements : [];

        $pdf = new ExamCertPDF($orientation, 'mm', 'A4');
        $pdf->AddPage();
        renderCertificateBackground($pdf, $data, $orientation);
        renderCertificateElements($pdf, $elements, $data);

        incrementCertificateDownload((int) $data['id']);

        $filename = 'Certificate_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $data['cert_number']) . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    public function previewPDFRoute(): void
    {
        requireLogin();
        $this->previewPDF((int) ($_GET['template_id'] ?? 0));
    }

    public function previewPDF(int $templateId): void
    {
        require_once ROOT_PATH . '/lib/tcpdf_helper.php';

        $template = $templateId > 0 ? getCertificateTemplate($templateId) : null;
        if (!$template) {
            http_response_code(404);
            echo 'ไม่พบเทมเพลต';
            exit;
        }

        $elements = json_decode((string) ($template['elements'] ?? '[]'), true);
        $elements = is_array($elements) ? $elements : [];
        $orientation = in_array(($template['orientation'] ?? 'L'), ['L', 'P'], true) ? (string) $template['orientation'] : 'L';

        $mockData = [
            'title' => 'นาย',
            'first_name' => 'สมชาย',
            'last_name' => 'ใจดี',
            'project_name' => 'อบรม AI เบื้องต้น รุ่น 1',
            'organizer' => 'สถาบัน ExamCert',
            'issued_date' => date('Y-m-d'),
            'percent' => '88.0',
            'cert_number' => 'CERT-' . ((int) date('Y') + 543) . '-00001',
            'verify_token' => 'preview_token',
            'bg_color' => $template['bg_color'] ?? '#FFFFFF',
            'bg_image' => $template['bg_image'] ?? '',
            'bg_type' => $template['bg_type'] ?? 'color',
        ];

        $pdf = new ExamCertPDF($orientation, 'mm', 'A4');
        $pdf->AddPage();
        renderCertificateBackground($pdf, $mockData, $orientation);
        renderCertificateElements($pdf, $elements, $mockData);
        $pdf->Output('preview.pdf', 'I');
        exit;
    }

    public function templates(): void
    {
        requireLogin();
        $templates = getCertificateTemplates();
        $templateMode = 'list';
        $pageTitle = 'จัดการเทมเพลต';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร', 'เทมเพลต'];
        $viewFile = VIEWS_PATH . '/certificates/templates.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function templateCreate(): void
    {
        requireLogin();
        require VIEWS_PATH . '/certificates/template_builder.php';
    }

    public function templateEdit(): void
    {
        requireLogin();
        require VIEWS_PATH . '/certificates/template_builder.php';
    }

    public function templateDelete(): void
    {
        requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $ok = deleteCertificateTemplate($id);
            setFlash($ok ? 'success' : 'error', $ok ? 'ลบเทมเพลตเรียบร้อยแล้ว' : 'ไม่สามารถลบเทมเพลตได้');
        }
        redirect('admin/certificates/templates');
    }

    public function revoke(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Bad request.';
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $revoked = (string) ($_POST['action'] ?? 'revoke') === 'revoke';
        $reason = trim((string) ($_POST['reason'] ?? ''));
        $ok = markCertificateRevoked($id, $revoked, $reason ?: null);
        setFlash($ok ? 'success' : 'error', $revoked ? 'ยกเลิกใบเกียรติบัตรเรียบร้อยแล้ว' : 'คืนสถานะใบเกียรติบัตรเรียบร้อยแล้ว');
        redirect('admin/certificates/');
    }

    public function apiSaveTemplate(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Method not allowed', [], 405);
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->jsonResponse(false, 'Invalid CSRF', [], 403);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $elements = (string) ($_POST['elements'] ?? '[]');

        if ($name === '') {
            $this->jsonResponse(false, 'กรุณาระบุชื่อเทมเพลต', [], 400);
        }

        json_decode($elements);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonResponse(false, 'elements JSON ไม่ถูกต้อง', [], 400);
        }

        require_once ROOT_PATH . '/models/CertTemplate.php';
        $result = saveCertificateTemplate($_POST, currentAdminId(), $id > 0 ? $id : null);
        if (!$result['success']) {
            $this->jsonResponse(false, implode("\n", $result['errors'] ?? ['บันทึกไม่สำเร็จ']), [], 400);
        }

        $this->jsonResponse(true, $id > 0 ? 'บันทึกสำเร็จ' : 'สร้างสำเร็จ', ['id' => (int) $result['id']]);
    }

    public function apiUploadAsset(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Method not allowed', [], 405);
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->jsonResponse(false, 'Invalid CSRF', [], 403);
        }

        $type = (string) ($_POST['type'] ?? 'element');
        $type = $type === 'bg' ? 'bg' : 'element';
        $file = $_FILES['file'] ?? null;

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->jsonResponse(false, 'ไม่มีไฟล์หรืออัปโหลดผิดพลาด', [], 400);
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            $this->jsonResponse(false, 'ไฟล์ใหญ่เกินไป (สูงสุด 5MB)', [], 400);
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName) || getimagesize($tmpName) === false) {
            $this->jsonResponse(false, 'ไฟล์ต้องเป็นรูปภาพ JPG/PNG/WEBP/GIF เท่านั้น', [], 400);
        }

        $info = getimagesize($tmpName);
        $mime = $info['mime'];
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        if (!isset($allowed[$mime])) {
            $this->jsonResponse(false, 'ไฟล์ต้องเป็นรูปภาพ JPG/PNG/WEBP/GIF เท่านั้น', [], 400);
        }

        $dir = TEMPLATE_UPLOAD_PATH . '/';
        $fname = ($type === 'bg' ? 'bg_' : 'el_') . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $path = 'uploads/templates/' . $fname;

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $this->jsonResponse(false, 'ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บไฟล์ได้ (Permission Denied)', ['path' => $dir], 500);
            }
        }

        if (!is_writable($dir)) {
            $this->jsonResponse(false, 'โฟลเดอร์ไม่สามารถเขียนไฟล์ได้ (Directory not writable)', ['path' => $dir], 500);
        }

        if (!move_uploaded_file($tmpName, $dir . $fname)) {
            $this->jsonResponse(false, 'บันทึกไฟล์ไม่สำเร็จ (move_uploaded_file failed)', ['fname' => $fname, 'dir' => $dir], 500);
        }

        $this->jsonResponse(true, 'อัปโหลดสำเร็จ', ['path' => $path]);
    }

    private function jsonResponse(bool $success, string $message = '', array $data = [], int $statusCode = 200): void
    {
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
