<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class OrdenCaptura extends Model
{
  use WorkflowTrait;

    protected $fillable = [
      "fecha_creacion",
      "estado",
      "id_expediente",
      "id_etapa",
      "id_audiencia",
      "id_juez"
    ];
protected $table = "ordenes_capturas";

  public function ordenable()
  {
        return $this->morphTo();
    }

    public function documento(){
        return $this->morphOne(Documento::class, 'documentable');
    }

  public function expediente()
  {
        return $this->belongsTo(Expediente::class, 'id_expediente');
    }

  public function etapa()
  {
      return $this->belongsTo(Etapa::class, 'id_etapa');
    }

  public function audiencia()
  {
      return $this->belongsTo(Audiencia::class, 'id_audiencia');
    }

  public function juez()
    {
      return $this->belongsTo(Juez::class, 'id_juez');
      }

  public function delitos()
  {
        return $this->HasMany(OrdenCapturaDelito::class, 'id_orden_captura');
    }

  public function estados()
    {
          return $this->HasMany(OrdenCapturaEstado::class, 'id_orden_captura');
      }

}
