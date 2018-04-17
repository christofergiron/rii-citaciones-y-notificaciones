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
use App\Emplazamiento;
use App\CanalEnvioCN;
use App\Juez;
use App\ProcesoJudicial;
use App\AsignacionJuez;

class EmplazamientoTools
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

  private function emplazamientos($id) {
    $emplazamiento_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $emplazamiento = Emplazamiento::find($id);
    $emplazamientos = new \stdClass;

    $emplazamientos->id_citacion = $emplazamiento->id;
    $emplazamientos->organo_juridiccional = $emplazamiento->organo_juridiccional;
    $emplazamientos->fecha_creacion = date('Y/m/d',strtotime($emplazamiento->fecha_creacion));
    $emplazamientos->audiencia = $emplazamiento->audiencia;
    $emplazamientos->etapa = $emplazamiento->etapa;
    $emplazamientos->solicitante = $emplazamiento->parte_solicitante;
    $emplazamientos->asunto = $emplazamiento->asunto;
    $emplazamientos->acto_procesal = $emplazamiento->tipo_acto_procesal;
    $emplazamientos->lugar_citacion = $emplazamiento->lugar_citacion;
    //ocupa la hora
    $emplazamientos->fecha_citacion = date('Y/m/d-g:i',strtotime($emplazamiento->fecha_citacion));
    $emplazamientos->observaciones = $emplazamiento->observaciones;

    $emplazamiento_arr = $emplazamientos;
    return $emplazamiento_arr;
  }

  private function numero_expediente($id) {
    $emplazamiento_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $emplazamiento = Emplazamiento::find($id);
    $emplazamientos = new \stdClass;

    $expediente = $emplazamiento->expediente()->first();
    if (is_null($expediente)) { return null; }

    $expedientepj = $expediente->numero_expediente;
    $expedienterii = $expediente->institucion()->first()->numero_expediente;

    //$expediente_pj = $emplazamiento->expediente()->first()->numero_expediente;
    //$expediente_pj = $emplazamiento->expediente()->institucion()->first()->numero_expediente;

    $emplazamientos->numero_expedinte_rii = $expedienterii;
    $emplazamientos->numero_expedinte_PJ = $expedientepj;

    $emplazamiento_arr = $emplazamientos;
    return $emplazamiento_arr;
  }

  private function citado($id) {
    $emplazamiento_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $emplazamiento = Emplazamiento::find($id);
    $emplazamientos = new \stdClass;

    $emplazamientos->persona_citada = $emplazamiento->persona_natural;
    $emplazamientos->tipo = $emplazamiento->tipo;

    $emplazamiento_arr = $emplazamientos;
    return $emplazamiento_arr;
  }

  private function medios_emplazamientos($id) {
      $emplazamiento_arr = [];
      $emplazamiento = Emplazamiento::find($id);
      //echo $emplazamiento;
      $canales_envio = $emplazamiento->canales_envio()->get();
      //$delitos = $orden_captura->delitos()->get();
      if (is_null($canales_envio)) {return null;}
      foreach($canales_envio as $cn) {
        $canal = new \stdClass;

        $canal->id = $cn->id;
        $canal->canal_envio = $cn->canal_envio;
        $canal->medios_envio = $cn->medios_envio;

        $emplazamiento_arr[] = $canal;
        unset($canal);
      }
      return $emplazamiento_arr;
  }

  private function funcionario_pj($id) {
      $funcionarios_arr = [];
      $emplazamiento = Emplazamiento::find($id);
      $responsable = new \stdClass;
      $funcionariopj = $emplazamiento->id_funcionario;
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

  private function get_emplazamiento($solicitud, $id, $token) {
    $emplazamiento_arr;
    //$user_email = $this->get_email_from_token($token);
    //if (!isset($user_email)) { return $solicitud_arr; }
    //if (empty($user_email)) {return $solicitud_arr; }

    $emplazamientos = new \stdClass;
    $emplazamientos->numero_expediente = $this->numero_expediente($id);
    $emplazamientos->emplazamiento = $this->emplazamientos($id);
    $emplazamientos->persona_citada = $this->citado($id);
    $emplazamientos->canales_envio = $this->medios_emplazamientos($id);
    $emplazamientos->creador = $this->funcionario_pj($id);
    $emplazamiento_arr = $emplazamientos;
    return $emplazamiento_arr;
  }

  public function pj_emplazamientos($emplazamiento_id, $token){
    $res = new \stdClass;
    $emplazamiento = Emplazamiento::find($emplazamiento_id);
    if (is_null($emplazamiento)) { return json_encode($res); }
    $id = $emplazamiento->id;

    $result = $this->get_emplazamiento($emplazamiento, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_emplazamiento";
    $hdr->label = "Numero EmplazamientoTools";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "organo_juridiccional";
    $hdr->label = "Organo Juridiccional";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_emplazamiento";
    $hdr->label = "Fecha Emplazamiento";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "persona_natural";
    $hdr->label = "Persona Citada";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Emplazamiento::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_emplazamiento = $dmp->id;
      $row->organo_juridiccional = $dmp->organo_juridiccional;
      $row->fecha_emplazamiento = date('Y/m/d',strtotime($dmp->fecha_citacion));
      $row->persona_citada = $dmp->persona_natural;
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function pj_list_emplazamientos($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
