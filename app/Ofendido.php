<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ofendido extends Model
{
  protected $fillable = ["denuncia_id"];
  public function tipoable(){
      return $this->morphTo();
  }

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }
}
