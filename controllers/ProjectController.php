<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/CertTemplate.php';

class ProjectController
{
    public function index(): void
    {
        requireLogin();
        $projects = getAllProjects();
        $pageTitle = 'โครงการสอบทั้งหมด';
        $breadcrumb = ['Dashboard', 'โครงการทั้งหมด'];
        $viewFile = VIEWS_PATH . '/projects/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();
        $project = projectDefaults();
        $templates = function_exists('getActiveCertificateTemplates') ? getActiveCertificateTemplates() : [];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'การขอข้อมูลไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            } else {
                $result = createProject($_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'สร้างโครงการเรียบร้อยแล้ว');
                    redirect('admin/projects/detail.php?id=' . (int) $result['id']);
                }
                $errors = $result['errors'];
                setFlash('error', 'กรุณาตรวจสอบข้อมูลโครงการให้ถูกต้อง');
                $project = array_merge($project, $_POST);
            }
        }

        $action = BASE_URL . '/admin/projects/create.php';
        $pageTitle = 'สร้างโครงการใหม่';
        $breadcrumb = ['Dashboard', 'โครงการทั้งหมด', 'สร้างโครงการ'];
        $viewFile = VIEWS_PATH . '/projects/create.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $project = getProject($id);
        if (!$project) {
            $this->notFound('ไม่พบโครงการที่ระบุ');
        }
        $templates = function_exists('getActiveCertificateTemplates') ? getActiveCertificateTemplates() : [];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'การขอข้อมูลไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';
            } else {
                $result = updateProject($id, $_POST);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขโครงการเรียบร้อยแล้ว');
                    redirect('admin/projects/detail.php?id=' . $id);
                }
                $errors = $result['errors'];
                setFlash('error', 'ไม่สามารถบันทึกการแก้ไขโครงการได้');
                $project = array_merge($project, $_POST);
            }
        }

        $action = BASE_URL . '/admin/projects/edit.php?id=' . $id;
        $pageTitle = 'แก้ไขโครงการ';
        $breadcrumb = ['Dashboard', 'โครงการทั้งหมด', $project['name'], 'แก้ไข'];
        $viewFile = VIEWS_PATH . '/projects/create.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function detail(): void
    {
        requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $project = getProject($id);
        if (!$project) {
            $this->notFound('ไม่พบโครงการที่ระบุ');
        }

        $runtimeStatus = getProjectRuntimeStatus($project);
        $pageTitle = $project['name'];
        $breadcrumb = ['Dashboard', 'โครงการทั้งหมด', $project['name']];
        $viewFile = VIEWS_PATH . '/projects/detail.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function schedule(): void
    {
        requireLogin();
        $id = (int) ($_POST['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null) || $id < 1) {
            $this->badRequest();
        }

        $ok = updateProjectSchedule($id, $_POST);
        setFlash($ok ? 'success' : 'error', $ok ? 'บันทึกการตั้งค่าช่วงเวลาสอบเรียบร้อยแล้ว' : 'ไม่สามารถบันทึกการตั้งค่าได้');
        redirect('admin/projects/detail.php?id=' . $id);
    }

    public function forceStatus(): void
    {
        requireLogin();
        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null) || $id < 1) {
            $this->badRequest();
        }

        setFlash(forceProjectStatus($id, $status) ? 'success' : 'error', 'อัปเดตสถานะโครงการเรียบร้อยแล้ว');
        redirect('admin/projects/detail.php?id=' . $id);
    }

    public function extendExam(): void
    {
        requireLogin();
        $id = (int) ($_POST['id'] ?? 0);
        $minutes = (int) ($_POST['minutes'] ?? 0);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            echo 'CSRF Token Invalid';
            exit;
        }

        if ($id < 1 || $minutes < 1) {
            http_response_code(400);
            echo 'ข้อมูลไม่ครบถ้วน (ID หรือ Minutes หายไป)';
            exit;
        }

        $ok = extendProjectExamEnd($id, $minutes);
        header('Content-Type: application/json');
        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'ขยายเวลาสอบเรียบร้อยแล้ว']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถขยายเวลาในระบบได้']);
        }
        exit;
    }

    public function delete(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->badRequest();
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0 && deleteProject($id)) {
            setFlash('success', 'ลบโครงการเรียบร้อยแล้ว');
        } else {
            setFlash('error', 'ไม่สามารถลบโครงการได้');
        }

        redirect('admin/projects/');
    }

    private function badRequest(): never
    {
        http_response_code(400);
        echo 'การขอข้อมูลไม่ถูกต้อง';
        exit;
    }

    private function notFound(string $message): never
    {
        http_response_code(404);
        echo e($message);
        exit;
    }
}
