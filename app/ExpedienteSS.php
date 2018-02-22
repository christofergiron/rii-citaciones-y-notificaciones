<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpedienteSS extends Model
{
  protected $table = "expedientes_ss";
  protected $attributes = ['numero_expediente'=>null];

  protected $fillable = [
  	"numero_expediente"
  ];
  public function institucion(){
      return $this->morphOne(Expediente::class, 'institucionable');
  }

}
