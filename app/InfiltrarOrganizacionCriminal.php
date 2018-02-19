<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class InfiltrarOrganizacionCriminal extends Model
{

  use WorkflowTrait;

  protected $table = "infiltrar_organizaciones_criminales";

  public function orden()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
}
