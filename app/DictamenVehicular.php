<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class DictamenVehicular extends Model
{

use WorkflowTrait;

protected $table = "dictamen_vehiculares";

protected $fillable = [
    "workflow_state", "id_solicitud", "informe_adjunto", "informe_html"
  ];

      public function dictamen(){
          return $this->morphOne(Dictamen::class, 'dictamable');
      }

      public function propietario()
      {
          return $this->hasMany(Persona::class, 'dueño');
      }
}
