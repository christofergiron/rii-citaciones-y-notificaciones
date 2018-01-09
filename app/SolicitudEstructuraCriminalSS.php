<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudEstructuraCriminalSS extends Model
{
    protected $table = "solicitudesestructurascriminales_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
