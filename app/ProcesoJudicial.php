<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class ProcesoJudicial extends Model
{
  protected $table = "procesos_judiciales";

  use WorkflowTrait;

  protected $fillable = [
    'reune_requisitos',
    'presencia_voluntaria',
    'descripcion',
    'institucion_id',
    'recepcionado_id',
    'defensor_id',
    'institucion_id',
    'persona_acusada_id',
    'departamento',
    'requerimiento_fiscal_id',
    'workflow_state'
  ];

  public function expediente(){
      return $this->morphOne(ExpedientePJ::class, 'asignable');
  }

  public function asignable(){
      return $this->morphTo();
  }

  public function asignaciones(){
      return $this->hasMany(AsignacionJuez::class);
  }

  public function sobreseimientos(){
      return $this->hasMany(Sobreseimiento::class);
  }

  public function notificaciones(){
      return $this->hasMany(AutoNotificacion::class);
  }

  public function audienciaDeclaracion()
  {
      return $this->hasOne(AudienciaDeclaracion::class);
  }

  public function audienciaRevision()
  {
      return $this->hasOne(AudienciaRevision::class);
  }

  public function audienciaInicial()
  {
      return $this->hasOne(AudienciaInicial::class);
  }

  public function audienciaPreliminar()
  {
      return $this->hasOne(AudienciaPreliminar::class);
  }

  public function juicio()
  {
      return $this->hasOne(Juicio::class);
  }

  public function recepcionadoPor()
  {
      return $this->hasOne(PersonaNaturalPJ::class, 'recepcionado_id');
  }

  public function secreataria()
  {
      return $this->hasOne(PersonaNaturalPJ::class, 'secretaria_id');
  }

  public function defensor()
  {
      return $this->hasOne(PersonaNaturalPJ::class, 'defensor_id');
  }

  public function acusadorPrivado()
  {
      return $this->hasOne(PersonaNaturalPJ::class, 'acusador_privado_id');
  }

  /*public function institucion()
  {
      return $this->hasOne(InstitucionPJ::class, "institucion_id");
  }*/

  public function acusado()
  {
      return $this->hasOne(PersonaNaturalPJ::class, "persona_acusada_id");
  }

  public function diligencia()
  {
      return $this->hasOne(DiligenciaPrevia::class);
  }

  public function requerimientoFiscal()
  {
      return $this->hasOne(RequerimientoFiscal::class, "requerimiento_fiscal_id");
  }

}
