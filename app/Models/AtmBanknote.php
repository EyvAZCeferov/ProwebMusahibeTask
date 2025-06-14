<?php

namespace App\Models;

use App\Models\CurrencyExchanges\Currency;
use App\Models\Transactions\TransactionDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtmBanknote extends Model
{
    use SoftDeletes;
    protected $table = 'atm_banknotes';
    protected $fillable = [
        'currency_id',
        'name',
        'quantity',
        'status',
        'user_id'
    ];
    protected $casts = [
        'currency_id' => "integer",
        'name' => "integer",
        'quantity' => "integer",
        'status' => "boolean",
        'user_id' => "integer"
    ];
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function transactions()
    {
        return $this->hasMany(TransactionDetails::class, 'atm_banknote_id', 'id');
    }
}
