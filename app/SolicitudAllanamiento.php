<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SolicitudAllanamiento extends Model
{

  use WorkflowTrait;

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function evidencias(){
             return $this->hasMany(Evidencia::class, 'id_allanamiento');
         }
}
