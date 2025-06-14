<?php

namespace App\Models\CurrencyExchanges;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exchanges extends Model
{
    protected $table = 'exchanges';
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'user_id',
        'rate'
    ];
    protected $casts = [
        'from_currency_id' => "integer",
        'to_currency_id' => "integer",
        'user_id' => "integer",
        'rate' => 'decimal:2',
    ];
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
