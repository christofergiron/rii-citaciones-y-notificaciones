<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class DelitoNotificacion extends Model
{
  use WorkflowTrait;
protected $table = "delitos_notificaciones";

protected $fillable = [
  "id_notificacion",
  "id_delito"
];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }

  public function delito()
{
      return $this->belongsTo(Delito::class, 'id_delito');
  }

}
