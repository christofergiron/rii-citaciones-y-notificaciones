<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flagrancia extends Model
{

protected $table = "flagrancias";

  public function captura_flagrancia()
  {
      return $this->hasMany(CapturaFlagrancia::class, 'id_captura');
  }

  public function denuncia()
  {
      return $this->hasMany(Denuncia::class, 'id_denuncia');
  }

}
