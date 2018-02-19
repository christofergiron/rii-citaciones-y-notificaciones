<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class CotejamientoDactilar extends Model
{

use WorkflowTrait;

protected $table = "cotejamientos_dactilares";

  public function detenido()
  {
      return $this->belongsTo(Detenido::class, 'id_detenido');
  }
}
