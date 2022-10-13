<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Middleware requires the user to be guest
 * (not authorized).
 */
class Guest implements \Core\Middleware
{
    public static function handle(){
        $content = json_decode(file_get_contents('php://input'));
        if (isset($content->jwt)) {
            http_response_code(403);
            echo json_encode([
                'result' => false,
                'message' => 'Route forbidden.'
            ]);
            die();
        }
    }
}