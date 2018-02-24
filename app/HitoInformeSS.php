<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoInformeSS extends Model
{
    protected $fillable = [
      "nombre",
      "descripcion",
      "fecha_inicio",
      "fecha_fin",
      "id_informe"
    ];

    protected $table = "hitos_informes_ss";

    public function informe() {
         return $this->belongsTo(Informe::class, 'id_informe');
    }
}
