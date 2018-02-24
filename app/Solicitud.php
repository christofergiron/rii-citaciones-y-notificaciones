<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
  protected $fillable = [
    "fecha",
    "titulo",
    "numero_oficio",
    "institucion",
    "solicitado_por",
    "descripcion"
  ];
  protected $table = "solicitudes";

  public function anexo(){
       return $this->morphOne(Anexo::class, 'anexable');
   }

  public function solicitable(){
     return $this->morphTo();
  }

  public function funcionario()
  {
      return $this->belongsTo(Funcionario::class, 'id_solicitante');
  }

  public function hitos_ss() //solicitud
  {
      return $this->hasMany(HitoSolicitudSS::class, 'id_solicitud');
  }

  public function hitos_informe_ss()
  {
      return $this->hasMany(HitoInformeSS::class, 'id_informe');
  }

  public function hitos_dictamen_ss()
  {
      return $this->hasMany(HitoDictamenSS::class, 'id_dictamen');
  }

  public function hitos_actas_ss()
  {
      return $this->hasMany(HitoActaSS::class, 'id_acta');
  }

  public function hitos_reporte_ss()
  {
      return $this->hasMany(HitoReporteSS::class, 'id_reporte');
  }
}
