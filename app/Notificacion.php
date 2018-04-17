<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Brexis\LaravelWorkflow\Traits\WorkflowTrait;

class Notificacion extends Model
{
use WorkflowTrait;
  protected $table = "notificaciones";

  protected $fillable = [
    "id_expediente",
    "id_funcionario",
    "organo_juridiccional",
    "fecha_creacion",
    "audiencia",
    "etapa",
    "proceso_judicial",
    "id_resolucion",
    "asunto",
    "objeto_proceso",
    "observaciones",
    "id_juez",
    "id_fiscal"
  ];

    public function documento()
  {
    return $this->morphOne(Documento::class, 'documentable');
    }

    public function expediente()
  {
        return $this->belongsTo(ExpedientePJ::class, 'id_expediente');
    }

    public function proceso_judicial()
  {
        return $this->belongsTo(ProcesoJudicial::class, 'proceso_judicial');
    }

    public function resolucion()
  {
        return $this->HasOne(Resolucion::class, 'id_resolucion');
    }

    public function victimas()
  {
        return $this->HasMany(VictimaNotificacion::class, 'id_notificacion');
    }

    public function imputados()
  {
        return $this->HasMany(ImputadoNotificacion::class, 'id_notificacion');
    }

    public function testigos()
  {
        return $this->HasMany(TestigoNotificacion::class, 'id_notificacion');
    }

    public function delitos()
  {
        return $this->HasMany(DelitoNotificacion::class, 'id_notificacion');
    }

    public function juez()
  {
        return $this->HasOne(Juez::class, 'id_juez');
    }

    public function fiscales()
  {
        return $this->HasMany(Fiscal::class, 'id_fiscal');
    }

    public function canales_envio()
  {
        return $this->HasMany(CanalEnvioCN::class, 'id_notificacion');
    }


//no tengo idea de estos sujetos procesales, ver como los meto
    public function sujetos_procesales()
  {
        return $this->HasMany(SujetoProcesalNotificacion::class, 'id_notificacion');
    }

    public function funcionarioPJ()
  {
        return $this->HasOne(FuncionarioPJ::class, 'id_funcionario');
    }
}
