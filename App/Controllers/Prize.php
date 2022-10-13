<?php

namespace App\Controllers;

use App\Models\Prize\BasePrizeModel;
use App\Models\Prize\SubjectPrizeModel;
use App\Models\PrizeStoreModel;

/**
 * Prize controller
 */
class Prize extends \Core\Controller
{

    public function __construct($route_body, $route_action)
    {
        parent::__construct($route_body, $route_action);
        $this->middlewareGroup = [
            'authenticated' => ['getRandomPrize', 'rejectPrize']
        ];
    }

    /**
     * Return the randomly selected prize
     * with random quantity.
     *
     * @return void
     * @throws \Exception
     */
    public function getRandomPrize()
    {
        $prizeModel = new PrizeStoreModel();
        $decodedData = json_decode($this->route_body);
        $randomPrize = $prizeModel->getRandomPrize($decodedData);

        echo json_encode([
            'status' => true,
            'message' => 'Prize successfully provided.',
            'prize' => $randomPrize
        ]);
    }

    /**
     * User declines the randomly selected prize.
     *
     * @return void
     * @throws \Exception
     */
    public function rejectPrize($prizeModel = BasePrizeModel::class)
    {
        $prizeData = json_decode($this->route_body);
        if (!property_exists($prizeData, 'reserve_id')){
            echo json_encode([
                'status' => false,
                'message' => 'Reserve_id not provided.'
            ]);
            die();
        }
        if (!property_exists($prizeData, 'id')){
            echo json_encode([
                'status' => false,
                'message' => 'ID not provided.'
            ]);
            die();
        }

        $subjectRevoked = True;
        if ($prizeData->id == 2){
            $subjectPrizeModel = new SubjectPrizeModel();
            if (!$subjectPrizeModel->revokeSubjectPrize((array) $prizeData)){
                $subjectRevoked = False;
            }
        }
        if (!$subjectRevoked){
            echo json_encode([
                'status' => false,
                'message' => 'Error occurred.'
            ]);
            die();
        }

        $result = (new $prizeModel)->rejectPrize($prizeData);

        if (!$result){
            echo json_encode([
                'status' => false,
                'message' => 'Prize was not rejected.'
            ]);
            die();
        }

        echo json_encode([
            'status' => true,
            'message' => 'Prize successfully rejected.'
        ]);
    }

    /**
     * User accepts the randomly selected prize.
     *
     * @return void
     * @throws \Exception
     */
    public function acceptPrize(
        $prizeModel = BasePrizeModel::class,
        $prizeStoreModel = PrizeStoreModel::class
    )
    {
        $data = json_decode($this->route_body);
        if (!property_exists($data, 'reserve_id')){
            echo json_encode([
                'status' => false,
                'message' => 'Reserve_id not provided.'
            ]);
            die();
        }
        $reservedPrize = (new $prizeModel)->acceptPrize($data);

        if (!$reservedPrize){
            echo json_encode([
                'status' => false,
                'message' => 'Prize was not accepted.'
            ]);
            die();
        }

        $isDecrementedInStore = (new $prizeStoreModel)->decrementPrizeQuantity($reservedPrize);

        echo json_encode([
            'status' => true,
            'message' => 'Prize successfully accepted.'
        ]);
    }
}