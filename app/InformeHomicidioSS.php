<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeHomicidioSS extends Model
{
    protected $table = "informeshomicidios_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
