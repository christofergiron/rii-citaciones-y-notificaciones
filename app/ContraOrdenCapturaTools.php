<?php

namespace App;
use App\OrdenCaptura;
use App\ContraOrdenCaptura;
use App\Fiscal;
use App\Juez;
use App\Workflow;
use Validator;
use App\Passport;
use App\Funcionario;
use App\FuncionarioMP;
use App\Lugar;
use App\LugarSS;
use App\Rol;
use App\PersonaNatural;
use App\Persona;
use App\Dependencia;
use App\Institucion;

class ContraOrdenCapturaTools
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

  private function get_vehiculo($id) {
    $vehiculo = Vehiculo::find($id);
    if (is_null($vehiculo)) {return null;}
    return $vehiculo;
  }

  private function juez($id) {
      $numero_juez = null;
      $contra_orden = ContraOrdenCaptura::find($id);
      $id_juez = $contra_orden->id_juez;
      if (is_null($id_juez)) {return $numero_juez;}
      //cambio, descomentar esto
      $juez = Juez::find($id_juez);
      if (is_null($juez)) {return $numero_juez;}
      $numero_juez = $juez->codigo;
      return $numero_juez;
  }

  private function fiscal($id) {
      $funcionarios_arr;
      $contra_orden = ContraOrdenCaptura::find($id);
      $responsable = new \stdClass;
      $id_fiscal = $contra_orden->id_fiscal;
      if (is_null($id_fiscal)) {return null;}
      $fiscales = Fiscal::find($id_fiscal);
      if (is_null($fiscales)) {return null;}
      $fiscal = $fiscales->rol()->first()->persona_natural_id;
      if (is_null($fiscal)) {return null;}

      //funcionarios
      if (is_null($fiscal)) {
        return null;
      }
        $persona = $this->get_persona_natural($fiscal);

        //funcionario
        $responsable->nombres = $persona->nombres;
        $responsable->apellidos = $persona->primer_apellido.', '.$persona->segundo_apellido;
        $funcionarios_arr = $responsable;
        return $funcionarios_arr;
  }

  private function contra_orden($id) {
    $contra_ordenes_captura_arr;
    $contra_orden = ContraOrdenCaptura::find($id);
    $ordenes_capturas = new \stdClass;

    $ordenes_capturas->numero_contra_orden_captura = $contra_orden->id;
    $ordenes_capturas->orden_captura = $contra_orden->id_orden;
    $ordenes_capturas->fecha_creacion = date('Y/m/d',strtotime($contra_orden->fecha_creacion));
    $ordenes_capturas->numero_expediente = $this->expediente($contra_orden->id_expediente);
    $ordenes_capturas->motivo = $contra_orden->razon;
    $ordenes_capturas->descripcion = $contra_orden->descripcion;

    $contra_ordenes_captura_arr = $ordenes_capturas;
    return $contra_ordenes_captura_arr;
  }

  private function expediente($id_expediente) {
    $unidad_arr;
    $expediente = Expediente::find($id_expediente);
    if (is_null($expediente)) {return null;}
      $temp = ExpedientePJ::find($expediente->institucionable_id)->first();
      if (is_null($temp)) {return null;}
      $temp2 = $temp->numero_expediente;
      return $temp2;
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

  private function workflow_actions($orden_captura, $user_email) {
    $actions_arr = [];
    $ordenes_capturas = $orden_captura->captura()->first();
    //$capturas =$captura->tipo()->first();

    if (is_null($ordenes_capturas)) {return $actions_arr; }

    $wf = new Workflow;
    $params = new \stdClass;
    $params->subject_id = $ordenes_capturas->id;
    $params->object_id = $ordenes_capturas->id;
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

  private function get_contra_orden_captura($id, $token) {
    $contra_orden_captura_arr = [];

    $contra_orden_captura = new \stdClass;

    $contra_orden_captura->contra_orden_captura = $this->contra_orden($id);
    $contra_orden_captura->juez = $this->juez($id);
    $contra_orden_captura->fiscal = $this->fiscal($id);
    $contra_orden_captura_arr = $contra_orden_captura;
    return $contra_orden_captura_arr;
  }

  public function contra_orden_captura($orden_captura_id, $token){
    $res = new \stdClass;
    $orden_captura = ContraOrdenCaptura::find($orden_captura_id);
    if (is_null($orden_captura)) { return json_encode($res); }
    $id = $orden_captura->id;

    $result = $this->get_contra_orden_captura($id, $token);

    if (!isset($result)) { return json_encode($res); }
    if (empty($result)) { return json_encode($res); }

    $json_result = json_encode($result);
    return $json_result;

  }

  private function headers(){
    $res = new \stdClass;
    $hdr = new \stdClass;
    $hdr->name = "id_contra_captura";
    $hdr->label = "Numero Contra Orden Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_expediente";
    $hdr->label = "Codigo Expediente";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_orden";
    $hdr->label = "ID Orden Captura";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "fecha_creacion";
    $hdr->label = "Fecha Creacion";
    $res->headers[] = $hdr;
    $hdr = new \stdClass;
    $hdr->name = "id_orden";
    $hdr->label = "ID Orden Captura";
    $res->headers[] = $hdr;
    return $res->headers;
  }

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (ContraOrdenCaptura::All() as $dmp) {
      $row = new \stdClass;
      $row->id_contra_orden_captura = $dmp->id;
      $row->id_expediente = $this->expediente($dmp->id_expediente);
      $row->id_orden_captura = $dmp->id_orden;
      $row->fecha_creacion = date('Y/m/d',strtotime($dmp->fecha_creacion));
      $row->updated_at = date('Y/m/d',strtotime($dmp->updated_at));
      $res->rows[] = $row;
    }
    return $res->rows;
  }

  public function list_contra_orden_captura($token) {
    $res = new \stdClass;
    $res->headers = $this->headers();
    $res->rows = $this->rows($token);
    return $res ;
  }
}
