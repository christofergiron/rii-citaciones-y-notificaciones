<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class DictamenVehicular extends Model
{

use WorkflowTrait;

protected $table = "dictamen_vehiculares";

      public function dictamen(){
          return $this->morphOne(Dictamen::class, 'dictamable');
      }

      public function propietario()
      {
          return $this->hasMany(Persona::class, 'dueño');
      }
}
