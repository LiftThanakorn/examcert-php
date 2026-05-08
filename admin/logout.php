<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Auth.php';

logoutAdmin();
redirect('admin/login.php');

