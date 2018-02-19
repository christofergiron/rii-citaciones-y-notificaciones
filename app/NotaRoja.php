<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class NotaRoja extends Model
{

  use WorkflowTrait;

  protected $table = "notas_rojas";

  public function persona()
  {
      return $this->belongsTo(PersonaNatural::class, 'identidad_persona');
  }

  public function captura_fin_extradicion(){
           return $this->hasMany(CapturaFinExtradicion::class, 'id_nota_rota');
       }
}
