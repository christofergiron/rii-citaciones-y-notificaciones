<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Documento extends Model
{

  use WorkflowTrait;
  protected $attributes =  ['titulo'=>null,
                            'workflow_state'=>null,
                            'descripcion'=>null,
                            'fecha_documento'=>null,
                            'tags' => null,
                            'otra_procedencia' => null    ,
                            'funcionario_id' => 1         
                          ];
  protected $fillable = [
    "numero_documento",
    "expediente_id",
    "institucion_id",
    "dependencia_id",
    "titulo",
    "expediente_id",
    "descripcion",
    "tamano",
    "tags",
    "fecha_documento",
    "workflow_state",
    "otra_procedencia",
    "funcionario_id",
    "hora_recepcion"
  ];


  protected $casts = [
      'tags' => 'array',
  ];

  public function documentable(){
      return $this->morphTo();
  }

  public function tipoable(){
      return $this->morphTo();
  }

  public function expediente()
  {
      return $this->belongsTo(Expediente::class);
  }


  public function lugares()
  {
      return $this->hasOne(Lugar::class);
  }

  public function institucion()
  {
      return $this->belongsTo(Institucion::class);
  }

  public function funcionario_asignado()
  {
      return $this->belongsTo(Funcionario::class);
  }

  public function dependencia()
  {
      return $this->belongsTo(Dependencia::class);
  }
}
