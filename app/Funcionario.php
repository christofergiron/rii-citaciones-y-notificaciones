<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{

  protected $attributes = ['dependencia_id'=>null];
  protected $fillable = [
      'dependencia_id'
  ];

  public function institucionable(){
      return $this->morphTo();
  }

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function usuario()
  {
      return $this->hasOne(Usuario::class);
  }

  public function dependencia()
  {
      return $this->belongsTo(Dependencia::class);
  }

  public function receptor_denuncias_ss()
  {
      return $this->hasOne(DenunciaSS::class);
  }

}
