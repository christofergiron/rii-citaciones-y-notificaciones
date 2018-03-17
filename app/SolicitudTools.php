<?php

namespace App;

use App\Solicitud;
use App\SolicitudAllanamiento;
use App\SolicitudAnalisis;
use App\Detenido;
use App\MenorDetenido;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioSS;
use App\Lugar;
use App\LugarSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class SolicitudTools
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
    //if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function funcionario($id) {
    $funcionarios_arr;
    $solicitud = Solicitud::find($id);
    $funcionarioss = $solicitud->solicitado_por;
    $responsable = new \stdClass;

      //funcionarios
      $funcionarios_ss = FuncionarioSS::find($funcionarioss);
      if (is_null($funcionarios_ss)) {return null;}
        $funcionario = $funcionarios_ss->institucion()->first();
        if (is_null($funcionario)) {return null;}
        $funcionarioid = $funcionario->id;
        $funcionario_policia = Funcionario::find($funcionarioid);
        $rol_funcionario = $funcionario_policia->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $responsable->placa = $funcionarios_ss->placa;
        $funcionarios_arr[] = $responsable;
        return $funcionarios_arr;
  }

  private function funcionario_fiscal($id) {
      $funcionarios_arr;
      $solicitud = Solicitud::find($id);
      $fiscal = $solicitud->solicitado_por;
      $responsable = new \stdClass;



      //funcionarios
      $funcionarios_ss = FuncionarioMP::find($fiscal);
      if (is_null($funcionarios_ss)) {
        $this->funcionario($id);
      }
        $funcionario = $funcionarios_ss->institucion()->first();
          if (is_null($funcionario)) {return null;}
        $funcionarioid = $funcionario->id;
        $funcionario_policia = Funcionario::find($funcionarioid);
        $rol_funcionario = $funcionario_policia->rol()->first();
        $persona_natural_id = $rol_funcionario->persona_natural_id;
        $persona = $this->get_persona_natural($persona_natural_id);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $funcionarios_arr = $responsable;
        return $funcionarios_arr;
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

  private function tipo_solicitud($id) {
      $tipo_solicitud_arr;
      $solicitud = Solicitud::find($id);
      if (is_null($solicitud)) { return json_encode($res); }

      $tipo_solicitud = new \stdClass;

      $id_solicitable = $solicitud->solicitable_id;

      $this->log::alert(json_encode($solicitud));
      if (preg_match('/Allanamiento/',$solicitud->solicitable_type))
      {
        $solicitud_allanamiento = SolicitudAllanamiento::find($id_solicitable);

        $tipo_solicitud->numero = $solicitud_allanamiento->id;
        $tipo_solicitud->tipo = "solicitud allanamiento";
        $tipo_solicitud->evidencias = $solicitud_allanamiento->numero_evidencias_encontradas;
        $tipo_solicitud->descripcion_evidencias = $solicitud_allanamiento->descripcion_evidencias;
        $tipo_solicitud_arr = $tipo_solicitud;
        return $tipo_solicitud_arr;

      }
      if (preg_match('/Analisis/',$solicitud->solicitable_type))
      {
        $solicitud_analisis = SolicitudAnalisis::find($id_solicitable);

        $tipo_solicitud->numero = $solicitud_analisis->id;
        $tipo_solicitud->tipo = "solicitud analisis";
        $tipo_solicitud->laboratorio = $solicitud_analisis->nombre_laboratorio;
        $tipo_solicitud->tipo_analisis = $solicitud_analisis->nombre_analisis;
        $tipo_solicitud->detalle_analisis = $solicitud_analisis->detalle_analisis;
        $tipo_solicitud_arr = $tipo_solicitud;
        return $tipo_solicitud_arr;
      }
  }

  private function solicitudes($id) {
    $solicitud_arr;
    $solicitud = Solicitud::find($id);
    $solicitudes = new \stdClass;

    $solicitudes->numero_solicitud = $solicitud->id;
    $solicitudes->numero_oficio = $solicitud->numero_oficio;
    $solicitudes->fecha_solicitud = date('Y/m/d',strtotime($solicitud->fecha));
    $solicitudes->tipo_solicitud = $solicitud->titulo;
    $solicitudes->descripcion = $solicitud->descripcion;

      $solicitud_arr = $solicitudes;
    return $solicitud_arr;
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

  private function get_solicitud($solicitud, $id, $token) {
    $solicitud_arr = [];
    //$user_email = $this->get_email_from_token($token);
    //if (!isset($user_email)) { return $solicitud_arr; }
    //if (empty($user_email)) {return $solicitud_arr; }

    $solicitudes = new \stdClass;
    $solicitudes->solicitud = $this->solicitudes($id);
    $solicitudes->tipo_solicitud = $this->tipo_solicitud($id);
    $solicitudes->solicitante = $this->funcionario_fiscal($id);
    $solicitudes->unidad = $this->unidad($solicitud);
    $solicitud_arr[] = $solicitudes;
    return $solicitud_arr;
  }

  public function solicitud($solicitud_id, $token){
    $res = new \stdClass;
    $solicitud = Solicitud::find($solicitud_id);
    if (is_null($solicitud)) { return json_encode($res); }
    $id = $solicitud->id;

    $result = $this->get_solicitud($solicitud, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "numero_solicitud";
    $hdr->label = "Numero Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "unidad";
    $hdr->label = "Unidad";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha";
    $hdr->label = "Fecha Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "tipo_solicitud";
    $hdr->label = "Tipo Solicitud";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "workflow_state";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "actions";
    $hdr->label = "Acciones";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function unidadtabla($solicitud) {
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

  private function tiposolicitud($solicitud){
    $tipo_solicitud;

    if (preg_match('/Allanamiento/',$solicitud->solicitable_type))
    { $tipo_solicitud = "Solicitud Allanamiento";
      return $tipo_solicitud;
    }

    if (preg_match('/Analisis/',$solicitud->solicitable_type))
    { $tipo_solicitud = "Solicitud Analisis";
      return $tipo_solicitud;
    }
  }

  private function tipoworkflow($solicitud){
    $id_solicitable = $solicitud->solicitable_id;

    if (preg_match('/Allanamiento/',$solicitud->solicitable_type))
    {
      $flagrancia = SolicitudAllanamiento::find($id_solicitable);
      $workflow = $flagrancia->workflow_state;
      return $workflow;
    }

    if (preg_match('/Analisis/',$solicitud->solicitable_type))
    {
      $extradicion = SolicitudAnalisis::find($id_solicitable);
      $workflow = $extradicion->workflow_state;
      return $workflow;
    }
      $workflow = $solicitud->workflow_state;
      return $workflow;
  }

  private function acciones($token, $solicitud) {
    $acciones = [];

    $user_email = $this->get_email_from_token($token);
    if (!isset($user_email)) {
      $this->log::alert('user_email is null');
      return $acciones;
    }

    if (empty($user_email)) {
      $this->log::alert('user_email is empty');
      return $acciones;
    }
    $solicitud_tipo = Solicitud::find($id);

    $acciones = $this->workflow_actions($solicitud_tipo, $user_email);
    $acciones[] = 'Expediente';

    $this->log::alert('acciones are ...');
    $this->log::alert(json_encode($acciones));

    return $acciones;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Solicitud::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_solicitud = $dmp->id;
      $row->fecha_solicitud = date('Y/m/d',strtotime($dmp->fecha));
      $row->unidad = $this->unidadtabla($dmp);
      $row->tipo_solicitud = $this->tiposolicitud($dmp);
      $row->acciones = $this->acciones($token, $dmp);
      $row->workflow_state = $this->tipoworkflow($dmp);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function list_solicitudes($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
