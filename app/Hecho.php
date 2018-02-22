<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hecho extends Model
{
  protected $attributes = ['narracion'=>null, 'fecha_ocurrencia'=>null, 'clase_lugar_hechos'=> null ];

  protected $fillable = [
  	"denuncia_id",
  	"lugar_id",
  	"narracion",
  	"fecha_ocurrencia",
    "hora_ocurrencia",
  	"clase_lugar_hechos",
  	"descripcion_detallada",
  ];
  
  protected $casts = [
      'clase_lugar_hechos' => 'array'
  ];

  public function lugar()
  {
      return $this->belongsTo(Lugar::class);
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class);
  }
}
