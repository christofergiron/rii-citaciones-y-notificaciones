<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class RecepcionarDeclaracion extends Model
{
  use WorkflowTrait;
  protected $table = "recepcionar_declaraciones";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
