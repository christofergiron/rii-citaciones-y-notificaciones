<?php

namespace App;

use App\Expediente;
use App\ExpedientePJ;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioPJ;
use App\Lugar;
use App\LugarSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;
use App\Citacion;
use App\CanalEnvioCN;
use App\Juez;
use App\ProcesoJudicial;
use App\AsignacionJuez;

class CitacionTools
{

  private $solicitud_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "solicitud_realizada";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function unidad($solicitud) {
      $unidad_arr;
      $id = $solicitud->institucion;
      $unidad = Dependencia::find($id);
        if (is_null($unidad)) {return null;}
      $unidades = new \stdClass;
      $unidades->id_unidad = $unidad->institucion_id;
      $unidades->nombre = $unidad->nombre;

        $unidad_arr = $unidades;
        return $unidad_arr;
  }

  private function citaciones($id) {
    $citacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $citacion = Citacion::find($id);
    $citaciones = new \stdClass;

    $citaciones->id_citacion = $citacion->id;
    $citaciones->organo_juridiccional = $citacion->organo_juridiccional;
    $citaciones->fecha_creacion = date('Y/m/d',strtotime($citacion->fecha_creacion));
    $citaciones->audiencia = $citacion->audiencia;
    $citaciones->etapa = $citacion->etapa;
    $citaciones->solicitante = $citacion->parte_solicitante;
    $citaciones->asunto = $citacion->asunto;
    $citaciones->acto_procesal = $citacion->tipo_acto_procesal;
    $citaciones->lugar_citacion = $citacion->lugar_citacion;
    //ocupa la hora
    $citaciones->fecha_citacion = date('Y/m/d-g:i',strtotime($citacion->fecha_citacion));
    $citaciones->observaciones = $citacion->observaciones;

    $citacion_arr = $citaciones;
    return $citacion_arr;
  }

  private function numero_expediente($id) {
    $citacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $citacion = Citacion::find($id);
    $citaciones = new \stdClass;

    $expediente = $citacion->expediente()->first();
    if (is_null($expediente)) { return null; }

    $expedientepj = $expediente->numero_expediente;
    $expedienterii = $expediente->institucion()->first()->numero_expediente;

    //$expediente_pj = $citacion->expediente()->first()->numero_expediente;
    //$expediente_pj = $citacion->expediente()->institucion()->first()->numero_expediente;

    $citaciones->numero_expedinte_rii = $expedienterii;
    $citaciones->numero_expedinte_PJ = $expedientepj;

    $citacion_arr = $citaciones;
    return $citacion_arr;
  }

  private function citado($id) {
    $citacion_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $citacion = Citacion::find($id);
    $citaciones = new \stdClass;

    $citaciones->persona_citada = $citacion->persona_natural;
    $citaciones->tipo = $citacion->tipo;

    $citacion_arr = $citaciones;
    return $citacion_arr;
  }

  private function medios_citacion($id) {
      $citacionarr = [];
      $citacionn = Citacion::find($id);
      //echo $citacionn;
      $canales_envio = $citacionn->canales_envio()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($canales_envio)) {return $citacion_arr;}
      foreach($canales_envio as $cn) {
        $canal = new \stdClass;

        $canal->id = $cn->id;
        $canal->canal_envio = $cn->canal_envio;
        $canal->medios_envio = $cn->medios_envio;

        $citacion_arr[] = $canal;
        unset($canal);
      }
      return $citacion_arr;
  }

  private function funcionario_pj($id) {
      $funcionarios_arr = [];
      $citacion = Citacion::find($id);
      $responsable = new \stdClass;
      $funcionariopj = $citacion->id_funcionario;
      //$investigadores = $captura->id_funcionario;

      //funcionarios
      $funcionarios_pj = FuncionarioPJ::find($funcionariopj);
      if (is_null($funcionarios_pj)) {return null;}
        $funcionario = $funcionarios_pj->institucion()->first();
        $funcionarioid = $funcionario->id;
        $funcionario_secretario = Funcionario::find($funcionarioid);
        if (is_null($funcionario_secretario)) {return null;}
        $rol_funcionario = $funcionario_secretario->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);
        if (is_null($persona)) {return null;}
        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function tipo_identidad($persona) {
    $persona_tipo_identidad = strtolower($persona->identificable_type);
    $tipo_identidad = "portador";

    if (preg_match('/anonimo/', $persona_tipo_identidad)) {
      $tipo_identidad = "anonimo";
    }

    if (preg_match('/desconocido/', $persona_tipo_identidad)) {
      $tipo_identidad = "desconocido";
    }

    if (preg_match('/protegido/', $persona_tipo_identidad)) {
      $tipo_identidad = "protegido";
    }

    if (preg_match('/noportador/', $persona_tipo_identidad)) {
      $tipo_identidad = "no_portador";
    }

    if (preg_match('/deoficio/', $persona_tipo_identidad)) {
      $tipo_identidad = "de_oficio";
    }

    return $tipo_identidad;
  }

  private function workflow_actions($solicitud_tipo, $user_email) {
    $actions_arr = [];
    $solicitudes = $solicitud_tipo->solicitud()->first();

    if (is_null($solicitudes)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $solicitudes->id;
    $params->object_id = $solicitudes->id;
    $params->workflow_type = "realizar_solicitud";  //$this->workflow_type;
    $params->user_email = $user_email;
    $this->log::alert("json_encoded w/o True parameter");
    $this->log::alert(json_encode($params));
    // $this->log::alert("json_encoded with True parameter");
    // $this->log::alert(json_encode($params),true );

    // watch this line
    $actions = $wf->user_actions(json_encode($params));
    $this->log::alert(json_encode($actions));

    if (is_null($actions)) {return $actions_arr; }
    if (!property_exists($actions, "contents")) { return $actions_arr; }
    if (!property_exists($actions, "code")) { return $actions_arr; }
    if (!$actions->code == 200) { return $actions_arr; }
    $json_actions = json_decode($actions->contents);

    if (!is_null($json_actions)) {
      if (property_exists($json_actions, "message")) {
        $actions_arr = $json_actions->message;
      }
    }

    return $actions_arr;
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

  private function get_citacion($solicitud, $id, $token) {
    $citacion_arr;
    //$user_email = $this->get_email_from_token($token);
    //if (!isset($user_email)) { return $solicitud_arr; }
    //if (empty($user_email)) {return $solicitud_arr; }

    $citaciones = new \stdClass;
    $citaciones->numero_expediente = $this->numero_expediente($id);
    $citaciones->citacion = $this->citaciones($id);
    $citaciones->persona_citada = $this->citado($id);
    $citaciones->canales_envio = $this->medios_citacion($id);
    $citaciones->creador = $this->funcionario_pj($id);
    $citacion_arr = $citaciones;
    return $citacion_arr;
  }

  public function pj_citaciones($citacion_id, $token){
    $res = new \stdClass;
    $citacion = Citacion::find($citacion_id);
    if (is_null($citacion)) { return json_encode($res); }
    $id = $citacion->id;

    $result = $this->get_citacion($citacion, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_citacion";
    $hdr->label = "Numero Citacion";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "organo_juridiccional";
    $hdr->label = "Organo Juridiccional";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_citacion";
    $hdr->label = "Fecha Citacion";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "estado";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Citacion::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_citacion = $dmp->id;
      $row->organo_juridiccional = $dmp->organo_juridiccional;
      $row->fecha_citacion = date('Y/m/d',strtotime($dmp->fecha_citacion));
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function pj_list_citaciones($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
