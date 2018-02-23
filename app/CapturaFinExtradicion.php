<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class CapturaFinExtradicion extends Model
{

use WorkflowTrait;

protected $table = "captura_fines_extradicion";

protected $fillable = [
  "workflow_state",
  "id_nota_roja"
];

protected $table = "captura_fines_extradicion";

public function captura(){
    return $this->morphOne(Captura::class, 'capturable');
}

  public function nota_roja()
  {
      return $this->belongsTo(NotaRoja::class, 'id_nota_rota');
  }
}
