<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContestacionOficioSS extends Model
{
    protected $table = "contestacionoficios_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
