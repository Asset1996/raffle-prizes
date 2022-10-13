<?php

namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Middleware requires valid JWT token.
 */
class Employee implements \Core\Middleware
{
    public static function handle(){
        $decodedData = \App\Middlewares\Authenticated::handle();
        if($decodedData->role_id != 2){
            http_response_code(403);
            echo json_encode([
                'result' => false,
                'message' => 'You have no access.'
            ]);
            die();
        }
        return true;
    }
}