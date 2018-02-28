<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuncionarioMP extends Model
{

  protected $table = "funcionarios_mp";
  public function institucion(){
      return $this->morphOne(Funcionario::class, 'institucionable');
  }
}
