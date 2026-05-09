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
        $template = templateDefaults();
        $templateMode = 'create';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'การขอข้อมูลไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            } else {
                $result = saveCertificateTemplate($_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'สร้างเทมเพลตเรียบร้อยแล้ว');
                    redirect('admin/certificates/templates.php');
                }
                $errors = $result['errors'];
                setFlash('error', 'กรุณาตรวจสอบข้อมูลให้ถูกต้อง');
                $template = array_merge($template, $_POST);
            }
        }

        $action = BASE_URL . '/admin/certificates/template-create.php';
        $pageTitle = 'สร้างเทมเพลตใหม่';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร', 'เทมเพลต', 'สร้างใหม่'];
        $viewFile = VIEWS_PATH . '/certificates/templates.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $template = getCertificateTemplate($id);
        if (!$template) {
            http_response_code(404);
            echo 'ไม่พบเทมเพลตที่ระบุ';
            exit;
        }

        $templateMode = 'edit';
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'การขอข้อมูลไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            } else {
                $result = saveCertificateTemplate($_POST, currentAdminId(), $id);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขเทมเพลตเรียบร้อยแล้ว');
                    redirect('admin/certificates/templates.php');
                }
                $errors = $result['errors'];
                setFlash('error', 'ไม่สามารถแก้ไขข้อมูลได้ กรุณาตรวจสอบอีกครั้ง');
                $template = array_merge($template, $_POST);
            }
        }

        $action = BASE_URL . '/admin/certificates/template-edit.php?id=' . $id;
        $pageTitle = 'แก้ไขเทมเพลต';
        $breadcrumb = ['Dashboard', 'ใบเกียรติบัตร', 'เทมเพลต', $template['name']];
        $viewFile = VIEWS_PATH . '/certificates/templates.php';
        require VIEWS_PATH . '/layout/admin.php';
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
}
