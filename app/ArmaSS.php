<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class ArmaSS extends Model
{
  use WorkflowTrait;
  protected $table = "armas_ss";

  protected $fillable = [
    "id_tipo_arma",
    "id_sospechoso_investigacion",
    "descripcion",
    "calibre",
    "modelo",
    "nombre",
    "serial",
    "marca"
  ];

  public function tipoarma()
  {
      return $this->belongsTo(TipoArmaSS::class, 'id_tipo_arma');
  }
}
