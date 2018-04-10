<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenCapturaEstado extends Model
{
  use WorkflowTrait;

      protected $fillable = [
        "id_orden_captura",
        "id_contra_orden",
        "id_funcionario",
        "estado_antiguo",
        "estado_nuevo",
        "fecha",
        "motivo"
      ];
//los estados son: vigente, cancelada por una contra orden o por la captura del sujeto
//este objeto es unicamente para los cambios de estado, registra el estado antiguo el actual fecha y quien fue
protected $table = "orden_captura_estados";

public function orden_captura()
{
      return $this->belongsTo(OrdenCaptura::class, 'id_orden_captura');
  }

}
