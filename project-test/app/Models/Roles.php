<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'id',
        'name',
        'description',
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
