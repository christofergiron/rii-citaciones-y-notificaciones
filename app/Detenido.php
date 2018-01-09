<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detenido extends Model
{
  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

//y los casos en flagrancia?
  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

//y los casos en flagrancia?
  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }

  public function lugarSS()
  {
      return $this->belongsTo(LugarSS::class, 'lugar_captura');
  }
}
