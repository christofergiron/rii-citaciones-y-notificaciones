<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudAnalisis extends Model
{

protected $table = "solicitudes_analisis";

  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }
}
