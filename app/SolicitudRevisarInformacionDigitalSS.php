<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudRevisarInformacionDigitalSS extends Model
{
    protected $table = "solicitudesrevisarinformaciondigitales_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
