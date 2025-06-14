<?php

namespace App\Actions\Banknotes;

use App\Models\AtmBanknote;
use App\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;

class CreateBanknoteAction implements CreateAction
{
    public function execute(array $data): AtmBanknote
    {
        $data['user_id'] = Auth::id();
        return AtmBanknote::create($data);
    }
}
