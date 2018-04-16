<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Requerimiento extends Model
{
  use WorkflowTrait;
protected $table = "requerimientos";

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
      return $this->HasMany(CanalEnvioCN::class, 'id_requerimiento');
  }

  public function funcionarioPJ()
{
      return $this->HasOne(FuncionarioPJ::class, 'id_funcionario');
  }

}
