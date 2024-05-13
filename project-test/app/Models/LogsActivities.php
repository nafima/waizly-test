<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsActivities extends Model
{
    use HasFactory;
    protected $table = 'logs_activities';

    protected $fillable = [
        'user_id',
        'activity_id',
        'description',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'version',
        'os',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $primaryKey = 'id';
    public $keyType = 'uuid';
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
];
}
