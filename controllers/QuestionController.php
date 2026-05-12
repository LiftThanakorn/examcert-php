<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Admin.php';
require_once ROOT_PATH . '/models/Project.php';
require_once ROOT_PATH . '/models/Question.php';

class QuestionController
{
    public function import(): void
    {
        requireLogin();
        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = getProject($projectId);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                setFlash('error', 'การขอข้อมูลไม่ถูกต้อง');
            } else {
                $file = $_FILES['csv_file'] ?? null;
                if ($file && $file['tmp_name']) {
                    $handle = fopen($file['tmp_name'], 'r');
                    $count = 0;
                    $errors = 0;
                    $questions = [];
                    
                    // Skip header if needed (assuming first row is header)
                    fgetcsv($handle); 

                    while (($data = fgetcsv($handle)) !== false) {
                        if (count($data) < 4) continue;
                        
                        $text = trim($data[0]);
                        $type = trim($data[1]);
                        $choicesRaw = trim($data[2]);
                        $correct = trim($data[3]);
                        $difficulty = trim($data[4] ?? 'medium');
                        $category = trim($data[5] ?? '');

                        if ($text === '') { $errors++; continue; }

                        // Process choices
                        $choices = [];
                        if ($type === 'multiple_choice') {
                            $choiceKeys = questionChoiceKeys();
                            $parts = explode('|', $choicesRaw);
                            foreach ($parts as $idx => $p) {
                                $p = trim($p);
                                if ($p !== '') {
                                    $key = $choiceKeys[count($choices)] ?? null;
                                    if (preg_match('/^([a-dกขคง])[\).\:\-]\s*(.+)$/u', $p, $matches)) {
                                        $key = normalizeChoiceKey($matches[1]);
                                        $p = trim($matches[2]);
                                    }
                                    if ($key !== null && in_array($key, $choiceKeys, true)) {
                                        $choices[] = ['key' => $key, 'text' => $p];
                                    }
                                }
                            }
                            $correct = normalizeChoiceKey($correct);
                        } elseif ($type === 'true_false') {
                            $choices = [
                                ['key' => 'true', 'text' => 'ถูก'],
                                ['key' => 'false', 'text' => 'ผิด'],
                            ];
                            $correct = textLower($correct);
                        }

                        $questions[] = [
                            'project_id' => $projectId,
                            'question_text' => $text,
                            'type' => $type,
                            'choices' => json_encode($choices, JSON_UNESCAPED_UNICODE),
                            'correct_answer' => $correct,
                            'difficulty' => $difficulty,
                            'category' => $category,
                            'created_by' => currentAdminId()
                        ];
                    }
                    fclose($handle);

                    if (!empty($questions)) {
                        $ok = bulkInsertQuestions($questions);
                        if ($ok) {
                            setFlash('success', 'นำเข้าข้อสอบสำเร็จ ' . count($questions) . ' ข้อ');
                            redirect('admin/questions/?project_id=' . $projectId);
                        }
                    }
                    setFlash('error', 'ไม่สามารถนำเข้าข้อมูลได้ หรือไฟล์ไม่มีข้อมูลที่ถูกต้อง');
                }
            }
        }

        $pageTitle = 'นำเข้าข้อสอบ (CSV)';
        $breadcrumb = ['Dashboard', 'คลังข้อสอบ', $project['name'], 'นำเข้า (CSV)'];
        $viewFile = VIEWS_PATH . '/questions/import.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

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
            $pageTitle = 'เลือกโครงการสอบเพื่อจัดการข้อสอบ';
            $targetLink = 'admin/questions/';
            $viewFile = VIEWS_PATH . '/layout/project_selector.php';
            require VIEWS_PATH . '/layout/admin.php';
            return;
        }

        $questions = getQuestionsByProject($projectId);
        $pageTitle = 'จัดการคลังข้อสอบ';
        $breadcrumb = ['Dashboard', 'คลังข้อสอบ', $project['name']];
        $viewFile = VIEWS_PATH . '/questions/index.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function create(): void
    {
        requireLogin();

        $projectId = (int) ($_GET['project_id'] ?? 0);
        $project = $this->ensureProject($projectId);

        if (!$project) {
            setFlash('error', 'กรุณาเลือกโครงการก่อนเพิ่มข้อสอบ');
            redirect('admin/projects/');
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
                setFlash('error', 'กรุณาตรวจสอบข้อมูลข้อสอบให้ถูกต้อง');
                $question = array_merge($question, $_POST);
            }
        }

        $action = BASE_URL . '/admin/questions/create.php?project_id=' . $projectId;
        $pageTitle = 'เพิ่มข้อสอบใหม่';
        $breadcrumb = ['Dashboard', 'คลังข้อสอบ', $project['name'], 'เพิ่มข้อสอบ'];
        $isEdit = false;
        $viewFile = VIEWS_PATH . '/questions/form.php';

        require VIEWS_PATH . '/layout/admin.php';
    }

    public function edit(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $questionRow = getQuestion($id);
        if (!$questionRow) {
            setFlash('error', 'ไม่พบข้อมูลข้อสอบ');
            redirect('admin/projects/');
        }

        $project = $this->ensureProject((int) $questionRow['project_id']);
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
                setFlash('error', 'ไม่สามารถบันทึกการแก้ไขข้อสอบได้');
                $question = array_merge($question, $_POST);
            }
        }

        $action = BASE_URL . '/admin/questions/edit.php?id=' . $id;
        $pageTitle = 'แก้ไขข้อมูลข้อสอบ';
        $breadcrumb = ['Dashboard', 'คลังข้อสอบ', $project['name'], 'แก้ไขข้อสอบ ID: ' . $id];
        $isEdit = true;
        $viewFile = VIEWS_PATH . '/questions/form.php';

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
