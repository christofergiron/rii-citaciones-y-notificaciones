<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class SospechosoInvestigacionSS extends Model
{
  use WorkflowTrait;
  protected $table = "sospechosos_investigacion_ss";

  protected $fillable = [
    "id_informe",
    "id_solicitud",
    "alias",
    "otros_nombres",
    "caracteristicas",
    "forma_cara",
    "contextura",
    "tono_voz",
    "discapacidad",
    "peso",
    "estatura",
    "tipo_sangre",
    "cicatrices",
    "zona",
    "descripcion_zona"
  ];

  public function delitos()
  {
      return $this->belongsToMany(Delito::class, 'delito_sospechoso_ss', 'sospechoso_id', 'delito_id')->withTimestamps();
  }
}
