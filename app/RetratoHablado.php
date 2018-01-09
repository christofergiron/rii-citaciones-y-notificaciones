<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetratoHablado extends Model
{

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
