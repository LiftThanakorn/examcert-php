<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/Project.php';

class ProjectController
{
    public function index(): void
    {
        requireLogin();

        $projects = getAllProjects();
        $flash = getFlash();
        $pageTitle = 'โครงการสอบ';
        $viewFile = VIEWS_PATH . '/projects/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();

        $project = projectDefaults();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = createProject($_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'สร้างโครงการสอบสำเร็จ');
                    redirect('admin/projects/detail.php?id=' . (int) $result['id']);
                }
                $errors = $result['errors'];
                $project = array_merge($project, $_POST);
            }
        }

        $action = BASE_URL . '/admin/projects/create.php';
        $pageTitle = 'สร้างโครงการสอบ';
        $viewFile = VIEWS_PATH . '/projects/create.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $project = getProject($id);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = updateProject($id, $_POST);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขโครงการสอบสำเร็จ');
                    redirect('admin/projects/detail.php?id=' . $id);
                }
                $errors = $result['errors'];
                $project = array_merge($project, $_POST);
            }
        }

        $action = BASE_URL . '/admin/projects/edit.php?id=' . $id;
        $pageTitle = 'แก้ไขโครงการสอบ';
        $viewFile = VIEWS_PATH . '/projects/edit.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function detail(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $project = getProject($id);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $flash = getFlash();
        $pageTitle = $project['name'];
        $viewFile = VIEWS_PATH . '/projects/detail.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function delete(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Bad request.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0 && deleteProject($id)) {
            setFlash('success', 'ลบโครงการสอบสำเร็จ');
        } else {
            setFlash('error', 'ไม่สามารถลบโครงการสอบได้');
        }

        redirect('admin/projects/');
    }
}
