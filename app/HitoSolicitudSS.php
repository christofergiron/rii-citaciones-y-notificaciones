<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoSolicitudSS extends Model
{
    protected $fillable = [
      "nombre",
      "descripcion",
      "fecha_inicio",
      "fecha_fin",
      "id_solicitud"
    ];
    protected $table = "hitos_solicitudes_ss";

    public function solicitud() {
         return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
