<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }

  public function denuncias()
  {
      return $this->hasMany(Denuncia::class, 'id_denuncia');
  }
}
