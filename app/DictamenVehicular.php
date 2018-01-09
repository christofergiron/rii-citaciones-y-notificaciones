<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DictamenVehicular extends Model
{

protected $table = "dictamen_vehiculares";

      public function dictamen(){
          return $this->morphOne(Dictamen::class, 'dictamable');
      }
}
