<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class OrdenCapturaVehiculo extends Model
{
  use WorkflowTrait;

    protected $fillable = [
      "observaciones",
      "workflow_state"
    ];
  protected $table = "ordenes_capturas_vehiculos";

  public function orden()
  {
      return $this->morphOne(OrdenCaptura::class, 'ordenable');
    }

    public function vehiculo()
    {
        return $this->hasMany(OrdenCapturaXVehiculo::class, "id_orden_captura");
    }
}
