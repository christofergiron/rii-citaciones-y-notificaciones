<?php

namespace App;

use App\Captura;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;

class RealizarCapturaTools
{

  private $captura_required;
  private $log;
  private $workflow_type;

  public function __construct()
  {
      $this->log = new  \Log;
      $this->workflow_type = "realizar_captura";
      $this->captura_required =  Array("nombres", "primer_apellido", "segundo_apellido", "fecha_nacimiento", "sexo", "genero", "id_lugar", "id_funcionario", "id_unidad", "descripcion_captura", "fecha_captura");
  }

  private function get_persona_natural($id) {
    $persona_natural = PersonaNatural::find($id);
    return $persona_natural;
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

  private function workflow_actions($captura_tipo, $user_email) {
    $actions_arr = [];
    $capturas = $captura_tipo->captura()->first();
    //$capturas =$captura->tipo()->first();

    if (is_null($capturas)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $capturas->id;
    $params->object_id = $capturas->id;
    $params->workflow_type = "realizar_captura";  //$this->workflow_type;
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

  private function get_captura($token) {
    $captura_arr = [];

    $user_email = $this->get_email_from_token($token);
    if (!isset($user_email)) { return $captura_arr; }
    if (empty($user_email)) {return $captura_arr; }

    $this->log::alert(json_encode($arr));
    $this->log::alert('count of $arr is '. count($arr));
    foreach($arr as $captura) {
      $captura->hechos = new \stdClass;
      $captura->hechos = $this->get_persona_natural($captura->captura_id);

      $captura_arr[] = $captura;
    }
    return $captura_arr;
  }

  public function ss_captura($captura_id, $token){
    $captura = Captura::find($captura_id);
    return $captura;
  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_persona";
    $hdr->label = "Nombre Capturado";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "numero_captura";
    $hdr->label = "Id_captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "lugar_captura";
    $hdr->label = "Lugar Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_captura";
    $hdr->label = "Fecha Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "workflow_state";
    $hdr->label = "Estado";
    $res->headers[] = $hdr;
    // $hdr = new \stdClass;
    // $hdr->name = "funcionario_asignado";
    // $hdr->label = "Asignado a";
    // $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "actions";
    $hdr->label = "Acciones";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function acciones($token, $captura) {
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
    $captura_tipo = Captura::find($id);
    //$id = $capturas->id;

    $acciones = $this->workflow_actions($captura_tipo, $user_email);
    $acciones[] = 'Expediente';

    $this->log::alert('acciones are ...');
    $this->log::alert(json_encode($acciones));

    return $acciones;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (Captura::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_captura = $dmp->id;
      $row->actions = $this->acciones($token, $dmp);
      //$row->actions = $this->workflow_actions($captura_tipo, $user_email);
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
    //  $row->id_persona = $this->id_persona($dmp);
    //  $row->lugar_captura = $this->id_lugar($dmp);
    //  $row->fecha_captura = $this->fecha_captura($dmp->tipoable_type);
      $row->workflow_state = $dmp->workflow_state;
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function ss_list_captura($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
