<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Project.php';

class HomeController
{
    public function index(): void
    {
        $projects = getAllProjects();
        $pageTitle = 'ยินดีต้อนรับสู่ ' . APP_NAME;
        require VIEWS_PATH . '/public/landing.php';
    }
}
