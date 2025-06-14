<?php

namespace App\Models\Transactions;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $table = 'transactions';
    protected $fillable = [
        'code',
        'user_id',
        'account_id',
        'amount',
        'transaction_status_id',
        'notes',
        'additional_data',
    ];
    protected $casts = [
        'user_id' => "integer",
        'account_id' => "integer",
        'amount' => "decimal:2",
        'transaction_status_id' => "integer",
        'additional_data' => "json",
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function transaction_status(): BelongsTo
    {
        return $this->belongsTo(TransactionStatuses::class, 'transaction_status_id');
    }
    public function transaction_details(): HasMany
    {
        return $this->hasMany(TransactionDetails::class, 'transaction_id');
    }
}
