<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    protected $table = 'users';
    
    protected $fillable = [
        'id',
        'username',
        'fullname',
        'email',
        'password',
        'birthdate',
        'phone',
        'address',
        'avatar',
        'last_ip',
        'status',
        'login_attempt',
        'last_login',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $primaryKey = 'id';
    public $keyType = 'uuid';
    public $incrementing = false;

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }
}
