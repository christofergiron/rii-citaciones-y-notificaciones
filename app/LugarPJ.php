<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarPJ extends Model
{
  protected $table = "lugares_pj";

  protected $fillable = [
    "departamento_id",
    "municipio_id",
    "barrio_pj_id",
    "aldea_pj_id",
    "caserio_pj_id",
    "persona_natural_pj_id"
  ];

  public function institucion(){
      return $this->morphOne(Lugar::class, 'institucionable');
  }

  public function persona_natural()
  {
      return $this->belongsTo(PersonaNaturalPJ::class, 'persona_natural_pj_id');
  }

  public function departamento()
  {
      return $this->belongsTo(Departamento::class);
  }

  public function municipio()
  {
      return $this->belongsTo(Municipio::class);
  }
}
