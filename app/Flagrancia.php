<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flagrancia extends Model
{

protected $table = "flagrancias";

protected $fillable = [
    "id_denuncia",
    "workflow_state"
  ];


public function captura(){
    return $this->morphOne(Captura::class, 'capturable');
}

  public function flagrancia()
  {
      return $this->hasMany(CapturaFlagrancia::class, 'id_captura');
  }

  public function denuncia()
  {
      return $this->hasMany(Denuncia::class, 'id_denuncia');
  }

}
