<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class VigilanciaSeguimiento extends Model
{

use WorkflowTrait;

protected $table = "vigilancias_seguimientos";

      public function solicitud(){
          return $this->morphOne(Solicitud::class, 'solicitable');
      }
}
