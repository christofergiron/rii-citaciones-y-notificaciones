<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapturaFinExtradicion extends Model
{

protected $table = "captura_fines_extradicion";

  public function orden_captura()
  {
      return $this->belongsTo(Orden::class, 'id_orden_captura');
  }

  public function nota_roja()
  {
      return $this->belongsTo(NotaRoja::class, 'id_nota_rota');
  }
}
