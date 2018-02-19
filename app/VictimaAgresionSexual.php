<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class VictimaAgresionSexual extends Model
{

  use WorkflowTrait;

  protected $table = "victima_agresiones_sexuales";

  public function victima(){
      return $this->morphOne(Victima::class, 'tipoable');
  }
}
