<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'is_superadmin'];

    protected $casts = ['password' => 'hashed', 'is_superadmin' => 'boolean'];

    protected $hidden = ['password', 'remember_token'];
}
