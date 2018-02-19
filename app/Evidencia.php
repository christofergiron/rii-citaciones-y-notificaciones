<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
//relacion de 1 a n
  public function expediente()
  {
      return $this->belongsTo(Expediente::class, 'id_expediente');
  }

  public function flagrancia()
  {
      return $this->belongsTo(Flagrancia::class, 'id_flagrancia');
  }

  //esta relacion es con persona porque puede pertenecer a cualquier rol, victima, detenido, sospechoso, testigo etc?
  //relacion de 1 a n
  public function persona()
  {
      return $this->belongsTo(PersonaNatural::class, 'id_persona');
  }

  public function registropersonal()
  {
      return $this->belongsTo(nameobjeto::class, 'id_registro_personal');
  }

  public function denuncia()
  {
      return $this->belongsTo(Denuncia::class, 'id_denuncia');
  }

  public function allanamiento()
  {
      return $this->belongsTo(SolicitudAllanamiento::class, 'id_allanamiento');
  }

  public function captura()
  {
      return $this->belongsTo(Captura::class, 'id_captura');
  }

  public function escenadelito()
  {
      return $this->belongsTo(nameobjeto::class, 'id_escena_delito');
  }

  public function solicitudanalisis()
  {
      return $this->belongsTo(SolicitudAnalisis::class, 'id_solicitud');
  }

  public function funcionario()
  {
      return $this->belongsTo(FuncionarioSS::class, 'id_funcionario');
  }
}
