<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoDigital extends Model
{
  protected $table = "documentos_digitales";
    
  public function documento(){
      return $this->morphOne(Documento::class, 'tipoable');
  }
}
