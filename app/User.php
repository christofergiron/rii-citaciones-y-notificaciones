<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    private $is_admin;
    private $log;

    public function __construct()
    {
        $this->log = new \Log;
    }

    public $attributes = ['id'=>null, 'name'=>null, 'email'=>null, 'funcionario_id' =>null];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'funcionario_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
