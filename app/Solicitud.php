<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Solicitud extends Model
{
  use WorkflowTrait;
  protected $fillable = [
    "fecha",
    "id_denuncia",
    "titulo",
    "numero_oficio",
    "institucion",
    "solicitado_por",
    "descripcion"
  ];
  protected $table = "solicitudes";

  public function documento(){
    return $this->morphOne(Documento::class, 'documentable');
}

  public function solicitable(){
     return $this->morphTo();
  }

  public function hitos_ss() //solicitud
  {
      return $this->hasMany(HitoSolicitudSS::class, 'id_solicitud');
  }

  public function funcionario()
  {
    return $this->belongsTo(Funcionario::class, 'solicitado_por');
  }

  public function denuncia(){
      return $this->hasOne(DenunciaSS::class, "id_denuncia");
  }

  public function sospechosos() //solicitud
  {
      return $this->hasMany(SospechosoInvestigacionSS::class, 'id_solicitud');
  }

  public function armas() //solicitud
  {
      return $this->hasMany(ArmaSS::class, 'id_solicitud');
  }

}
