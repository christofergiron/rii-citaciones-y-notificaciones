<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudReveladoFotografiaSS extends Model
{
    protected $table = "solicitudesreveladofotografias_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
