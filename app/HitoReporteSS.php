<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoReporteSS extends Model
{
    protected $fillable = [
      "nombre",
      "descripcion",
      "fecha_inicio",
      "fecha_fin",
      "id_reporte"
    ];
    protected $table = "hitos_reportes_ss";


  public function reporte() {
       return $this->belongsTo(Reporte::class, 'id_reporte');
  }
}
