<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class MenorDetenido extends Model
{

  use WorkflowTrait;

  protected $table = "menores_detenidos";

  public function detenido(){
      return $this->morphOne(Detenido::class, 'tipoable');
  }
}
