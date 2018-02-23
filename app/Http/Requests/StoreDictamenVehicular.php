<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\Solicitud;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreDictamenVehicular //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $dictamen_vehicular;

    public function __construct()
    {
        $this->log = new \Log;
      //  $this->StorePersona = new StorePersona;
        $this->nue_type = "nuevo_dictamen_vehicular";
        $this->rii_nue_type = "Rii_nuevo_dictamen_vehicular";
        $this->dictamen_vehicular = "DictamenVehicular";
        $this->init();
    }

    private function init() {
        $this->response = new \stdClass;
        $this->response->code = 200;
        $this->response->success = true;
        $this->response->message = null;
        $this->response->payload = new \stdClass;
        $this->response->payload->id = null;
    }

    public function rules($arr)
    {

       $this->log::alert('inside rules ....');
       $this->log::alert(json_encode($arr));

       $validator = Validator::make($arr   , [
         "solicitar" => "required",
         "solicitar.token" => "required",
         "solicitar.vehiculo" => "required",
         "solicitar.solicitud" => "required",
         "solicitar.solicitante" => "required",
         "solicitar.propietario" => "required",

         "solicitar.propietario.nombre" => "present|nullable",
         "solicitar.propietario.apellido" => "rpresent|nullable",
         "solicitar.propietario.edad" => "present|nullable",
         "solicitar.propietario.sexo" => "present|nullable"

        "solicitar.vehiculo.tipo_vehiculo" => "required",
        "solicitar.vehiculo.marca_vehiculo" => "required",
        "solicitar.vehiculo.modelo_vehiculo" => "required",
        "solicitar.vehiculo.placa" => "present|nullable",
        "solicitar.vehiculo.numero_motor" => "required",
        "solicitar.vehiculo.kilometraje" => "required|numeric",
        "solicitar.vehiculo.id_propietario" => "present|nullable|numeric",

        "solicitar.solicitud.id_expediente" => "required|numeric",
        "solicitar.solicitud.id_tipo_solicitud" => "required|numeric",
        "solicitar.solicitud.fecha_solicitud" => "required|date|date_format:Y/m/d",
        "solicitar.solicitud.id_fiscal" => "required|numeric",
        "solicitar.solicitud.descripcion" => "required",
        "solicitar.solicitud.departamento_policial" => "required",
        "solicitar.solicitud.observaciones" => "present|nullable",
        "solicitar.solicitud.id_unidad_tecnico" => "required|numeric",
        "solicitar.solicitud.id_tecnico" => "required|numeric",
        "solicitar.solicitud.autorizacion_fiscal" => "required",

        "solicitar.solicitante.id_investigador" => "required|numeric",
        "solicitar.solicitante.id_departamento" => "required|numeric",
        "solicitar.solicitante.id_unidad" => "required|numeric",
        "solicitar.solicitante.placa" => "present|nullable|numeric",
        "solicitar.solicitante.descripcion" => "present|nullable",
        "solicitar.solicitante.departamento_policial" => "required",
        "solicitar.solicitante.rango" => "required",
        "solicitar.solicitante.puesto" => "required"
       ]);

       if ($validator->fails()) {
         $this->response->code = 403;
         $this->response->message = $validator->errors();
         return $this->response;
       }

       return $this->response;
    }

    public function workflow_rules(Array $arr)
    {

       $this->log::alert('inside workflow rules dictamen vehicular ....');
       $this->log::alert(json_encode($arr));

       $action = new Action($arr);
       try {
            $rule = PolyBaseFactory::getAction($arr["action"]);
       }
       catch (\Exception $e) {
            $rule = new DefaultAction();
       }

       $this->response = $action->validate($rule);

       return $this->response;
    }

    public function persist($arr) {
        $user_details = $this->get_user($arr["token"]);
        $user = $this->get_institucion_dependencia($user_details->funcionario_id);
        $user->lugar = $this->get_lugar($user_details->funcionario_id);
        // $this->log::alert(json_encode($res));

        // save expedientes
        try {
            $expediente = $this->set_solicitud($arr);
            $this->log::alert(json_encode($expediente));
            $doc = $this->set_vechiculo($arr);
            $this->log::alert(json_encode($doc));

            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "Solicitud creada exitosamente";
            $this->response->payload->id = $solicitud->id;
        } catch (Exception $e) {
            $this->log::error($e);
            $this->init();
            $this->response->code = 403;
            $this->response->success = false;
            $this->response->message = "Solicitud no creada";
            return $this->response;
        }

        // $this->log::alert('this->response is ...');
        // $this->log::alert(json_encode($this->response));

        return $this->response;
    }

    public function set_solicitud($arr) {
        $soli = Solicitud::create();

        // expediente RII
        $arr = [
            "id_expediente" => $arr["solicitar"]["solicitud"]["id_expediente"],
            "id_tipo_solicitud" => $arr["solicitar"]["solicitud"]["id_tipo_solicitud"],
            "id_solicitante" => $arr["solicitar"]["solicitante"]["id_investigador"],
            "id_funcionario" => $arr["solicitar"]["solicitante"]["id_investigador"],
            "id_lugar" => $arr["solicitar"]["solicitud"]["departamento_policial"],
            "fecha_solicitud" => $arr["solicitar"]["solicitud"]["fecha_solicitud"],
            "fecha_aprobacion" => $arr["solicitar"]["solicitud"]["fecha_aprobacion"],
            "descripcion" => $arr["solicitar"]["solicitud"]["descripcion"],
            "lugar_ejecucion" => $arr["solicitar"]["solicitud"]["id_unidad_tecnico"],
            "ejecutante" => $arr["solicitar"]["solicitud"]["id_tecnico"],
            "autorizacion_fiscal" => $arr["solicitar"]["solicitud"]["autorizacion_fiscal"]
        ];
        $exp = new Solicitud($arr);

        // associate father & child
        $soli->solicitud()->save($exp);

        return $exp;
    }

    public function set_vechiculo($arr) {
        $vehi = Vehiculo::create();

        // expediente RII
        $arr = [
            "tipo_vehiculo" => $arr["solicitar"]["vehiculo"]["tipo_vehiculo"],
            "marca_vehiculo" => $arr["solicitar"]["vehiculo"]["marca_vehiculo"],
            "modelo_vehiculo" => $arr["solicitar"]["vehiculo"]["modelo_vehiculo"],
            "placa" => $arr["solicitar"]["vehiculo"]["placa"],
            "numero_motor" => $arr["solicitar"]["vehiculo"]["numero_motor"],
            "kilometraje" => $arr["solicitar"]["vehiculo"]["kilometraje"],
            "id_propietario" => $arr["solicitar"]["vehiculo"]["id_propietario"]
        ];
        $exp = new Vehiculo($arr);

        // associate father & child
        $vehi->vehiculo()->save($exp);

        return $exp;
    }

    public function store_persona($institucion_id, $persona) {
      $tipo_persona_natural = "pj";

      if ($institucion_id == 2) {
        $tipo_persona_natural = "mp";
      }

      if ($institucion_id == 3) {
        $tipo_persona_natural = "ss";
      }

      $params = new \stdClass;
      $params->persona_natural = new \stdClass;
      $params->tipo_persona_natural = $tipo_persona_natural;
      $params->simple = true;
      $params->tipo_identidad = $persona["tipo_identidad"];
      $params->persona_natural->nombres = $persona["nombres"];
      $params->persona_natural->primer_apellido = $persona["primer_apellido"];
      $params->persona_natural->segundo_apellido = $persona["segundo_apellido"];
      $params->persona_natural->genero = $persona["genero"];
      $params->persona_natural->sexo = $persona["sexo"];

      //$arr = $params;
      $arr = json_decode(json_encode($params), true);

      $this->log::alert("storing_persona...");
      $this->log::alert(json_encode($arr));


      $simple = true;

      $res = $this->StorePersona->rules($arr, $simple);

      $this->log::alert("results in ...");
      $this->log::alert(json_encode($res));

      if ($res->code != 200) {
        $res->error = $res->message;
        return $res;
      }

      $res = $this->StorePersona->persist($arr, $simple);
      $res->success = true;

      # return success response
      return $res->id;
    }

    public function apply_transition(Array $arr) {
       $this->log::alert('inside apply_transition Solicitud ....');
       $this->log::alert(json_encode($arr));

       $act = new Action($arr);
       try {
            $action = PolyBaseFactory::getAction($arr["action"]);
       }
       catch (\Exception $e) {
            $action = new DefaultAction();
       }

       $res = $act->apply_transition($action);

       if (is_null($res)) {
        $this->response->code = 500;
        $this->response->success = false;
        $this->response->message = "acción/transición no permitida en el flujo de trabajo";
        return $this->response;
       }

       $this->response->message = $res;
       return $this->response;
    }


}
