<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detenido extends Model
{

  public function tipoable(){
     return $this->morphTo();
  }

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }

  public function cotejamiento_dactilar(){
           return $this->hasOne(CotejamientoDactilar::class, 'id_cotejamiento');
       }
}
