<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistroPersona extends Model
{
  protected $table = "analizar_informaciones_criminales";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'id_denuncia');
    }
}
