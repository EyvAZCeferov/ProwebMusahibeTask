<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;

interface UpdateAction
{
    public function execute(Model $collect, array $data): Model;
}
