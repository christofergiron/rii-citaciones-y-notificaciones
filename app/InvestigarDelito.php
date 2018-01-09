<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvestigarDelito extends Model
{
  
  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class, 'id_denuncia');
  }
}
