<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Denunciante extends Model
{

  protected $fillable = [
  	"denuncia_id"
  ];
  
  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }
}
