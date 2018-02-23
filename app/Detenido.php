<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detenido extends Model
{
  //como ya posee herencia con persona,no necesita el id o si?
protected $fillable = [
    "id_orden",
    "id_requerimiento",
    "id_expediente",
    "id_captura",
    "id_persona",
    "fecha_nacimiento",
    "nacionalidad",
    "genero",
    "sexo",
    "edad",
    "fecha_captura",
    "lugar_retencion",
    "id_fiscal",
    "id_abogado_defensor",
    "fecha_remision_mp",
    "id_fiscalia_remision",
    "fecha_remision_pj",
    "id_juzgado_remision",
    "fecha_remision_penal",
    "id_penal",
    "fecha_extracion",
    "pais_extraditado"
  ];

  public function tipoable(){
     return $this->morphTo();
  }

  public function rol(){
      return $this->morphOne(Rol::class, 'rolable');
  }

  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

  public function orden_captura()
  {
      return $this->hasOne(Orden::class, 'id_orden');
  }

  public function cotejamiento_dactilar(){
           return $this->hasOne(CotejamientoDactilar::class, 'id_cotejamiento');
       }
}
