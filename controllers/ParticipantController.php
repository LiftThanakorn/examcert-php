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
        $projectId = (int) ($_GET['project_id'] ?? $_POST['project_id'] ?? 0);
        $project = $this->ensureProject($projectId);

        if (!$project) {
            setFlash('error', 'กรุณาเลือกโครงการก่อนนำเข้าข้อมูล');
            redirect('admin/projects/');
        }

        $errors = [];
        $summary = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = 'คำขอไม่ถูกต้อง กรุณาลองใหม่';
            } else {
                $summary = $this->handleImport($projectId);
                $errors = $summary['errors'] ?? [];
                if (!$errors) {
                    setFlash('success', 'นำเข้าข้อมูลรายชื่อสำเร็จ (สร้างใหม่ ' . $summary['created'] . ' รายการ)');
                } else {
                    setFlash('error', 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล');
                }
            }
        }

        $pageTitle = 'นำเข้าข้อมูลรายชื่อผู้มีสิทธิ์สอบ (CSV/Excel)';
        $breadcrumb = ['Dashboard', 'ผู้มีสิทธิ์สอบ', $project['name'], 'นำเข้าข้อมูล'];
        $viewFile = VIEWS_PATH . '/participants/import.php';
        require VIEWS_PATH . '/layout/admin.php';
    }

    private function handleImport(int $projectId): array
    {
        $file = $_FILES['participant_file'] ?? null;
        if (!$file || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['created' => 0, 'skipped' => 0, 'errors' => ['กรุณาอัปโหลดไฟล์ CSV'], 'rows' => []];
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
            return ['created' => 0, 'skipped' => 0, 'errors' => ['รองรับเฉพาะไฟล์ CSV, XLSX หรือ XLS เท่านั้น'], 'rows' => []];
        }

        if ($extension !== 'csv') {
            $autoload = ROOT_PATH . '/lib/PhpSpreadsheet/autoload.php';
            if (!is_file($autoload)) {
                return ['created' => 0, 'skipped' => 0, 'errors' => ['ไม่พบไลบรารี PhpSpreadsheet'], 'rows' => []];
            }
            require_once $autoload;
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                return ['created' => 0, 'skipped' => 0, 'errors' => ['PhpSpreadsheet IOFactory is unavailable.'], 'rows' => []];
            }
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load((string) $file['tmp_name']);
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } else {
            $rows = [];
            $content = file_get_contents((string) $file['tmp_name']);
            if ($content === false) {
                return ['created' => 0, 'skipped' => 0, 'errors' => ['ไม่สามารถอ่านไฟล์ที่อัปโหลดได้'], 'rows' => []];
            }
            // Auto-detect and convert to UTF-8 (common in Thailand: Windows-874)
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, Windows-874');
            $handle = fopen('php://temp', 'r+');
            fwrite($handle, $content);
            rewind($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        $created = 0;
        $skipped = 0;
        $rowResults = [];
        $batch = 'import-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));
        foreach ($rows as $index => $row) {
            if ($index === 0 && $this->looksLikeHeader($row)) {
                continue;
            }
            $data = [
                'first_name' => trim((string) ($row[0] ?? '')),
                'last_name' => trim((string) ($row[1] ?? '')),
                'email' => trim((string) ($row[2] ?? '')),
                'organization' => trim((string) ($row[3] ?? '')),
                'position' => trim((string) ($row[4] ?? '')),
                'phone' => trim((string) ($row[5] ?? '')),
                'id_card' => trim((string) ($row[6] ?? '')),
                'note' => trim((string) ($row[7] ?? '')),
                'import_batch' => $batch,
            ];
            if ($data['first_name'] === '' && $data['last_name'] === '' && $data['email'] === '') {
                continue;
            }
            $result = createParticipant($projectId, $data, currentAdminId());
            if ($result['success']) {
                $created++;
                $rowResults[] = ['row' => $index + 1, 'status' => 'created', 'message' => $data['first_name'] . ' ' . $data['last_name']];
            } else {
                $skipped++;
                $rowResults[] = ['row' => $index + 1, 'status' => 'skipped', 'message' => implode(', ', $result['errors'])];
            }
        }

        return ['created' => $created, 'skipped' => $skipped, 'errors' => [], 'rows' => $rowResults, 'batch' => $batch];
    }

    private function looksLikeHeader(array $row): bool
    {
        $first = strtolower(trim((string) ($row[0] ?? '')));
        return in_array($first, ['first_name', 'firstname', 'first name', 'ชื่อ'], true);
    }
}
