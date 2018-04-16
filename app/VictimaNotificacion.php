<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class VictimaNotificacion extends Model
{
  use WorkflowTrait;
protected $table = "victimas_notificaciones";

protected $fillable = [
  "id_notificacion",
  "id_victima"
];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }

  public function victima()
{
      return $this->belongsTo(Victima::class, 'id_victima');
  }
}
