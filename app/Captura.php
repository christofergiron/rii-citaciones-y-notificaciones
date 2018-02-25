<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Captura extends Model
{
use WorkflowTrait;

  protected $fillable = [
    "workflow_state",
    "id_orden",
    "id_requerimiento",
    "id_expediente",
    "id_lugar",
    "id_funcionario",
    "descripcion_captura",
    "observaciones",
    "fecha_captura"
  ];

    public function capturable(){
       return $this->morphTo();
    }
//esta tiene relacion con la orden de pj
  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }
  //esta tiene relacion con el requerimiento de MP
    public function requerimiento()
    {
        return $this->hasOne(Requerimiento::class, 'id_requerimiento');
    }

  public function expediente()
  {
      return $this->hasOne(Expediente::class, 'id_expediente');
  }

  public function detenidos()
  {
      return $this->hasMany(Detenido::class, 'id_captura');
  }

  public function LugarSS()
  {
      return $this->belongsTo(LugarSS::class, 'id_lugar');
  }

  public function funcionarios() {
       return $this->belongsTo(FuncionarioSS::class, 'id_funcionario');
  }

  public function evidencias(){
           return $this->hasMany(Evidencia::class, 'id_captura');
       }

}
