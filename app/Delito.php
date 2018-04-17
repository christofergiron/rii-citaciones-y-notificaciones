<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delito extends Model
{
  protected $attributes = ['descripcion'=>null];

  public function institucionable(){
      return $this->morphTo();
  }

  public function delitos_atribuidos()
  {
      return $this->hasMany(DelitoAtribuido::class);
  }

  // public function imputados()
  // {
  //     return $this->hasMany(DelitoImputado::class);
  // }

  // public function sospechosos()
  // {
  //     return $this->hasMany(DelitoSospechoso::class);
  // }

  public function fiscalias()
  {
      return $this->hasMany(FiscaliaDelito::class);
  }

  public function sospechoso_investigacion()
  {
      return $this->belongsToMany(SospechosoInvestigacionSS::class, 'delito_sospechoso_ss', 'sospechoso_id', 'delito_id')->withTimestamps();
  }

}
