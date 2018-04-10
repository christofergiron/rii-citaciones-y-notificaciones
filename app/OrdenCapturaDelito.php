<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class OrdenCapturaDelito extends Model
{
  use WorkflowTrait;

      protected $fillable = [
        "id_orden_captura",
        "tipo_delito",
        "delito",
        "id_victima",
        "perjudicado",
        "descripcion"
      ];
protected $table = "ordenes_capturas_delitos";

  public function orden_captura()
  {
        return $this->belongsTo(OrdenCaptura::class, 'id_orden_captura');
    }

  //public function delitos()
  //{
  //      return $this->belongsTo(Delito::class, 'id_delito');
  //  }
}
