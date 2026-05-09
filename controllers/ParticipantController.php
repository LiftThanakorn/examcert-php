<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/Participant.php';

class ParticipantController
{
    private function ensureProject(int $projectId): ?array
    {
        return getProject($projectId);
    }

    public function index(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = $this->ensureProject($projectId);

        if (!$project) {
            $pageTitle = 'เลือกโครงการสอบเพื่อจัดการรายชื่อ';
            $targetLink = 'admin/participants/';
            $viewFile = VIEWS_PATH . '/layout/project_selector.php';
            require VIEWS_PATH . '/layout/admin.php';
            return;
        }

        $participants = getParticipantsByProject($projectId);
        $participantMode = 'list';
        $pageTitle = 'จัดการรายชื่อผู้มีสิทธิ์สอบ';
        $breadcrumb = ['Dashboard', 'ผู้มีสิทธิ์สอบ', $project['name']];
        $viewFile = VIEWS_PATH . '/participants/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = $this->ensureProject($projectId);

        if (!$project) {
            setFlash('error', 'กรุณาเลือกโครงการก่อนเพิ่มรายชื่อ');
            redirect('admin/projects/');
        }

        $participant = participantDefaults();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = createParticipant($projectId, $_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'เพิ่มรายชื่อสำเร็จ');
                    redirect('admin/participants/?project_id=' . $projectId);
                }
                $errors = $result['errors'];
                setFlash('error', 'กรุณาตรวจสอบข้อมูลรายชื่อให้ถูกต้อง');
                $participant = array_merge($participant, $_POST);
            }
        }

        $action = BASE_URL . '/admin/participants/create.php?project_id=' . $projectId;
        $participantMode = 'create';
        $pageTitle = 'เพิ่มรายชื่อใหม่';
        $breadcrumb = ['Dashboard', 'ผู้มีสิทธิ์สอบ', $project['name'], 'เพิ่มรายชื่อ'];
        $viewFile = VIEWS_PATH . '/participants/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $participant = getParticipant($id);
        if (!$participant) {
            setFlash('error', 'ไม่พบข้อมูลผู้มีสิทธิ์สอบ');
            redirect('admin/projects/');
        }

        $project = $this->ensureProject((int) $participant['project_id']);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = updateParticipant($id, $_POST);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขข้อมูลสำเร็จ');
                    redirect('admin/participants/?project_id=' . (int) $project['id']);
                }
                $errors = $result['errors'];
                setFlash('error', 'ไม่สามารถบันทึกการแก้ไขได้ กรุณาตรวจสอบข้อมูล');
                $participant = array_merge($participant, $_POST);
            }
        }

        $action = BASE_URL . '/admin/participants/edit.php?id=' . $id;
        $participantMode = 'edit';
        $pageTitle = 'แก้ไขข้อมูลผู้มีสิทธิ์สอบ';
        $breadcrumb = ['Dashboard', 'ผู้มีสิทธิ์สอบ', $project['name'], $participant['first_name'] . ' ' . $participant['last_name'], 'แก้ไข'];
        $viewFile = VIEWS_PATH . '/participants/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function delete(): void
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            setFlash('error', 'คำขอไม่ถูกต้อง');
            redirect('admin/projects/');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $participant = $id > 0 ? getParticipant($id) : null;
        $projectId = $participant ? (int) $participant['project_id'] : 0;

        if ($participant && deleteParticipant($id)) {
            setFlash('success', 'ลบรายชื่อสำเร็จ');
        } else {
            setFlash('error', 'ไม่สามารถลบรายชื่อได้');
        }

        redirect('admin/participants/?project_id=' . $projectId);
    }

    public function import(): void
    {
        requireLogin();
        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = $this->ensureProject($projectId);

        if (!$project) {
            setFlash('error', 'กรุณาเลือกโครงการก่อนนำเข้าข้อมูล');
            redirect('admin/projects/');
        }

        $pageTitle = 'นำเข้าข้อมูลรายชื่อผู้มีสิทธิ์สอบ (CSV/Excel)';
        $breadcrumb = ['Dashboard', 'ผู้มีสิทธิ์สอบ', $project['name'], 'นำเข้าข้อมูล'];
        $viewFile = VIEWS_PATH . '/participants/import.php';
        require VIEWS_PATH . '/layout/admin.php';
    }
}
}
