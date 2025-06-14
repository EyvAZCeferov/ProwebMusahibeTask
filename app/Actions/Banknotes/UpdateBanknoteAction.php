<?php

namespace App\Actions\Banknotes;

use App\Actions\UpdateAction;
use App\Models\AtmBanknote;
use Illuminate\Database\Eloquent\Model;

class UpdateBanknoteAction implements UpdateAction
{
    public function execute(Model $banknote, array $data): AtmBanknote
    {
        $banknote->update($data);
        return $banknote->fresh();
    }
}
