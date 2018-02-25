<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function hitos_reporte_ss()
   {
       return $this->hasMany(HitoReporteSS::class, 'id_reporte');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }

   public function reportable(){
      return $this->morphTo();
   }
}
