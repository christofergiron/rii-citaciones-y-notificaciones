<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeLogisticoSS extends Model
{
    protected $table = "informeslogisticos_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
