<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SolicitudAnalisis extends Model
{
use WorkflowTrait;
protected $table = "solicitudes_analisis";

protected $fillable = [
    "id_laboratorio", "nombre_laboratorio", "tipo_analisis", "nombre_analisis", "detalle_analisis", "workflow_state"
  ];

  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }

  public function evidencias(){
           return $this->hasMany(Evidencia::class, 'id_solicitud');
       }
}
