<?php

namespace App\Models;

use stdClass;

/**
 * User-Prize connection model.
 */
class UsersPrizesModel extends \Core\Model
{
    /**
     * Name of table in DB.
     *
     * @var string
     */
    public $tableName = 'users_prizes';
    public static $tableNameStatic = 'users_prizes';

    /**
     * Fetches prize from store to users balance.
     *
     * @param stdClass $prizeData
     * @return bool
     */
    public static function addFetchedPrize(stdClass $prizeData): bool
    {
        $currentUser = UserModel::getCurrentUser();
        $statement = "INSERT INTO " . self::$tableNameStatic . " (user_id, prize_id, quantity)
            VALUES (
                " . $currentUser->user_id . ", 
                " . $prizeData['id'] . ", 
                " . $prizeData['selected_quantity'] . ") 
            ON DUPLICATE KEY UPDATE quantity = quantity + " . $prizeData['selected_quantity'];
        $insert = self::$dbStatic->prepare($statement);
        return $insert->execute();
    }
}