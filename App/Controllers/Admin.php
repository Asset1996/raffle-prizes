<?php

namespace App\Controllers;

use App\Models\Prize\BasePrizeModel;

/**
 * Admin controller.
 */
class Admin extends \Core\Controller
{

    public function __construct($route_body, $route_action)
    {
        parent::__construct($route_body, $route_action);
        $this->middlewareGroup = [
            'employee' => ['getAllAcceptedPrizes', 'sendPrize']
        ];
    }

    /**
     * Returns all accepted prizes list
     * with user and subject info.
     *
     * @return void
     */
    public function getAllAcceptedPrizes()
    {
        $prizeModel = new BasePrizeModel();
        $acceptedPrizesList = $prizeModel->getAcceptedPrizesList();

        echo json_encode([
            'status' => true,
            'message' => 'Data successfully fetched.',
            'data' => $acceptedPrizesList
        ]);
    }

    /**
     * Send prizes:
     * - money          - to bank API,
     * - subject        - to email of user,
     * - loyalty points - to application account.
     *
     * @return void
     */
    public function sendPrize(){
        $data = json_decode($this->route_body);
        if (!property_exists($data, 'reserve_id')){
            echo json_encode([
                'status' => false,
                'message' => 'Reserve_id not provided.'
            ]);
            die();
        }
        $prizeModel = new BasePrizeModel();
        $prize = $prizeModel->getByReserveID($data->reserve_id);
        print_r($prize);exit();
    }
}