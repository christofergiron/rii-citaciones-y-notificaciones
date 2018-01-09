<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeCuadroEstadisticoSS extends Model
{
    protected $table = "informescuadrosestadisticos_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
