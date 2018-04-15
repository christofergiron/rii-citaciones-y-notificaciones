<?php

namespace App;

use App\Solicitud;
use App\SolicitudContraOrden;
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

class SolicitudContraOrdenTools
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

  private function tipo_solicitud($id) {
      $tipo_solicitud_arr;
      $contra = SolicitudContraOrden::find($id);
      $solicitud = $contra->solicitud()->first();
      if (is_null($solicitud)) { return json_encode($res); }

      $tipo_solicitud = new \stdClass;

        $solicitud_orden = SolicitudContraOrden::find($id);
        $imputado_id = $solicitud_orden->id_persona;
        $imputado = Imputado::find($imputado_id);
        if (!is_null($imputado)) {
          $persona_natural_id = $imputado->rol()->first()->persona_natural_id;
          $persona = $this->get_persona_natural($persona_natural_id);
          $tipo_solicitud->id_imputado = $persona->persona_natural_id;
          $tipo_solicitud->nombre_imputado = $persona->nombres;
          $tipo_solicitud->apellidos_imputado = $persona->primer_apellido.', '.$persona->segundo_apellido;
        }

        $tipo_solicitud->id_orden_captura = $solicitud_orden->id_orden_captura;
        $tipo_solicitud->id_expediente = $solicitud_orden->id_expediente;
        $tipo_solicitud->motivo = $solicitud->descripcion;

        //solicitud no aprobada
        if (!is_null($solicitud_orden->razon_rechazo)) {
            $tipo_solicitud->fecha_rechazo = date('Y/m/d',strtotime($solicitud_orden->fecha_rechazo));
            $tipo_solicitud->razon_rechazo = $solicitud_orden->razon_rechazo;
            $tipo_solicitud->estado = $solicitud_orden->workflow_state;
            $tipo_solicitud->motivo = $solicitud_orden->motivo;
            $tipo_solicitud_arr = $tipo_solicitud;
            return $tipo_solicitud_arr;
          }
          //solicitud aprobada
          if (!is_null($solicitud_orden->fecha_aprovacion)) {
              $tipo_solicitud->fecha_aprovacion = date('Y/m/d',strtotime($solicitud_orden->fecha_aprovacion));
              $tipo_solicitud->id_orden_captura = $solicitud_orden->id_orden_captura;
              $tipo_solicitud->id_contra_orden = $solicitud_orden->id_contra_orden;
              $tipo_solicitud->estado = $solicitud_orden->workflow_state;
              $tipo_solicitud_arr = $tipo_solicitud;
              return $tipo_solicitud_arr;
            }
            $tipo_solicitud_arr = $tipo_solicitud;
            return $tipo_solicitud_arr;
  }

  private function solicitudes($id) {
    $solicitud_arr;
    //$tipo  "App\SolicitudOrdenCaptura";
    $solicitudorden = SolicitudContraOrden::find($id);
    $solicitud = $solicitudorden->solicitud()->first();
    $solicitudes = new \stdClass;

    $solicitudes->numero_solicitud = $solicitud->id;
    $solicitudes->tipo_solicitud = $solicitud->titulo;
    $solicitudes->numero_oficio = $solicitud->numero_oficio;
    $solicitudes->solicitante = $solicitud->solicitado_por;
    $solicitudes->fecha_solicitud = date('Y/m/d',strtotime($solicitud->fecha));

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
    $solicitudes->solicitud_orden = $this->tipo_solicitud($id);
    $solicitudes->unidad = $this->unidad($solicitud);
    $solicitud_arr[] = $solicitudes;
    return $solicitud_arr;
  }

  public function solicitud_orden($solicitud_id, $token){
    $res = new \stdClass;
    $solicitud = SolicitudContraOrden::find($solicitud_id);
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
    $hdr->name = "estado";
    $hdr->label = "Estado";
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

  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
    foreach (SolicitudContraOrden::All() as $dmp) {
      $row = new \stdClass;
      $row->numero_solicitud = $dmp->id;
      $row->fecha_solicitud = date('Y/m/d',strtotime($dmp->fecha));
      $row->unidad = $this->unidadtabla($dmp);
      $row->estado = $dmp->workflow_state;
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
