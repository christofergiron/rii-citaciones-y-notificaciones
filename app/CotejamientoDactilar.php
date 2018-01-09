<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CotejamientoDactilar extends Model
{

protected $table = "cotejamientos_dactilares";

  public function detenido()
  {
      return $this->belongsTo(Detenido::class, 'id_detenido');
  }
}
