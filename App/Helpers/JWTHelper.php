<?php

namespace App\Helpers;

use App\Models\UserModel;
use Firebase\JWT\JWT;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class JWTHelper
{
    public $payload = [];

    /**
     * Generates the jwt token
     *
     * @return string
     */
    public function getJwt($user)
    {
        $this->payload = $this->getPayload($user);
        return JWT::encode($this->payload, $_ENV['JWT_KEY'], $_ENV['JWT_ENCRYPTION']);
    }

    /**
     * Gets payload for encryption.
     *
     * @return array
     */
    private function getPayload($user)
    {
        return [
            "iat" => time(),
            "exp" => time() + intval($_ENV['JWT_LIVE_SEC']),
            'user_id' => $user['id'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'role_id' => $user['id'],
        ];
    }
}