<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model
{
    protected $table = 'audit_logs';
    protected $fillable = [
        'request_id',
        'user_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'status_code',
        'latency_ms'
    ];
    protected $casts = [
        'user_id' => "integer",
        'user_agent' => "json",
        'status_code' => "integer",
        'latency_ms' => "integer"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
