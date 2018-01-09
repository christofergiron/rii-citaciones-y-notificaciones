<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudTransporteAlmacenGeneralSS extends Model
{
    protected $table = "solicitudestransportesalmacengeneral_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
