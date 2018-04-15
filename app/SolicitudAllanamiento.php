<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SolicitudAllanamiento extends Model
{
  use WorkflowTrait;
  protected $fillable = [
      "workflow_state", "descripcion", "numero_evidencias_encontradas", "descripcion_evidencias"
    ];

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function evidencias(){
             return $this->hasMany(Evidencia::class, 'id_allanamiento');
         }
}
