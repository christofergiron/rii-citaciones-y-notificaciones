<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeEscenaCrimenSS extends Model
{
    protected $table = "informesescenacrimenes_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
