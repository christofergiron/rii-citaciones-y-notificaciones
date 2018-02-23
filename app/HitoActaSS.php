<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoActaSS extends Model
{
    protected $fillable = [
      "nombre",
      "descripcion",
      "fecha_inicio",
      "fecha_fin",
      "id_acta"
    ];
    protected $table = "hitos_actas_ss";

    public function acta() {
         return $this->belongsTo(Acta::class, 'id_acta');
    }
}
