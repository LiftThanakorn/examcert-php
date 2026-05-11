<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/CertTemplate.php';

class TemplateController
{
    public function index(): void
    {
        requireLogin();
        $templates = getCertificateTemplates();
        $templateMode = 'list';
        $pageTitle = 'จัดการเทมเพลต';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร', 'เทมเพลต'];
        $viewFile = VIEWS_PATH . '/certificates/templates.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();
        require VIEWS_PATH . '/certificates/template_builder.php';
    }

    public function edit(): void
    {
        requireLogin();
        require VIEWS_PATH . '/certificates/template_builder.php';
    }

    public function builder(): void
    {
        requireLogin();
        require VIEWS_PATH . '/certificates/template_builder.php';
    }

    public function delete(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            echo 'Bad request.';
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        setFlash(deleteCertificateTemplate($id) ? 'success' : 'error', 'ลบเทมเพลตเรียบร้อยแล้ว');
        redirect('admin/certificates/templates.php');
    }

    public function preview(): void
    {
        requireLogin();
        $id = (int) ($_GET['id'] ?? $_GET['template_id'] ?? 0);
        $template = $id > 0 ? getCertificateTemplate($id) : null;

        if (!$template) {
            http_response_code(404);
            echo 'Template not found.';
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($template, JSON_UNESCAPED_UNICODE);
    }
}
