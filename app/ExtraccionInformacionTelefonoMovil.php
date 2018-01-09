<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraccionInformacionTelefonoMovil extends Model
{

protected $table = "extraccion_informacion_telefonos_moviles";

  public function orden()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
}
