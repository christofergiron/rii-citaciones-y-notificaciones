<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudRecordHistoriales extends Model
{
    protected $table = "solicitudesesrecordshistoriales_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
