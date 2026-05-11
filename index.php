<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';

// Simple Router
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = str_replace('\\', '/', dirname($scriptName));

if ($baseDir !== '/' && strpos($requestUri, $baseDir) === 0) {
    $requestUri = substr($requestUri, strlen($baseDir));
}

$requestUri = '/' . ltrim($requestUri, '/');
if ($requestUri === '/' || $requestUri === '/index.php') {
    $requestUri = '/';
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
    '/admin', '/admin/', '/admin/dashboard.php' => [DashboardController::class, 'index'],
    '/admin/login.php' => [AuthController::class, 'login'],
    '/admin/logout.php' => [AuthController::class, 'logout'],
    
    // Projects
    '/admin/projects', '/admin/projects/', '/admin/projects/index.php' => [ProjectController::class, 'index'],
    '/admin/projects/create.php' => [ProjectController::class, 'create'],
    '/admin/projects/edit.php' => [ProjectController::class, 'edit'],
    '/admin/projects/detail.php' => [ProjectController::class, 'detail'],
    '/admin/projects/delete.php' => [ProjectController::class, 'delete'],
    '/admin/projects/schedule.php' => [ProjectController::class, 'schedule'],
    '/admin/projects/force-status.php' => [ProjectController::class, 'forceStatus'],
    '/admin/projects/extend.php' => [ProjectController::class, 'extendExam'],
    
    // Questions & Participants
    '/admin/questions', '/admin/questions/', '/admin/questions/index.php' => [QuestionController::class, 'index'],
    '/admin/questions/import.php' => [QuestionController::class, 'import'],
    '/admin/questions/create.php' => [QuestionController::class, 'create'],
    '/admin/questions/edit.php' => [QuestionController::class, 'edit'],
    '/admin/questions/delete.php' => [QuestionController::class, 'delete'],
    '/admin/participants', '/admin/participants/', '/admin/participants/index.php' => [ParticipantController::class, 'index'],
    '/admin/participants/import.php' => [ParticipantController::class, 'import'],
    '/admin/participants/export.php' => [ParticipantController::class, 'export'],
    '/admin/participants/create.php' => [ParticipantController::class, 'create'],
    '/admin/participants/edit.php' => [ParticipantController::class, 'edit'],
    '/admin/participants/delete.php' => [ParticipantController::class, 'delete'],
    
    // Certificates & Templates (Unified in CertificateController)
    '/admin/certificates', '/admin/certificates/', '/admin/certificates/index.php' => [CertificateController::class, 'index'],
    '/admin/certificates/issue', '/admin/certificates/issue.php' => [CertificateController::class, 'issue'],
    '/admin/certificates/revoke', '/admin/certificates/revoke.php' => [CertificateController::class, 'revoke'],
    '/admin/certificates/templates', '/admin/certificates/templates.php' => [CertificateController::class, 'templates'],
    '/admin/certificates/template/create', '/admin/certificates/template-create.php' => [CertificateController::class, 'templateCreate'],
    '/admin/certificates/template/edit', '/admin/certificates/template-edit.php', '/admin/certificates/template-builder.php' => [CertificateController::class, 'templateEdit'],
    '/admin/certificates/template/delete' => [CertificateController::class, 'templateDelete'],
    '/certificates/preview_pdf' => [CertificateController::class, 'previewPDFRoute'],
    '/certificates/export' => [CertificateController::class, 'exportRoute'],
    
    // Exam Sessions & Reports
    '/admin/exam-sessions', '/admin/exam-sessions/', '/admin/exam-sessions/index.php' => [ExamController::class, 'sessions'],
    '/admin/exam-sessions/export.php' => [ExamController::class, 'exportSessions'],
    '/admin/exam-sessions/delete.php' => [ExamController::class, 'deleteSession'],
    '/admin/reports', '/admin/reports/', '/admin/reports/index.php' => [ReportController::class, 'index'],
    '/admin/reports/export.php' => [ReportController::class, 'export'],
    
    // Public Routes
    '/verify', '/public/verify.php' => [PublicExamController::class, 'verify'],
    '/public/exam.php', '/public/entry.php' => [PublicExamController::class, 'entry'],
    '/public/take-exam.php' => [PublicExamController::class, 'take'],
    '/public/result.php' => [PublicExamController::class, 'result'],
    
    // API Routes
    '/api/save-template' => [CertificateController::class, 'apiSaveTemplate'],
    '/api/upload-asset' => [CertificateController::class, 'apiUploadAsset'],
    
    default => null,
};

if ($match && $match[0]) {
    [$controllerClass, $method] = $match;
    $controller = new $controllerClass();
    $controller->$method();
} elseif ($requestUri === '/') {
    require_once ROOT_PATH . '/models/Project.php';
    $projects = array_filter(getAllProjects(), fn($p) => $p['status'] === 'active');
    require VIEWS_PATH . '/public/landing.php';
} else {
    http_response_code(404);
    echo "404 Not Found: " . e($requestUri);
}
