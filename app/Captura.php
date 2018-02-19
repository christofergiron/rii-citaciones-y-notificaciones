<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Captura extends Model
{
    public function capturable(){
       return $this->morphTo();
    }
//esta tiene relacion con la orden de pj
  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
  //esta tiene relacion con el requerimiento de MP
    public function requerimiento()
    {
        return $this->hasOne(Requerimiento::class, 'id_requerimiento');
    }

  public function expediente()
  {
      return $this->hasOne(Expediente::class, 'id_expediente');
  }

  public function Detenido()
  {
      return $this->hasMany(PersonaNatural::class, 'id_persona');
  }

  public function LugarSS()
  {
      return $this->belongsTo(LugarSS::class, 'id_lugar');
  }

  public function funcionarios() {
       return $this->belongsTo(FuncionarioSS::class, 'id_funcionario');
  }

  public function evidencias(){
           return $this->hasMany(Evidencia::class, 'id_captura');
       }

}
