<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dictamen extends Model
{
  protected $table = "dictamenes";

  public function dictamable(){
     return $this->morphTo();
  }

  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }

   public function hitos_dictamen_ss()
   {
       return $this->hasMany(HitoDictamenSS::class, 'id_dictamen');
   }

}
