<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpedientePJ extends Model
{
  protected $table = "expedientes_pj";
  protected $attributes = ['numero_expediente'=>null];
  protected $fillable = [
  	"numero_expediente"
  ];

  public function institucion(){
      return $this->morphOne(Expediente::class, 'institucionable');
  }

}
