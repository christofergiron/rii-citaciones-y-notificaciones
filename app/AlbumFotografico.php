<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class AlbumFotografico extends Model
{

use WorkflowTrait;

protected $table = "albumes_fotograficos";

  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }
}
