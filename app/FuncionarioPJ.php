<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuncionarioPJ extends Model
{

  protected $table = "funcionarios_pj";
  public function institucion(){
      return $this->morphOne(Funcionario::class, 'institucionable');
  }
}
