<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformeEscenaDelitoSS extends Model
{
    protected $fillable = [
      "workflow_state"
    ];
    protected $table = "informesescenacrimenes_ss";

    public function informe(){
        return $this->morphOne(Informe::class, 'tipoable');
    }

    public function evidencias(){
        return $this->hasMany(Evidencia::class, 'id_escena_delito');
    }
}
