<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VictimaAgresionSexual extends Model
{
  protected $table = "victima_agresiones_sexuales";

  public function victima(){
      return $this->morphOne(Victima::class, 'tipoable');
  }
}
