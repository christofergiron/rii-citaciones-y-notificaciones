<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarSS extends Model
{
  protected $table = "lugares_ss";

  protected $fillable = [
    "departamento_id",
    "municipio_id",
    "ciudad_ss_id",
    "colonia_ss_id",
    "sector_ss_id",
    "aldea_ss_id",
    "persona_natural_ss_id",
    "regional_id",
    "zona_ss_id"
  ];

  public function institucion(){
      return $this->morphOne(Lugar::class, 'institucionable');
  }

  public function persona_natural()
  {
      return $this->belongsTo(PersonaNaturalSS::class, 'persona_natural_ss_id');
  }

  public function regional()
  {
      return $this->belongsTo(Regional::class);
  }

  public function departamento()
  {
      return $this->belongsTo(Departamento::class);
  }

  public function municipio()
  {
      return $this->belongsTo(Municipio::class);
  }

  public function ciudad()
  {
      return $this->belongsTo(CiudadSS::class, 'ciudad_ss_id');
  }

  public function barrio()
  {
      return $this->belongsTo(BarrioSS::class,'barrio_ss_id');
  }

  public function colonia()
  {
      return $this->belongsTo(ColoniaSS::class,'colonia_ss_id');
  }

  public function Aldea()
  {
      return $this->belongsTo(AldeaSS::class, 'aldea_ss_id');
  }

  public function sector()
  {
      return $this->belongsTo(SectorSS::class,'sector_ss_id');
  }

  public function zona()
  {
      return $this->belongsTo(ZonaSS::class,'zona_ss_id');
  }

}
