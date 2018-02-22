<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DenunciaFuenteNoFormal extends Model
{
  protected $table = "denuncias_fuentes_no_formales";
  protected $attributes =  ['fuente_informacion'=>null];

  protected $fillable = [
  	'fuente_informacion'
  ];
  
  protected $casts = [
      'fuente_informacion' => 'array'
  ];

  public function denuncia(){
      return $this->morphOne(DenunciaSS::class, 'formable');
  }

  public function actividades() {
      return $this->hasMany(ActividadConfirmacion::class,'denuncia_fuente_no_formal_id','id');
  }

}
