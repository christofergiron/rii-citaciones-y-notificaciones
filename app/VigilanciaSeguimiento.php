<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VigilanciaSeguimiento extends Model
{

protected $table = "vigilancias_seguimientos";

      public function solicitud(){
          return $this->morphOne(Solicitud::class, 'solicitable');
      }
}
