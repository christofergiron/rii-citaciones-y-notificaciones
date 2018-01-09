<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecepcionarDeclaracion extends Model
{
  protected $table = "recepcionar_declaraciones";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
