<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
  protected $fillable = [
    "fecha",
    "id_denuncia",
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

   public function denuncia(){
       return $this->hasOne(DenunciaSS::class, "id_denuncia");
   }

   public function sospechosos() //solicitud
   {
       return $this->hasMany(SospechosoInvestigacionSS::class, 'id_informe');
   }

   public function armas() //solicitud
   {
       return $this->hasMany(ArmaSS::class, 'id_informe');
   }

}
