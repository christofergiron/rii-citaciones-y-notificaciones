<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeURIDSS extends Model
{
    protected $table = "informesurid_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }
}
