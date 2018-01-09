<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenorDetenido extends Model
{
  protected $table = "menores_detenidos";

  public function detenido(){
      return $this->morphOne(Detenido::class, 'tipoable');
  }
}
