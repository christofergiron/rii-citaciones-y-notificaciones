<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformePerfilacionCriminalSS extends Model
{
    protected $table = "informesperfilacionescriminales_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
