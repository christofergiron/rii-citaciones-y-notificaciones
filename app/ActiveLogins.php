<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveLogins extends Model
{
    protected $fillable = ['email', 'token'];
    protected $table = 'active_logins';
    public $timestamps = false;
}
