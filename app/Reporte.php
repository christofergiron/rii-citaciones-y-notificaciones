<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function hitos_ss()
   {
       return $this->hasMany(HitoSS::class, 'id_documento');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }

   public function reportable(){
      return $this->morphTo();
   }
}
