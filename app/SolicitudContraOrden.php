<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SolicitudContraOrden extends Model
{
use WorkflowTrait;

  protected $table = "solicitudes_contra_ordenes";
  protected $fillable = [
      "id_orden_captura", "id_expediente", "id_persona", "id_contra_orden", "fecha_aprovacion",
      "fecha_rechazo", "razon_rechazo", "workflow_state", "motivo", "id_juez", "id_fiscal"
    ];

  public function solicitud()
  {
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function contra_orden()
    {
          return $this->hasOne(ContraOrdenCaptura::class, 'id_contra_orden');
      }
}
