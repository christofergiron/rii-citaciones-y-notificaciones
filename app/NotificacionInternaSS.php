<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificacionInternaSS extends Model
{
    protected $table = "notificacionesinternas_ss";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
