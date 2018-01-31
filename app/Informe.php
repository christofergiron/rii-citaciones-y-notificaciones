<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }

   public function hitos_ss()
   {
       return $this->hasMany(HitoSS::class, 'id_hito');
   }
}
