<?php

namespace App\Models\BaseSettings;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translations extends Model
{
    protected $table = 'translations';
    protected $fillable = [
        'key',
        'value',
        'user_id'
    ];
    protected $casts = [
        'value' => "json",
        'user_id' => "integer"
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
