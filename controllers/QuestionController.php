<?php
declare(strict_types=1);

require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/Project.php';
require_once ROOT_PATH . '/src/Question.php';

class QuestionController
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

        $questions = getQuestionsByProject($projectId);
        $flash = getFlash();
        $pageTitle = 'คลังข้อสอบ';
        $viewFile = VIEWS_PATH . '/questions/index.php';

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

        $question = questionDefaults();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = createQuestion($projectId, $_POST, currentAdminId());
                if ($result['success']) {
                    setFlash('success', 'เพิ่มข้อสอบสำเร็จ');
                    redirect('admin/questions/?project_id=' . $projectId);
                }

                $errors = $result['errors'];
                $question = array_merge($question, $_POST);
            }
        }

        $action = BASE_URL . '/admin/questions/create.php?project_id=' . $projectId;
        $pageTitle = 'เพิ่มข้อสอบ';
        $viewFile = VIEWS_PATH . '/questions/create.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $questionRow = getQuestion($id);
        if (!$questionRow) {
            http_response_code(404);
            exit('Question not found.');
        }

        $project = getProject((int) $questionRow['project_id']);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        $question = decodeQuestionForForm($questionRow);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $result = updateQuestion($id, $_POST);
                if ($result['success']) {
                    setFlash('success', 'แก้ไขข้อสอบสำเร็จ');
                    redirect('admin/questions/?project_id=' . (int) $project['id']);
                }

                $errors = $result['errors'];
                $question = array_merge($question, $_POST);
            }
        }

        $action = BASE_URL . '/admin/questions/edit.php?id=' . $id;
        $pageTitle = 'แก้ไขข้อสอบ';
        $viewFile = VIEWS_PATH . '/questions/edit.php';

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
        $question = $id > 0 ? getQuestion($id) : null;
        $projectId = $question ? (int) $question['project_id'] : 0;

        if ($question && deleteQuestion($id)) {
            setFlash('success', 'ลบข้อสอบสำเร็จ');
        } else {
            setFlash('error', 'ไม่สามารถลบข้อสอบได้');
        }

        redirect('admin/questions/?project_id=' . $projectId);
    }
}
