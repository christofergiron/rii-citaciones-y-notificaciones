<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Celular extends Model
{
    protected $table = "celulares";
    protected $fillable= ['marca','modelo','imei'];
}
