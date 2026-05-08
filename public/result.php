<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once CONTROLLERS_PATH . '/PublicExamController.php';

(new PublicExamController())->result();
