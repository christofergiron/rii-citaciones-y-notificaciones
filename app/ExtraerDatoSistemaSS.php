<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraerDatoSistemaSS extends Model
{
    protected $table = "extraerdatosSistemas_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
