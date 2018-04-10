<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarMP extends Model
{
  protected $table = "lugares_mp";

  protected $fillable = [
    "departamento_id",
    "municipio_id",
    "barrio_mp_id",
    "aldea_mp_id",
    "caserio_mp_id",
    "persona_natural_mp_id"
  ];

  public function institucion(){
      return $this->morphOne(Lugar::class, 'institucionable');
  }

  public function persona_natural()
  {
      return $this->belongsTo(PersonaNaturalMP::class, 'persona_natural_mp_id');
  }

  public function departamento()
  {
      return $this->belongsTo(Departamento::class);
  }

  public function municipio()
  {
      return $this->belongsTo(Municipio::class);
  }

  public function caserio()
  {
      return $this->belongsTo(CaserioMP::class, 'caserio_mp_id');
  }

  public function Barrio()
  {
      return $this->belongsTo(BarrioMP::class, 'barrio_mp_id');
  }

  public function Aldea()
  {
      return $this->belongsTo(AldeaMP::class, 'aldea_mp_id');
  }
}
