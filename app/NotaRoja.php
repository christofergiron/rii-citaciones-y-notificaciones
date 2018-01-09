<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaRoja extends Model
{
  protected $table = "notas_rojas";

  public function persona()
  {
      return $this->belongsTo(Persona::class, 'identidad_persona');
  }

  public function captura_fin_extradicion(){
           return $this->hasMany(CapturaFinExtradicion::class, 'id_nota_rota','id');
       }
}
