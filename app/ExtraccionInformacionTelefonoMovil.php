<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class ExtraccionInformacionTelefonoMovil extends Model
{

use WorkflowTrait;

protected $table = "extraccion_informacion_telefonos_moviles";

  public function orden()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
}
