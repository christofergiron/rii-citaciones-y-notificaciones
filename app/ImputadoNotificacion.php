<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class ImputadoNotificacion extends Model
{
  use WorkflowTrait;
protected $table = "imputados_notificaciones";

protected $fillable = [
  "id_notificacion",
  "id_imputado"
];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }

  public function imputado()
{
      return $this->belongsTo(Imputado::class, 'id_imputado');
  }
}
