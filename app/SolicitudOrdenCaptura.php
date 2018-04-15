<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SolicitudOrdenCaptura extends Model
{
  use WorkflowTrait;

protected $table = "solicitudes_ordenes_capturas";
protected $fillable = [
    "id_orden_captura", "id_expediente", "id_persona", "fecha_aprovacion", "fecha_rechazo",
    "razon_rechazo", "workflow_state", "motivo"
  ];

  public function solicitud()
  {
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function orden_captura()
    {
          return $this->hasOne(OrdenCaptura::class, 'id_orden_captura');
      }
}
