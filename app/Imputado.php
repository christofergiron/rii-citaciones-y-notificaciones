<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imputado extends Model
{
  protected $attributes = ['condiciones'=>null, 'moviles'=>null, 'objetos'=>null, 'transportes'=>null, 'armas'=>null];

  protected $casts = [
      'moviles' => 'array',
      'objetos' => 'array',
      'transportes' => 'array',
      'armas' => 'array',
      'condiciones' => 'array'
  ];

  protected $fillable = [
    "denuncia_id",
    "moviles",
    "objetos",
    "transportes",
    "condiciones",
    "armas"
  ];

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }

  public function fiscales()
  {
      return $this->hasMany(FiscalAsignado::class);
  }

  public function fiscalias()
  {
      return $this->hasMany(FiscaliaAsignada::class);
  }

  public function delitos()
  {
      return $this->hasMany(DelitoAtribuido::class);
  }

  public function relaciones() {
      return $this->hasMany(RelacionesImputado::class);
  }
  
}
