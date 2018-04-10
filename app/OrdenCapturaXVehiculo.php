<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class OrdenCapturaXVehiculo extends Model
{
  use WorkflowTrait;

      protected $fillable = [
        "id_orden_captura",
        "id_vehiculo",
        "direccion",
        "motivo"
      ];
protected $table = "ordenes_capturas_x_vehiculos";

  public function orden_captura()
  {
        return $this->belongsTo(OrdenCaptura::class, 'id_orden_captura');
    }

  public function vehiculo()
  {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }
}
