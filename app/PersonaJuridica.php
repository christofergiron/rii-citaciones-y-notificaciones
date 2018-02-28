<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaJuridica extends Model
{
  protected $table = "personas_juridicas";
  protected $attributes = ['tipo_persona_juridica' =>null];

  public function persona(){
      return $this->morphOne(Persona::class, 'personable');
  }
}
