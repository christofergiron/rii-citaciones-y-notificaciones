<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flagrancia extends Model
{

protected $table = "captura_flagrancia";

  public function captura()
  {
      return $this->hasMany(Captura::class, 'id_captura');
  }

  public function denuncia()
  {
      return $this->hasMany(Denuncia::class, 'id_denuncia');
  }

}
