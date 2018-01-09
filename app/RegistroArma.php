<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistroArma extends Model
{
  protected $table = "registros_armas";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
