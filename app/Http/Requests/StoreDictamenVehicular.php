<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\Solicitud;
use App\Dictamen;
use App\DictamenVehicular;
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
         "token" => "required",
         "dictamen" => "required",
         "dictamen.id_autor" => "required",
         "dictamen.id_expediente" => "required",
         "dictamen.fecha_creacion" => "required",
         "dictamen.unidad" => "required",
         //"dictamen.id_solicitud" => "required",
         "dictamen.remitido_A" => "required",
         "dictamen.fecha_envio" => "required"
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
        //$user_details = $this->get_user($arr["token"]);
        //$user = $this->get_institucion_dependencia($user_details->funcionario_id);
        //$user->lugar = $this->get_lugar($user_details->funcionario_id);
        // $this->log::alert(json_encode($res));

        // save expedientes
        try {
            $dictamen = $this->set_dictamen($arr);
            $this->log::alert(json_encode($dictamen));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "Solicitud creada exitosamente";
            $this->response->payload->id = $dictamen->id;
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

    public function set_dictamen($arr) {
        $dictamen = new Dictamen;

        $dictamen->id_autor = $arr["dictamen"]["id_autor"];
        $dictamen->id_expediente = $arr["dictamen"]["id_expediente"];
        $dictamen->fecha_creacion = $arr["dictamen"]["fecha_creacion"];
        $dictamen->unidad = $arr["dictamen"]["id_autor"];

        if (!is_null($arr["dictamen"]["descripcion"])) {
            $dictamen->descripcion = $arr["dictamen"]["descripcion"];
        }
        if (!is_null($arr["dictamen"]["observaciones"])) {
            $dictamen->observaciones = $arr["dictamen"]["observaciones"];
        }
        $dictamen->fecha_envio = $arr["dictamen"]["fecha_envio"];
        $dictamen->remitido_A = $arr["dictamen"]["remitido_A"];

        $resul = json_decode($this->set_dictamen_vehicular($arr), true);
        $dictamenvehicular = DictamenVehicular::create($resul);
        $dictamenvehicular->dictamen()->save($dictamen);
        return $dictamen;
    }

    public function set_dictamen_vehicular($arr) {
        $dictamenvehicular = new DictamenVehicular;

        $dictamenvehicular->workflow_state = "solicitud_recibida";
        $dictamenvehicular->id_solicitud = $arr["dictamen"]["id_solicitud"];

        if (!is_null($arr["dictamen"]["informe_adjunto"])) {
            $dictamenvehicular->informe_adjunto = $arr["dictamen"]["informe_adjunto"];
        }
        if (!is_null($arr["dictamen"]["informe_html"])) {
            $dictamenvehicular->informe_html = $arr["dictamen"]["informe_html"];
        }

        return $dictamenvehicular;
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

    private function get_lugar($funcionario_id) {
        $res = new \stdClass;
        $res->departamento_id = null;
        $res->municipio_id = null;
        $res->aldea_id = null;
        $res->barrio_id = null;
        $res->caserio_id = null;
        $result = json_decode(json_encode($res),true);

        $funcionario = Funcionario::whereId($funcionario_id)->with('dependencia.lugar')->first();

        if (is_null($funcionario)) {
            return $result;
        }

        if (is_null($funcionario->dependencia)) {
            return $result;
        }

        if (is_null($funcionario->dependencia->lugar)) {
            return $result;
        }

        if (is_null($funcionario->dependencia->lugar->institucionable)) {
            return $result;
        }

        $lugar = $funcionario->dependencia->lugar->institucionable;
        $res->departamento_id = $lugar->departamento_id;
        $res->municipio_id = $lugar->municipio_id;
        $res->aldea_id = $lugar->aldea_id;
        $res->barrio_id = $lugar->barrio_id;
        $res->caserio_id = $lugar->caserio_id;

        $result = json_decode(json_encode($res),true);
        return $result;
    }

    public function get_institucion_dependencia($id) {
        $result = new \stdClass;
        try {
            $funcionario = Funcionario::findOrFail($id);
        } catch (\Exception $e) {
            $this->log::error($e);
            return $result;
        }
        $result->dependencia_id = $funcionario->dependencia_id;
        $result->institucion_id = $funcionario->dependencia()->first()->institucion_id;
        return $result;
    }

    public function get_user($token) {
        $user_details = "";
        $passport = new Passport;
        $user_details = $passport->details($token);

        if (empty($user_details)) {return "";}
        if (!property_exists($user_details, "code")) {return "";}
        if ($user_details->code != 200) {return "";}
        if (!property_exists($user_details, "contents")) {return "";}
        $contents = json_decode($user_details->contents);
        if (!property_exists($contents, "success")) {return "";}

        return $contents->success;
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
