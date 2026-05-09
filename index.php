<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}
$path = '/' . trim($path, '/');

$route = match ($path) {
    '/', '/admin', '/admin/', '/admin/dashboard.php' => [DashboardController::class, 'index'],
    '/admin/login.php' => [AuthController::class, 'login'],
    '/admin/logout.php' => 'logout',
    '/admin/projects', '/admin/projects/', '/admin/projects/index.php' => [ProjectController::class, 'index'],
    '/admin/projects/create.php' => [ProjectController::class, 'create'],
    '/admin/projects/edit.php' => [ProjectController::class, 'edit'],
    '/admin/projects/detail.php' => [ProjectController::class, 'detail'],
    '/admin/projects/delete.php' => [ProjectController::class, 'delete'],
    '/admin/projects/schedule.php' => [ProjectController::class, 'schedule'],
    '/admin/projects/force-status.php' => [ProjectController::class, 'forceStatus'],
    '/admin/projects/extend.php' => [ProjectController::class, 'extendExam'],
    '/admin/participants', '/admin/participants/', '/admin/participants/index.php' => [ParticipantController::class, 'index'],
    '/admin/participants/create.php' => [ParticipantController::class, 'create'],
    '/admin/participants/edit.php' => [ParticipantController::class, 'edit'],
    '/admin/participants/delete.php' => [ParticipantController::class, 'delete'],
    '/admin/participants/import.php' => [ParticipantController::class, 'import'],
    '/admin/questions', '/admin/questions/', '/admin/questions/index.php' => [QuestionController::class, 'index'],
    '/admin/questions/create.php' => [QuestionController::class, 'create'],
    '/admin/questions/edit.php' => [QuestionController::class, 'edit'],
    '/admin/questions/delete.php' => [QuestionController::class, 'delete'],
    '/admin/questions/import.php' => [QuestionController::class, 'import'],
    '/admin/certificates', '/admin/certificates/', '/admin/certificates/index.php' => [CertificateController::class, 'index'],
    '/admin/certificates/issue.php' => [CertificateController::class, 'issue'],
    '/admin/certificates/download.php' => [CertificateController::class, 'download'],
    '/admin/certificates/revoke.php' => [CertificateController::class, 'revoke'],
    '/admin/certificates/templates.php' => [TemplateController::class, 'index'],
    '/admin/certificates/template-create.php' => [TemplateController::class, 'create'],
    '/admin/certificates/template-edit.php' => [TemplateController::class, 'edit'],
    '/admin/certificates/template-delete.php' => [TemplateController::class, 'delete'],
    '/admin/exam-sessions', '/admin/exam-sessions/', '/admin/exam-sessions/index.php' => [ExamController::class, 'sessions'],
    '/admin/exam-sessions/export.php' => [ExamController::class, 'exportSessions'],
    '/admin/reports', '/admin/reports/', '/admin/reports/index.php' => [ReportController::class, 'index'],
    '/admin/reports/export.php' => [ReportController::class, 'export'],
    '/public/exam.php' => [PublicExamController::class, 'entry'],
    '/public/take-exam.php' => [PublicExamController::class, 'take'],
    '/public/result.php' => [PublicExamController::class, 'result'],
    '/public/verify.php' => [PublicExamController::class, 'verify'],
    default => null,
};

if ($route === 'logout') {
    require_once ROOT_PATH . '/models/Admin.php';
    logoutAdmin();
    redirect('admin/login.php');
}

if (is_array($route)) {
    [$controller, $method] = $route;
    require_once CONTROLLERS_PATH . '/' . $controller . '.php';
    (new $controller())->$method();
    exit;
}

http_response_code(404);
echo 'Not found.';
