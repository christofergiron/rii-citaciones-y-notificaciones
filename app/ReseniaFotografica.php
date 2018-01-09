<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReseniaFotografica extends Model
{
  protected $table = "resenias_fotograficas";

    public function solicitud(){
        return $this->morphOne(Solicitud::class, 'solicitable');
    }
}
