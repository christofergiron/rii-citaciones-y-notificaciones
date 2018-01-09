<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dictamen extends Model
{
  protected $table = "dictamenes";
  
  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }
}
