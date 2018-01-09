<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlbumFotografico extends Model
{

protected $table = "albumes_fotograficos";

  public function solicitud(){
      return $this->morphOne(Solicitud::class, 'solicitable');
  }
}
