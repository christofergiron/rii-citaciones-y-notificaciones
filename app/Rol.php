<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
  protected $table = "roles";

  protected $fillable = [
    "institucion_id",
    "persona_natural_id"
  ];

  public function rolable(){
      return $this->morphTo();
  }

  public function institucion()
  {
      return $this->belongsTo(Institucion::class);
  }

  public function persona()
  {
      return $this->belongsTo(PersonaNatural::class, 'persona_natural_id');
  }

}
