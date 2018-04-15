<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fiscalia extends Model
{

  public function dependencia()
  {
      return $this->belongsTo(Dependencia::class);
  }

  public function fiscales()
  {
      return $this->hasMany(Fiscal::class);
  }

  public function fiscales_asignados()
  {
      return $this->hasMany(FiscalAsignado::class);
  }

  public function delitos()
  {
      return $this->hasMany(FiscaliaDelito::class);
  }

  public function denuncias()
  {
      return $this->hasMany(Denuncia::class);
  }
}
