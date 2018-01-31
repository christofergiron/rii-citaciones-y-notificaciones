<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
  protected $table = "solicitudes";

  public function hitos_ss()
  {
      return $this->hasMany(HitoSS::class, 'id_hito');
  }

  public function funcionario()
  {
      return $this->belongsTo(Funcionario::class, 'id_solicitante');
  }
}
