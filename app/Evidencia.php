<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
  public function lugar() {
       return $this->belongsTo(LugarSS::class, 'id_lugarSS');
  }

//esta relacion es con expediente o con denuncia?
//relacion de 1 a n
  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

  //esta relacion es con persona porque puede pertenecer a cualquier rol, victima, detenido, sospechoso, testigo etc?
  //relacion de 1 a n
  public function persona()
  {
      return $this->belongsTo(Persona::class, 'id_persona');
  }
}
