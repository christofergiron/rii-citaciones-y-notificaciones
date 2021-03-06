<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Vehiculo extends Model
{
  use WorkflowTrait;
  protected $fillable = [
      "tipo", "marca", "modelo", "placa", "año", "color", "estado", "motor", "chasis", "vin",
      "descripcion", "id_propietario", "licencia", "id_unidad", "id_funcionario", "fecha_registro", "id_denuncia",
      "id_orden_captura", "id_lugar"
    ];

  public function denuncias()
  {
      return $this->belongsToMany(Denuncia::class, 'denuncia_vehiculo', 'id_vehiculo', 'id_denuncia');
  }

  public function propietario()
  {
      return $this->belongsTo(PersonaNatural::class, 'id_propietario');
  }

  public function ordencaptura()
  {
      return $this->hasOne(OrdenCaptura::class, 'id_orden_captura');
  }

  public function lugarSS()
  {
      return $this->hasOne(LugarSS::class, 'id_lugar');
  }

  public function marca_object(){
    return $this->hasOne(DetalleListaValorRelacional::class,'id_detalle_catalago','marca');
  }
}
