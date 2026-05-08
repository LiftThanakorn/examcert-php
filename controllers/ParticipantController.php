<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/Project.php';
require_once ROOT_PATH . '/src/Participant.php';

class ParticipantController
{
    public function index(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = getProject($projectId);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $participants = getParticipantsByProject($projectId);
        $flash = getFlash();
        $pageTitle = 'ผู้มีสิทธิ์สอบ';
        $viewFile = VIEWS_PATH . '/participants/index.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = getProject($projectId);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $participant = participantDefaults();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = createParticipant($projectId, $_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'เพิ่มผู้มีสิทธิ์สอบสำเร็จ');
                    redirect('admin/participants/?project_id=' . $projectId);
                }
                $errors = $result['errors'];
                $participant = array_merge($participant, $_POST);
            }
        }

        $action = BASE_URL . '/admin/participants/create.php?project_id=' . $projectId;
        $pageTitle = 'เพิ่มผู้มีสิทธิ์สอบ';
        $viewFile = VIEWS_PATH . '/participants/create.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $participant = getParticipant($id);
        if (!$participant) {
            http_response_code(404);
            exit('Participant not found.');
        }

        $project = getProject((int) $participant['project_id']);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = updateParticipant($id, $_POST);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขผู้มีสิทธิ์สอบสำเร็จ');
                    redirect('admin/participants/?project_id=' . (int) $project['id']);
                }
                $errors = $result['errors'];
                $participant = array_merge($participant, $_POST);
            }
        }

        $action = BASE_URL . '/admin/participants/edit.php?id=' . $id;
        $pageTitle = 'แก้ไขผู้มีสิทธิ์สอบ';
        $viewFile = VIEWS_PATH . '/participants/edit.php';
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
        $participant = $id > 0 ? getParticipant($id) : null;
        $projectId = $participant ? (int) $participant['project_id'] : 0;

        if ($participant && deleteParticipant($id)) {
            setFlash('success', 'ลบรายชื่อผู้มีสิทธิ์สอบสำเร็จ');
        } else {
            setFlash('error', 'ไม่สามารถลบรายชื่อผู้มีสิทธิ์สอบได้');
        }

        redirect('admin/participants/?project_id=' . $projectId);
    }
}
