<?php

namespace App\Models\Transactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionStatuses extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_statuses';
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'code'
    ];
    protected $casts = [
        'user_id' => "integer",
        'name' => "json",
        'status=>"boolean'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
