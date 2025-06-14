<?php

namespace App\Actions\Account;

use App\Models\Account;
use App\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateAccountAction implements CreateAction
{
    public function execute(array $data): Account
    {
        $data['code'] = Str::uuid();

        if (isset($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userId = Auth::id();
        }

        $data['user_id'] = $userId;
        $data['created_by'] = Auth::id();

        $data['settings'] = [
            'daily_transaction_limit' => $data['daily_transaction_limit'] ?? 1000,
        ];

        unset($data['daily_transaction_limit']);

        return Account::create($data);
    }
}
