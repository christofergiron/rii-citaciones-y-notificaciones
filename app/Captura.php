<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Captura extends Model
{
  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }

  public function denuncias()
  {
      return $this->hasMany(Denuncia::class, 'id_denuncia');
  }

  public function requerimiento()
  {
      return $this->hasOne(Requerimiento::class, 'id_requerimiento');
  }
}
