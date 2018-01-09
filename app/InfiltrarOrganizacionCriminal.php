<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfiltrarOrganizacionCriminal extends Model
{
  protected $table = "infiltrar_organizaciones_criminales";

  public function orden()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
}
