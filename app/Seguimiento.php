<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Seguimiento extends Model
{

  use WorkflowTrait;

  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }

  public function expediente()
  {
      return $this->hasMany(Expediente::class, 'id_expediente');
  }
}
