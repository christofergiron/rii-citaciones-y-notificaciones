<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeDelitoContraVidaSS extends Model
{
    protected $table = "informesdelitoscontravida_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
