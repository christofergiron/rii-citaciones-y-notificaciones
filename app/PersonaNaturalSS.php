<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaNaturalSS extends Model
{
  protected $table = "personas_naturales_ss";
  protected $attributes =  ['residente'=>null];

  protected $fillable = [
  	'residente'
  ];

  public function institucion(){
      return $this->morphOne(PersonaNatural::class, 'institucionable');
  }

  public function direcciones()
  {
      return $this->hasMany(LugarSS::class, 'persona_natural_ss_id', 'id');
  }
}
