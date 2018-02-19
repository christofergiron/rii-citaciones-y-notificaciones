<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeTopografiaForenseSS extends Model
{
  protected $table = "informetopografiaforense_ss";

  public function informe(){
      return $this->morphOne(Informe::class, 'tipoable');
  }
}
