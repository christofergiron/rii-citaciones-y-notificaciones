<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sospechoso extends Model
{
  protected $attributes = ['detenido'=>null, 'remitido'=>null];

  protected $fillable = [
      "denuncia_id",
      "remitido",
      "detenido"
  ];

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }

  public function fiscales()
  {
      return $this->hasMany(FiscalAsignado::class);
  }

  public function fiscalias()
  {
      return $this->hasMany(FiscaliaAsignada::class);
  }

  public function delitos()
  {
      return $this->hasMany(DelitoAtribuido::class);
  }

}
