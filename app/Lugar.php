<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
  protected $table = "lugares";
  protected $attributes = ['descripcion'=>null, 'caracteristicas'=>null];

  protected $fillable = [
    "documento_id",
    "descripcion",
    "caracteristicas",
  ];

  public function institucionable(){
      return $this->morphTo();
  }

  public function documento()
  {
      return $this->belongsTo(Documento::class);
  }

  public function hechos()
  {
      return $this->hasMany(Hecho::class);
  }

}
