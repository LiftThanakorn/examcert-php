<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';

// Simple Router
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = dirname($scriptName);

// Clean up URI
if ($baseDir !== '/' && strpos($requestUri, $baseDir) === 0) {
    $requestUri = substr($requestUri, strlen($baseDir));
}
if ($requestUri === '' || $requestUri === '/') {
    $requestUri = '/index.php';
}

// Auto-loader for Controllers
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/controllers/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Route mapping
$match = match ($requestUri) {
    '/index.php' => [null, null], // Handle home separately or via default
    '/admin', '/admin/', '/admin/index.php' => [AdminController::class, 'dashboard'],
    '/admin/login.php' => [AdminController::class, 'login'],
    '/admin/logout.php' => [AdminController::class, 'logout'],
    '/admin/projects', '/admin/projects/', '/admin/projects/index.php' => [ProjectController::class, 'index'],
    '/admin/projects/create.php' => [ProjectController::class, 'create'],
    '/admin/projects/edit.php' => [ProjectController::class, 'edit'],
    '/admin/projects/delete.php' => [ProjectController::class, 'delete'],
    '/admin/projects/schedule.php' => [ProjectController::class, 'schedule'],
    '/admin/projects/questions.php' => [ProjectController::class, 'questions'],
    '/admin/projects/participants.php' => [ProjectController::class, 'participants'],
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
    '/admin/exam-sessions/delete.php' => [ExamController::class, 'deleteSession'],
    '/admin/reports', '/admin/reports/', '/admin/reports/index.php' => [ReportController::class, 'index'],
    '/admin/reports/export.php' => [ReportController::class, 'export'],
    '/public/exam.php', '/public/entry.php' => [PublicExamController::class, 'entry'],
    '/public/take-exam.php' => [PublicExamController::class, 'take'],
    '/public/result.php' => [PublicExamController::class, 'result'],
    '/public/verify.php' => [PublicExamController::class, 'verify'],
    '/public/render-cert.php' => [PublicExamController::class, 'renderCertificate'],
    '/public/download-cert.php' => [PublicExamController::class, 'downloadCertificate'],
    default => null,
};

if ($match && $match[0]) {
    [$controllerClass, $method] = $match;
    $controller = new $controllerClass();
    $controller->$method();
} elseif ($requestUri === '/index.php' || $requestUri === '') {
    require_once ROOT_PATH . '/models/Project.php';
    $projects = array_filter(getAllProjects(), fn($p) => $p['status'] === 'active');
    require VIEWS_PATH . '/public/landing.php';
} else {
    http_response_code(404);
    echo "404 Not Found: " . e($requestUri);
}
