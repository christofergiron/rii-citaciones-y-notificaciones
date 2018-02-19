<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class ReseniaFotografica extends Model
{

  use WorkflowTrait;

  protected $table = "resenias_fotograficas";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
