<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstandarizacionSS extends Model
{
    protected $table = "estandarizaciones_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
