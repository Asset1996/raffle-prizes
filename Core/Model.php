<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 */
abstract class Model
{
    public $db;

    public function __construct(){
        $this->db = $this->getDB();
    }
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected function getDB()
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $usr = $_ENV['DB_USER'];
        $pwd = $_ENV['DB_PASSWORD'];
        return new \FaaPz\PDO\Database($dsn, $usr, $pwd);
    }
}