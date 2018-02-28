<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaNaturalPJ extends Model
{
      protected $table = "personas_naturales_pj";

      public function institucion(){
          return $this->morphOne(PersonaNatural::class, 'institucionable');
      }

}
