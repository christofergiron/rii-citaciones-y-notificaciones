<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeDisciplinarioSS extends Model
{
    protected $table = "informesdisciplinarios_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
