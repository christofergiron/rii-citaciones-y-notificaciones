<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HitoDictamenSS extends Model
{
  protected $fillable = [
    "nombre",
    "descripcion",
    "fecha_inicio",
    "fecha_fin",
    "id_dictamen"
  ];
  protected $table = "hitos_dictamenes_ss";

  public function dictamen() {
       return $this->belongsTo(Dictamen::class, 'id_dictamen');
  }
}
