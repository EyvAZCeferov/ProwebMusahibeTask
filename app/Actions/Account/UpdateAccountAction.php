<?php

namespace App\Actions\Account;

use App\Actions\UpdateAction;
use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UpdateAccountAction implements UpdateAction
{
    public function execute(Model $model, array $data): Account
    {
        $data['code'] = Str::uuid();


        if (isset($data['daily_transaction_limit'])) {
            $data['settings'] = [
                'daily_transaction_limit' => $data['daily_transaction_limit'] ?? 1000,
            ];

            unset($data['daily_transaction_limit']);
        }

        $model->update($data);
        return $model->fresh();
    }
}
