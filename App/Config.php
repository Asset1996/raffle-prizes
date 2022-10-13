<?php

namespace App;

use PDO;

/**
 * Application configuration
 *
 */
class Config
{
    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Database connection.
     * @var connection
     */
    public $connection;
}