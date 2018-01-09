<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalizarInformacionCriminal extends Model
{
  protected $table = "analizar_informaciones_criminales";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
