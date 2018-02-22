<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeDelitoComunSS extends Model
{
  protected $fillable = [
    "workflow_state"
  ];

  protected $table = "informesdelitoscomunes_ss";

  public function informe(){
      return $this->morphOne(Informe::class, 'tipoable');
  }
}
