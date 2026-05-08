<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Bangkok');

define('APP_NAME', 'ExamCert');
define('APP_ENV', 'local');
define('BASE_URL', 'http://localhost/examcert');

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('LOG_FILE', LOGS_PATH . '/app.log');

define('CERT_UPLOAD_PATH', UPLOADS_PATH . '/certificates');
define('TEMPLATE_UPLOAD_PATH', UPLOADS_PATH . '/templates');
define('SIGNATURE_UPLOAD_PATH', UPLOADS_PATH . '/signatures');

define('DEFAULT_TIMEZONE', 'Asia/Bangkok');

