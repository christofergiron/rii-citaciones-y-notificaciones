<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
  protected $fillable = [
    "fecha",
    "titulo",
    "numero_oficio",
    "institucion",
    "solicitado_por",
    "descripcion"
  ];

  protected $table = "informes";

  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

   public function funcionarioss() {
        return $this->belongsTo(FuncionarioSS::class, 'id_autor');
   }

   public function hitos_informe_ss()
   {
       return $this->hasMany(HitoInformeSS::class, 'id_informe');
   }

   public function tipoable(){
      return $this->morphTo();
   }

}
