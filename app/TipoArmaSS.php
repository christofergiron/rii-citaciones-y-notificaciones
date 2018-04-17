<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class TipoArmaSS extends Model
{
  use WorkflowTrait;
  protected $table = "tipo_armas_ss";

  protected $fillable = [
    "descripcion"
  ];

  public function armas()
  {
      return $this->hasMany(ArmaSS::class, 'id_tipo_arma');
  }
}
