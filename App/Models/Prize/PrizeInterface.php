<?php

namespace App\Models\Prize;

/**
 * Prize model interface.
 */
interface PrizeInterface
{

    /**
     * Method creates the prize record.
     *
     * @param array $prizeData
     */
    public function createRecord(array $prizeData);

    /**
     * Send prizes:
     * - money          - to bank API,
     * - subject        - to email of user,
     * - loyalty points - to application account.
     *
     * @param array $prizeData
     */
    public function send(array $prizeData);
}