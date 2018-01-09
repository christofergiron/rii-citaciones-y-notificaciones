<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntervencionComunicacion extends Model
{

protected $table = "intervenciones_comunicaciones";

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class, 'id_denuncia');
  }

  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }
}
