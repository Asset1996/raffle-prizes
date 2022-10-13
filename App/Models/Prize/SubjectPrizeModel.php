<?php

namespace App\Models\Prize;

use App\Models\UserModel;
use FaaPz\PDO\Clause\Conditional;
use App\Models\UsersPrizesModel;
use App\Models\Prize\PrizeInterface;
use App\Models\Prize\SubjectPrizeStoreModel;

/**
 * Subject Prize model.
 */
class SubjectPrizeModel extends \App\Models\Prize\BasePrizeModel
    implements PrizeInterface
{
    use SubjectPrizeStoreModel;

    /**
     * Creates the Subject prize record.
     *
     * @param array $prizeData
     * @return bool|int
     */
    public function createRecord(array $prizeData)
    {
        $prizeFromStore = $this->reserveSubjectInStore($prizeData);
        if (!$prizeFromStore){
            echo json_encode([
                'status' => false,
                'message' => 'No more subject prize in Subject prize store.',
            ]);
            die();
        }
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
}