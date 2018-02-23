<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
  // public function institucionable(){
  //     return $this->morphTo();
  // }

  protected $fillable = [
    "denuncia_id"
  ];

  public function anexable(){
     return $this->morphTo();
  }

  public function documento(){
      return $this->morphOne(Documento::class, 'documentable');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }

}
