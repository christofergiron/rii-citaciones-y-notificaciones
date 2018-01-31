<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeDelitoComunSS extends Model
{
  protected $table = "informesdelitoscomunes_ss";

  public function informe(){
      return $this->morphOne(Informe::class, 'tipoable');
  }
}
