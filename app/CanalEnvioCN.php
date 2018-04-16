<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class CanalEnvioCN extends Model
{
  use WorkflowTrait;
  protected $table = "canales_envio";

    protected $fillable = [
      "id_citacion",
      "id_notificacion",
      "id_emplazamiento",
      "id_requerimiento",
      "canal_envio",
      "medios_envio"
    ];

  public function notificacion()
{
      return $this->belongsTo(Notificacion::class, 'id_notificacion');
  }

  public function citaciones()
  {
        return $this->belongsTo(Citacion::class, 'id_citacion');
    }

  public function emplazamiento()
{
      return $this->belongsTo(Emplazamiento::class, 'id_emplazamiento');
  }

 public function requerimiento()
{
      return $this->belongsTo(Requerimiento::class, 'id_requerimiento');
  }
}
