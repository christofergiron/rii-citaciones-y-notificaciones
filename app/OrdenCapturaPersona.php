<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class OrdenCapturaPersona extends Model
{
  use WorkflowTrait;

    protected $fillable = [
      "observaciones",
      "workflow_state"
    ];
  protected $table = "ordenes_capturas_personas";

public function orden()
{
    return $this->morphOne(OrdenCaptura::class, 'ordenable');
  }

//si la relacion aqui es con imputado, en ese caso seria con rol la relacion
  public function personas()
  {
      return $this->hasMany(OrdenCapturaPersonaNatural::class, "id_orden_captura");
  }
}
