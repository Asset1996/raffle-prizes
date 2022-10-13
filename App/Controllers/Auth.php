<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\JWTHelper;

/**
 * Auth controller
 */
class Auth extends \Core\Controller
{

    public function __construct($route_body, $route_action)
    {
        parent::__construct($route_body, $route_action);
        $this->middlewareGroup = [
            'guest' => ['login']
        ];
    }

    /**
     * UserModel login.
     *
     * @return void
     */
    public function login($jwt = JWTHelper::class)
    {
        $userModel = new UserModel();
        $decodedData = json_decode($this->route_body);
        $user = $userModel->attemptLogin($decodedData);

        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'status' => false,
                'message' => 'UserModel not found'
            ]);
            die();
        }

        if (!password_verify($decodedData->password, $user['password'])){
            http_response_code(405);
            echo json_encode([
                'status' => false,
                'message' => 'Incorrect password'
            ]);
            die();
        }

        $jwt = (new $jwt())->getJwt($user);

        echo json_encode([
            'status' => true,
            'message' => 'Successfully authorized',
            'jwt' => $jwt
        ]);
    }
}