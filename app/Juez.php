<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Juez extends Model
{
  use WorkflowTrait;

  protected $table = "jueces";

  protected $fillable = ['id', 'numero_sala'];

  public function persona(){
      return $this->morphOne(PersonaNaturalPJ::class, 'personable');
  }

  public function asignable(){
      return $this->morphTo();
  }

  public function asignaciones(){
      return $this->hasMany(AsignacionJuez::class);
  }

  public function dependencia()
  {
      return $this->hasMany(Dependencia::class);
  }

}
