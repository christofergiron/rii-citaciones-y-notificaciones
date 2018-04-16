<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Emplazamiento extends Model
{
  use WorkflowTrait;
protected $table = "emplazamientos";

protected $fillable = [
  "id_expediente",
  "id_funcionario",
  "organo_juridiccional",
  "fecha_creacion",
  "audiencia",
  "etapa",
  "proceso_judicial",
  "parte_solicitante",
  "asunto",
  "tipo_acto_procesal",
  "lugar_citacion",
  "fecha_citacion",
  "observaciones",
  "persona_natural",
  "tipo"
];

  public function documento()
{
  return $this->morphOne(Documento::class, 'documentable');
  }

  public function expediente()
{
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

  public function proceso_judicial()
{
      return $this->belongsTo(ProcesoJudicial::class, 'proceso_judicial');
  }

  public function canales_envio()
{
      return $this->HasMany(CanalEnvioCN::class, 'id_emplazamiento');
  }

  public function funcionarioPJ()
{
      return $this->HasOne(FuncionarioPJ::class, 'id_funcionario');
  }
}
