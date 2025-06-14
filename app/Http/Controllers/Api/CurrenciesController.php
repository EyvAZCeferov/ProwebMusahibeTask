<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\CurrencyExchanges\Currency;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    public function currencies(){
        $data = Currency::with(['banknotes', 'user'])->latest()->paginate(20);
        return CurrencyResource::collection($data);
    }
}
