<?php
declare(strict_types=1);

require_once ROOT_PATH . '/config/database.php';

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }
}

