<?php

namespace App\Models\Prize;

use App\Models\UserModel;
use FaaPz\PDO\Clause\Conditional;
use FaaPz\PDO\Clause\Join;
use stdClass;

/**
 * Abstract base prize model.
 */
class BasePrizeModel extends \Core\Model
{
    /**
     * Name of table in DB.
     *
     * @var string
     */
    public $tableName = 'prizes';

    /**
     * Current user info.
     *
     * @var stdClass
     */
    public $user;

    /**
     * Constructor of class.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->user = UserModel::getCurrentUser();
    }

    /**
     * Rejects the reserved prize.
     *
     * @param stdClass $prizeData
     * @return bool
     */
    public function rejectPrize(stdClass $prizeData): bool
    {
        $update = $this->db->update([
            'reserve_id' => null,
            'is_reserved' => 0,
            'status' => 2
        ])
            ->table($this->tableName)
            ->where(
                new Conditional("reserve_id", "=", $prizeData->reserve_id)
            );
        return (bool) $update->execute();
    }

    /**
     * Accepts the reserved prize.
     *
     * @param stdClass $prizeData
     * @return bool|array
     */
    public function acceptPrize(stdClass $prizeData)
    {
        $update = $this->db->update([
            'is_reserved' => 0,
            'status' => 3
        ])
            ->table($this->tableName)
            ->where(
                new Conditional("reserve_id", "=", $prizeData->reserve_id)
            );

        $prize = $this->getByReserveID($prizeData->reserve_id);
        if ($update->execute()){
            return $prize;
        }
        return false;
    }

    /**
     * Return the prize by reserve_id.
     *
     * @param string $reserve_id
     * @return bool|array
     */
    public function getByReserveID(string $reserve_id){

        $select = "SELECT p.id as prize_id, p.prize_type, p.quantity, 
            p.receiver_id, p.reserve_id, ss.id as subject_id, ss.name as subject_name,
            u.id as user_id, u.email, u.role_id, u.name as user_name, u.surname 
            FROM `" . $this->tableName . "` as p " .
            "LEFT JOIN `subjects_store` as ss ON p.reserve_id = ss.reserve_id
            LEFT JOIN `user` as u ON p.receiver_id = u.id" .
            " WHERE p.reserve_id='" . $reserve_id . "'";

        $stmt = $this->db->prepare($select);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Return the accepted prizes list.
     *
     * @return bool|array
     */
    public function getAcceptedPrizesList(){

        $select = "SELECT p.id as prize_id, p.prize_type, p.quantity, 
            p.receiver_id, p.reserve_id, ss.id as subject_id, ss.name as subject_name,
            u.id as user_id, u.email, u.role_id, u.name as user_name, u.surname
            FROM `" . $this->tableName . "` as p
            LEFT JOIN `subjects_store` as ss ON p.reserve_id = ss.reserve_id
            LEFT JOIN `user` as u ON p.receiver_id = u.id
            WHERE status=3;";
        $stmt = $this->db->prepare($select);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}