<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NuevaPromocionSS extends Model
{
    protected $table = "nuevaspromociones_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
