<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class TestigoNotificacion extends Model

{
  use WorkflowTrait;
protected $table = "testigos_notificaciones";

protected $fillable = [
  "id_notificacion",
  "id_testigo"
];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }

  public function testigo()
{
      return $this->belongsTo(Delito::class, 'id_testigo');
  }
}
