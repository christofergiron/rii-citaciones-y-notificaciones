<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcuerdoInternoSS extends Model
{
    protected $table = "acuerdosinternos_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
