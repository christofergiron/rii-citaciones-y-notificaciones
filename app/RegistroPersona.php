<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class RegistroPersona extends Model
{

  use WorkflowTrait;

  protected $table = "analizar_informaciones_criminales";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'id_denuncia');
    }

    public function evidencias(){
        return $this->hasMany(Evidencia::class, 'id_escena_delito');
    }
}
