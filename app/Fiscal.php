<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fiscal extends Model
{
  protected $table = "fiscales";

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function fiscalia()
  {
      return $this->belongsTo(Fiscalia::class);
  }

  public function imputados()
  {
      return $this->hasMany(FiscalAsignado::class);
  }

  public function denuncias()
  {
      // return $this->hasMany(Denuncia::class);
      // return $this->hasManyThrough(Denuncia::class, FiscalAsignado::class);
      return $this->belongsToMany(\App\Denuncia::class, 'fiscales_asignados');
  }

  public function casos_asignados()
  {
      return $this->belongsTo(FiscalAsignado::class);
  }

}
