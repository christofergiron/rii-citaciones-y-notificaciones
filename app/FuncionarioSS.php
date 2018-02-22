<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuncionarioSS extends Model
{
  protected $table = "funcionarios_ss";
  protected $attributes = [
      'placa' => null,
      'departamento_ss_id' => null,
      'unidad_ss_id' => null,
      'seccion_ss_id' => null
  ];

  protected $fillable = [
      'placa',
      'departamento_ss_id',
      'unidad_ss_id',
      'seccion_ss_id'
  ];
  
  public function institucion(){
      return $this->morphOne(Funcionario::class, 'institucionable');
  }

  public function departamento()
  {
      return $this->belongsTo(Dependencia::class,'departamento_ss_id', 'id');
  }

  public function unidad()
  {
      return $this->belongsTo(Dependencia::class,'unidad_ss_id', 'id');
  }

  public function seccion()
  {
      return $this->belongsTo(Dependencia::class,'seccion_ss_id', 'id');
  }

}
