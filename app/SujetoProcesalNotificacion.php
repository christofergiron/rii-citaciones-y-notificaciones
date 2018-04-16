<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SujetoProcesalNotificacion extends Model
{
  use WorkflowTrait;
protected $table = "sujetos_procesales_notificaciones";

protected $fillable = [
  "id_notificacion",
  "nombre",
  "tipo"
];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }
}
