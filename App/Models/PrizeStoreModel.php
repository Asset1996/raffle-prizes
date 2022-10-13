<?php

namespace App\Models;

use App\Models\Prize\MoneyPrizeModel;
use App\Models\Prize\SubjectPrizeModel;
use App\Models\Prize\LoyaltyPointPrizeModel;
use FaaPz\PDO\Clause\Conditional;
use stdClass;
use Exception;

/**
 * Prize store model.
 */
class PrizeStoreModel extends \Core\Model
{
    /**
     * Name of table in DB.
     *
     * @var string
     */
    public $tableName = 'prizes_store';
    public static $tableNameStatic = 'prizes_store';

    /**
     * Returns random prize from prizes store.
     *
     * @param stdClass $decodedData
     * @return stdClass|bool
     * @throws Exception
     */
    public function getRandomPrize(stdClass $decodedData)
    {
        $select = [
            'id',
            'name',
            'quantity_left as quantity_was',
            '(CASE
                WHEN id=1 THEN FLOOR( 1 + RAND( ) * quantity_left )
                WHEN id=2 THEN 1
                ELSE FLOOR(RAND( ) * 1000)
            END) as selected_quantity'
        ];
        $select = $this->db->select($select)
            ->from($this->tableName)
            ->where(new Conditional('id', '=', random_int(1,3)));

        $stmt = $select->execute();
        $prizeData = $stmt->fetch();

        if (!$prizeData) {
            return false;
        }
        $quantityLeft = $prizeData['id'] == 3
            ? -1
            : $prizeData['quantity_was'] - $prizeData['selected_quantity'];

        $prizeData['quantity_left'] = $quantityLeft;
        $prizeData['reserve_id'] = uniqid();

        if($prizeData['id'] == 1){
            $prizeModel = new MoneyPrizeModel();
        }elseif($prizeData['id'] == 2){
            $prizeModel = new SubjectPrizeModel();
        }else{
            $prizeModel = new LoyaltyPointPrizeModel();
        }

        $prizeID = $prizeModel->createRecord($prizeData);
        $prizeData['prize_id'] = $prizeID;

        return $prizeData;
    }

    /**
     * Decrements the prize quantity in prize store.
     *
     * @param array $prizeData
     * @return bool
     */
    public function decrementPrizeQuantity(array $prizeData):bool
    {
        if ($prizeData['prize_type'] == 3) {
            return True;
        }

        $updateQuery = "UPDATE " . $this->tableName
            . " SET quantity_left=quantity_left-" . $prizeData['quantity']
            . " WHERE id=" . $prizeData['prize_type'];
        $update = $this->db->prepare($updateQuery);

        if($update->execute()){
            return True;
        }
        return False;
    }
}