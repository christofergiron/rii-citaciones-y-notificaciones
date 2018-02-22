<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeLogisticoSS extends Model
{
    protected $fillable = [
      "workflow_state"
    ];
    protected $table = "informeslogisticos_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
