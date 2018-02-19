<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
  protected $table = "solicitudes";

  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }
   
  public function solicitable(){
     return $this->morphTo();
  }

  public function funcionario()
  {
      return $this->belongsTo(Funcionario::class, 'id_solicitante');
  }

  public function hitos_ss()
  {
      return $this->hasMany(HitoSS::class, 'id_documento');
  }
}
