<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReconocimientoRuedaPersona extends Model
{

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
