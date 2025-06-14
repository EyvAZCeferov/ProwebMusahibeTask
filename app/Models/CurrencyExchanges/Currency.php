<?php

namespace App\Models\CurrencyExchanges;

use App\Models\AtmBanknote;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;
    protected $table = 'currencies';
    protected $fillable = [
        'name',
        'symbol',
        'code',
        'status',
        'user_id'
    ];
    protected $casts = [
        'name' => "json",
        'status' => "boolean",
        'user_id' => "integer"
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function banknotes(): HasMany
    {
        return $this->hasMany(AtmBanknote::class, 'currency_id', 'id')->where("status", true);
    }
}
