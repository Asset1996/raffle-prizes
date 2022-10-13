<?php

namespace App\Models;

use FaaPz\PDO\Clause\Conditional;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

/**
 * User model.
 */
class UserModel extends \Core\Model
{
    /**
     * Name of table in DB.
     *
     * @var string
     */
    public $tableName = 'user';
    public static $tableNameStatic = 'user';

    /**
     * Returns current signed-in user info.
     *
     * @return stdClass|bool
     */
    public static function getCurrentUser()
    {
        $data = json_decode(file_get_contents('php://input'));
        if (!property_exists($data, 'jwt')) {
            return false;
        }
        return JWT::decode(
            $data->jwt,
            new Key($_ENV['JWT_KEY'],$_ENV['JWT_ENCRYPTION'])
        );
    }

    /**
     * Attempt tp authorize user.
     *
     * @param stdClass $decodedData
     * @return array|bool
     */
    public function attemptLogin(stdClass $decodedData)
    {
        $select = $this->db->select()
            ->from($this->tableName)
            ->where(new Conditional(
                'email', '=', $decodedData->email
            ));

        $stmt = $select->execute();
        $userData = $stmt->fetch();

        if (!$userData) {
            return false;
        }

        return $userData;
    }
}