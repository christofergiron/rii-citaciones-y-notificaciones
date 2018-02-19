<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class RegistroArma extends Model
{

  use WorkflowTrait;

  protected $table = "registros_armas";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
