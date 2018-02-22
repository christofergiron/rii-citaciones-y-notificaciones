<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DenunciaFuenteFormal extends Model
{
  protected $table = "denuncias_fuentes_formales";

  protected $attributes = [
  	'dependencia_id' => null,
  	'delito_id' => null,
  	'unidad_competente'=>null  ,
    'entidad' => null	,
    'unidad_competente_receptor' => null,
    'numero_placa_receptor' => null
  ];

  protected $fillable = [
  	'dependencia_id',
  	'delito_id',
  	'unidad_competente',
    'entidad',
    'unidad_competente_receptor',
    'numero_placa_receptor'
  ];

  public function denuncia(){
      return $this->morphOne(DenunciaSS::class, 'formable');
  }

  public function dependencia()
  {
      return $this->belongsTo(Dependencia::class);
  }

}
