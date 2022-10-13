<?php

namespace App\Models\Prize;

use FaaPz\PDO\Clause\Conditional;

/**
 * Subject Prize Store model.
 */
trait SubjectPrizeStoreModel
{
    public $storeTableName = 'subjects_store';
    /**
     * Reserves the prize in the Subject prize store.
     *
     * @param array $prizeData
     * @return bool|array
     */
    public function reserveSubjectInStore(array $prizeData)
    {
        $updateQuery = "UPDATE " . $this->storeTableName
            . " SET reserve_id='" . $prizeData['reserve_id'] . "',"
            . " is_reserved=1"
            . " WHERE is_reserved=0 AND reserve_id is NULL
            ORDER BY rand() LIMIT 1";
        $update = $this->db->prepare($updateQuery);

        if($update->execute()){
            $select = $this->db->select()
                ->from($this->storeTableName)
                ->where(new Conditional('reserve_id', '=', $prizeData['reserve_id']));
            $stmt = $select->execute();
            $selectedPrize = $stmt->fetch();
        }else{
            $selectedPrize = null;
        };

        return $selectedPrize;
    }

    /**
     * Revokes the reserved subject prize.
     *
     * @param array $prizeData
     * @return bool
     */
    public function revokeSubjectPrize(array $prizeData): bool
    {
        $updateQuery = "UPDATE " . $this->storeTableName
            . " SET reserve_id=NULL,"
            . " is_reserved=0"
            . " WHERE is_reserved=1 AND reserve_id='"
            . $prizeData['reserve_id'] . "'";

        $update = $this->db->prepare($updateQuery);
        return (bool) $update->execute();
    }
}