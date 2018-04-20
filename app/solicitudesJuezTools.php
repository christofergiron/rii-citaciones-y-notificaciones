<?php

namespace App;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioPJ;
use App\Juez;
use App\ProcesoJudicial;
use App\AsignacionJuez;
use App\Lugar;
use App\LugarPJ;
use App\Documento;
use App\Denuncia;
use App\SolicitudOrdenCaptura;
use App\SolicitudContraOrden;
use App\Expediente;
use App\ExpedientePJ;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class solicitudesJuezTools
{

  private $captura_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "captura_realizada";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function juez($id) {
      $numero_juez = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_juez = $orden_captura->id_juez;
      if (is_null($id_juez)) {return $numero_juez;}
      //cambio, descomentar esto
      //$juez = Juez::find($id_juez);
      //if (is_null($juez)) {return $numero_juez;}
      //$numero_juez = $juez->codigo;
      $numero_juez = $orden_captura->id_juez;
      return $numero_juez;
  }

  private function etapa($id) {
      $tipo_etapa = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_etapa = $orden_captura->id_etapa;
      if (is_null($id_etapa)) {return $tipo_etapa;}
      //$etapa Etapa::find($id_etapa);
      //if (is_null($etapa)) {return $tipo_etapa;}
      $tipo_etapa = $etapa->nombre;

      return $tipo_etapa;
  }

  private function audiencia($id) {
     //cambio esta vaina lleva herencia cuidado, no esta terminada
      $tipo_audiencia = "null";
      $orden_captura = OrdenCaptura::find($id);
      $id_audiencia = $orden_captura->id_etapa;
      if (is_null($id_audiencia)) {return $tipo_audiencia;}
      //$audiencia Audiencia::find($id_audiencia);
      //if (is_null($audiencia)) {return $tipo_audiencia;}
      $tipo_audiencia = $audiencia->nombre;

      return $tipo_audiencia;
  }

  private function get_email_from_token($token) {
    $user_email = "";
    $passport = new Passport;
    $res = $passport->details($token);

    if (!isset($res)) {return $user_email; }
    if (!($res->code == 200)) {return $user_email; }

    $this->log::alert('inside get_email_from_token');
    $this->log::alert(json_encode($res));

    $contents = json_decode($res->contents);
    if (property_exists($contents, "success")) {
      $user_email = $contents->success->email;
    }

    return $user_email;
  }

  private function headers_solicitud_orden(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_solicitud";
    $hdr->label = "Numero Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha";
    $hdr->label = "Fecha Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "estado";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function estado_solicitud_orden($estado) {
    $unidad_arr;
    $nuevo_estado = $estado;
    if($estado == "solicitud_realizada")
    {
      $nuevo_estado = "pendiente_revision";
    }
      return $nuevo_estado;
  }

  private function rows_solicitud_orden($arr) {
    $res = new \stdClass;

    //$id_juez = $arr["id_juez"];
    //$solicitud = SolicitudOrdenCaptura::where('id_juez', $id_juez)->get();

    foreach (SolicitudOrdenCaptura::All() as $dmp) {
      $row = new \stdClass;
      //falta mostrar el fiscal que la solicita
      $row->numero_solicitud = $dmp->id;
      $row->fecha_solicitud = date('Y/m/d',strtotime($dmp->fecha));
      $row->estado = $this->estado_solicitud_orden($dmp->workflow_state);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function list_solicitud_orden($arr) {
    $res = new \stdClass;
    $res->headers = $this->headers_solicitud_orden();
    $res->rows = $this->rows_solicitud_orden($arr);
    return $res;
  }

  private function headers_solicitud_contra_orden(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_solicitud";
    $hdr->label = "Numero Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha";
    $hdr->label = "Fecha Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "estado";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows_solicitud_contra_orden($arr) {
    $res = new \stdClass;

    //$id_juez = $arr["id_juez"];
    //$solicitud = SolicitudContraOrden::where('id_juez', $id_juez)->get();

    foreach (SolicitudContraOrden::All() as $dmp) {
      $row = new \stdClass;
      //falta mostrar el fiscal que la solicita
      $row->numero_solicitud = $dmp->id;
      $row->fecha_solicitud = date('Y/m/d',strtotime($dmp->fecha));
      $row->estado = $this->estado_solicitud_orden($dmp->workflow_state);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function list_solicitud_contra_orden($arr) {
    $res = new \stdClass;
    $res->headers = $this->headers_solicitud_contra_orden();
    $res->rows = $this->rows_solicitud_contra_orden($arr);
    return $res;
  }

  public function acciones_orden($arr) {
    $estado_arr = [];
    $estado;
    $orden = SolicitudOrdenCaptura::find($arr["id_solicitud"]);

    if (preg_match('/realizada/', $orden->workflow_state)) {

      $estado_arr[] = "Aceptar_solicitud";
      $estado_arr[] = "Rechazar_solicitud";

      return $estado_arr;
    }

    if (preg_match('/aprovada/', $orden->workflow_state)) {

      $estado = $orden->workflow_state;
      return $estado;
    }

    if (preg_match('/rechazada/', $orden->workflow_state)) {

      $estado = $orden->workflow_state;
      return $estado;
    }

  }

  public function store_contra_orden($arr) {
    $estado_arr = [];
    $estado;
    $orden = SolicitudContraOrden::find($arr["id_solicitud"]);

    if (preg_match('/realizada/', $orden->workflow_state)) {

      $estado_arr[] = "Aceptar_solicitud";
      $estado_arr[] = "Rechazar_solicitud";

      return $estado_arr;
    }

    if (preg_match('/aprovada/', $orden->workflow_state)) {

      $estado = $orden->workflow_state;
      return $estado;
    }

    if (preg_match('/rechazada/', $orden->workflow_state)) {

      $estado = $orden->workflow_state;
      return $estado;
    }

  }

}
