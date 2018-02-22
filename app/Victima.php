<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Victima extends Model
{
  protected $attributes = ['residente'=>null];

  protected $fillable = [
  	'denuncia_id',
  	'residente'
  ];

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }
}
