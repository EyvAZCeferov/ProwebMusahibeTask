<?php

namespace App\Models;

use App\Models\CurrencyExchanges\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [
        'currency_id',
        'code',
        'balance',
        'status',
        'user_id',
        'created_by',
        'settings'
    ];
    protected $casts = [
        'currency_id' => "integer",
        'balance' => 'decimal:2',
        'status' => "boolean",
        'user_id' => "integer",
        'created_by' => "integer",
        'settings' => "json"
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
