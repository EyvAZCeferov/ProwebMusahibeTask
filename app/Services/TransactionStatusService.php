<?php

namespace App\Services;

use App\Models\Transactions\TransactionStatuses;

class TransactionStatusService
{
    public function getStatusId(String $key)
    {
        $data = TransactionStatuses::where("code", $key)->select("id")->first();
        return !empty($data) ? $data->id : null;
    }
}
