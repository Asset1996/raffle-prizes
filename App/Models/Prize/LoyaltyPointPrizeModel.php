<?php

namespace App\Models\Prize;

use App\Models\UserModel;
use FaaPz\PDO\Clause\Conditional;
use App\Models\UsersPrizesModel;
use App\Models\Prize\PrizeInterface;
use stdClass;

/**
 * Loyalty Points Prize model.
 */
class LoyaltyPointPrizeModel extends \App\Models\Prize\BasePrizeModel
    implements PrizeInterface
{
    /**
     * Creates the loyalty points prize record.
     *
     * @param array $prizeData
     * @return bool|int
     */
    public function createRecord(array $prizeData)
    {
        $insert = $this->db
            ->insert([
                'prize_type',
                'quantity',
                'receiver_id',
                'is_reserved',
                'reserve_id'
            ])
            ->into($this->tableName)
            ->values(
                $prizeData['id'],
                $prizeData['selected_quantity'],
                $this->user->user_id,
                1,
                $prizeData['reserve_id']
            );

        if (!$insert->execute()) {
            return False;
        }
        return (int) $this->db->lastInsertId();
    }

    /**
     * Sends loyalty points to application account.
     *
     * @param array $prizeData
     * @return bool|int
     */
    public function send(array $prizeData){

    }
}