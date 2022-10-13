<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\JWTHelper;

/**
 * User controller
 */
class User extends \Core\Controller
{

    public function __construct($route_body, $route_action)
    {
        parent::__construct($route_body, $route_action);
        $this->middlewareGroup = [
            'authenticated' => ['getCurrentUser']
        ];
    }

    /**
     * Returns current user info.
     *
     * @return void
     */
    public function getCurrentUser()
    {
        $userModel = UserModel::getCurrentUser();

        echo json_encode([
            'status' => true,
            'message' => 'User info successfully provided',
            'user_info' => $userModel
        ]);
    }
}