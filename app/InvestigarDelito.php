<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class InvestigarDelito extends Model
{

  use WorkflowTrait;

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class, 'id_denuncia');
  }
}
