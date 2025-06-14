<?php

namespace App\Models\Transactions;

use App\Models\AtmBanknote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetails extends Model
{
    protected $table = 'transaction_details';
    protected $fillable = [
        'transaction_id',
        'atm_banknote_id',
        'quantity',
    ];
    protected $casts = [
        'transaction_id' => 'integer',
        'atm_banknote_id' => 'integer',
        'quantity' => 'integer',
    ];
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    public function atmBanknote(): BelongsTo
    {
        return $this->belongsTo(AtmBanknote::class, 'atm_banknote_id');
    }
}
