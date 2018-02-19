<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapturaFlagrancia extends Model
{
protected $table = "capturas_flagrancias";

    public function captura(){
        return $this->morphOne(Captura::class, 'capturable');
    }

  public function flagrancia()
  {
      return $this->belongsTo(Flagrancia::class, 'id_flagrancia');
  }

}
