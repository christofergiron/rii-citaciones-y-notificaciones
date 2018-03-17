<?php

namespace App;

use App\Dictamen;
use App\DictamenVehicular;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioSS;
use App\FuncionarioMP;
use App\Lugar;
use App\LugarSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class DictamenVehicularTools
{

  private $dictamen_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "dictmaen_realizado";
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    //if (is_null($persona_natural)) {return null;}
    return $persona_natural;
  }

  private function dictamen_vehicular($id) {
    $dictamen_arr;
    $dictamen = Dictamen::find($id);
    $dictamenes = new \stdClass;;
    if (is_null($dictamen)) {return $dictamen_arr;}

    if (preg_match('/DictamenVehicular/',$dictamen->dictamable_type))
    {
      $id = $dictamen->dictamable_id;
      $dictamen_vehicular = DictamenVehicular::find($id);
      $dictamenes->id_solicitud = $dictamen_vehicular->id_solicitud;
      $dictamenes->workflow_state = $dictamen_vehicular->workflow_state;
      $dictamenes->informe_adjunto = $dictamen_vehicular->informe_adjunto;
      $dictamenes->informe_html = $dictamen_vehicular->informe_html;

      $dictamen_arr = $dictamenes;
      return $dictamen_arr;
    }
  }

  private function funcionario($id) {
      $funcionarios_arr;
      $dictamen = Dictamen::find($id);
      $dictamenes = $dictamen->funcionarioss()->get()->first();
      $autor = new \stdClass;
      $funcionarioss = $dictamen->id_autor;

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
        $autor->nombres = $persona->nombres;
        $autor->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $autor->placa = $funcionarios_ss->placa;
        $funcionarios_arr = $autor;
        return $funcionarios_arr;
  }

  private function funcionario_fiscal($id) {
      $funcionarios_arr;
      $dictamen = Dictamen::find($id);
      $fiscal = $dictamen->remitido_A;
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

  private function unidad($id) {
    $unidad_arr;
    $dictamen = Dictamen::find($id);
    $lugar = new \stdClass;
    $idlugar = $dictamen->unidad;

      $unidad = Dependencia::find($idlugar);
      $lugar->id_unidad = $unidad->institucion_id;
      $lugar->nombre = $unidad->nombre;

        $unidad_arr = $lugar;
        return $unidad_arr;
  }

  private function dictamen($id) {
    $dictamen_arr;
    $dictamen = Dictamen::find($id);
    $dictamenes = new \stdClass;

    $dictamenes->id = $dictamen->id;
    $dictamenes->id_expediente = $dictamen->id_expediente;
    $dictamenes->fecha_creacion = date('Y/m/d',strtotime($dictamen->fecha_creacion));
    $dictamenes->descripcion = $dictamen->descripcion;
    $dictamenes->observaciones = $dictamen->observaciones;
    $dictamenes->fecha_envio = date('Y/m/d',strtotime($dictamen->fecha_envio));

      $dictamen_arr = $dictamenes;
    return $dictamen_arr;
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

  private function workflow_actions($dictamen_tipo, $user_email) {
    $actions_arr = [];
    $dictamenes = $dictamen_tipo->dictamen()->first();

    if (is_null($dictamenes)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $dictamenes->id;
    $params->object_id = $dictamenes->id;
    $params->workflow_type = "realizar_dictamen";  //$this->workflow_type;
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

  private function get_dictamen($dictamen, $id, $token) {
    $dictamen_arr = [];

    $dictamenes = new \stdClass;

    $dictamenes->dictamen = $this->dictamen($id);
    $dictamenes->dictamen_vehicular = $this->dictamen_vehicular($id);
    $dictamenes->autor = $this->funcionario($id);
    $dictamenes->unidad = $this->unidad($id);
    $dictamenes->entregado_A = $this->funcionario_fiscal($id);
    $dictamen_arr[] = $dictamenes;
    return $dictamen_arr;
  }

  public function ss_dictamen($dictamen_id, $token){
    $res = new \stdClass;
    $dictamen = Dictamen::find($dictamen_id);
    if (is_null($dictamen)) { return json_encode($res); }
    $id = $dictamen->id;

    $result = $this->get_dictamen($dictamen, $id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_dictamen";
    $hdr->label = "Numero Dictamen";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_expediente";
    $hdr->label = "Numero Expediente";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_unidad";
    $hdr->label = "unidad";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_autor";
    $hdr->label = "autor";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "remitido_A";
    $hdr->label = "enviado A";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "actions";
    $hdr->label = "Acciones";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function unidadresponsable($dic){

    $idunidad = $dic->unidad;
    $unidad = Dependencia::find($idunidad);
    $lugar = "";
    if (is_null($unidad)) {return $lugar;}
     $lugar = $unidad->nombre;
     return $lugar;
  }

  private function responsable($dic){

    $idautor = $dic->id_autor;
    $funcionarios_ss = FuncionarioSS::find($idautor);
    $nombre = "";
    if (is_null($funcionarios_ss)) {return $nombre;}
    $funcionario = $funcionarios_ss->institucion()->first();
    if (is_null($funcionario)) {return null;}
    $funcionarioid = $funcionario->id;
    $funcionario_policia = Funcionario::find($funcionarioid);
    $rol_funcionario = $funcionario_policia->rol()->first();
    $persona_natural_id = $rol_funcionario->persona_natural_id;
    $persona = $this->get_persona_natural($persona_natural_id);
    $nombre = $persona->primer_apellido.', '.$persona->segundo_apellido;;
     return $nombre;
  }

  private function remitidoa($dic){

    $idautor = $dic->id_autor;
    $funcionarios_ss = FuncionarioMP::find($idautor);
    $nombre = "";
    if (is_null($funcionarios_ss)) {
      $this->responsable($dic);
    }
    $funcionario = $funcionarios_ss->institucion()->first();
    if (is_null($funcionario)) {return null;}
    $funcionarioid = $funcionario->id;
    $funcionario_policia = Funcionario::find($funcionarioid);
    $rol_funcionario = $funcionario_policia->rol()->first();
    $persona_natural_id = $rol_funcionario->persona_natural_id;
    $persona = $this->get_persona_natural($persona_natural_id);
    $nombre = $persona->primer_apellido.', '.$persona->segundo_apellido;
     return $nombre;
  }

  private function tipodictamen($dictamen){

    if (preg_match('/DictamenVehicular/',$dictamen->dictamable_type))
    { $tipo_dictamen = "Dictamen Vehicular";
      return $tipo_dictamen;
    }
  }

  private function tipoworkflow($dictamen){
    $id_dictamable = $dictamen->dictamable_id;

    if (preg_match('/DictamenVehicular/',$dictamen->dictamable_type))
    {
      $dictamenvehicular = DictamenVehicular::find($id_dictamable);
      $workflow = $dictamenvehicular->workflow_state;
      return $workflow;
    }
  }

  private function acciones($token, $dictamen) {
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
    $dictamen_tipo = Dictamen::find($id);

    $acciones = $this->workflow_actions($dictamen_tipo, $user_email);
    $acciones[] = 'Expediente';

    $this->log::alert('acciones are ...');
    $this->log::alert(json_encode($acciones));

    return $acciones;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Dictamen::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_dictamen = $dmp->id;
      $row->numero_expediente = $dmp->id_expediente;
      $row->unidad = $this->unidadresponsable($dmp);
      $row->autor = $this->responsable($dmp);
      $row->enviado_A = $this->remitidoa($dmp);
      $row->fecha_creacion = date('Y/m/d',strtotime($dmp->fecha_creacion));
      $row->fecha_envio = date('Y/m/d',strtotime($dmp->fecha_envio));
      $row->tipo_dictamen = $this->tipodictamen($dmp);
      $row->acciones = $this->acciones($token, $dmp);
      $row->workflow_state = $this->tipoworkflow($dmp);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function ss_list_dictamen($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
