<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Middleware requires valid JWT token.
 */
class Authenticated implements \Core\Middleware
{
    public static function handle(){
        $content = json_decode(file_get_contents('php://input'));
        if (!isset($content->jwt)) {
            http_response_code(403);
            echo json_encode([
                'result' => false,
                'message' => 'JWT token not provided.'
            ]);
            die();
        }
        try {
            return JWT::decode($content->jwt, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ENCRYPTION']));
        }catch (\Exception $e){
            http_response_code(403);
            echo json_encode([
                'result' => false,
                'message' => $e->getMessage()
            ]);
            die();
        }

    }
}