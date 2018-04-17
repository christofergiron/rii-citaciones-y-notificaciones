<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelitoMP extends Model
{
  protected $table = "delitos_mp";

  public function institucion(){
      return $this->morphOne(Delito:class, 'institucionable');
  }
}
