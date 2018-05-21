<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\Dependencia;
use App\Vehiculo;
use App\FuncionarioSS;
use App\Lugar;
use App\LugarSS;
use App\PersonaNatural;
use App\Rol;
use App\Fiscal;
use App\DenunciaSS;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreVehiculo //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;

    public function __construct()
    {
        $this->log = new \Log;
        $this->StorePersona = new StorePersona;
        $this->nue_type = "ss";
        $this->rii_nue_type = "Rii_N_U_E";
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
         //"id_funcionario" => "required",
         "vehiculo" => "required|array|min:1",
         "vehiculo.*.tipo" => "required",
         "vehiculo.*.marca" => "required",
         "vehiculo.*.modelo" => "required",
         "vehiculo.*.año" => "required",
         "vehiculo.*.placa" => "required",
         "vehiculo.*.color" => "required",
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

       $this->log::alert('inside workflow rules denuncia mp ....');
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
        //$user = $this->get_institucion_dependencia($user_details->funcionario_id);
        //$user->lugar = $this->get_lugar($user_details->funcionario_id);
      //  $this->log::alert(json_encode($res));

        // save expedientes
        try {
            $vehiculo = $this->set_vehiculo_denuncia($arr);
            $this->log::alert(json_encode($vehiculo));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "vehiculo registrado Correctamente";
            $this->response->payload->id = $vehiculo->id;

        } catch (Exception $e) {
            $this->log::error($e);
            $this->init();
            $this->response->code = 403;
            $this->response->success = false;
            $this->response->message = "error al ingresar la captura";
            return $this->response;
        }

        // $this->log::alert('this->response is ...');
        // $this->log::alert(json_encode($this->response));

        return $this->response;
    }

    public function set_vehiculo_denuncia($arr) {

      $vehiculo = new Vehiculo;

     foreach($arr["vehiculo"] as $d) {

       $vehiculo->tipo = $d["tipo"];
       $vehiculo->marca = $d["marca"];
       $vehiculo->modelo = $d["modelo"];
       $vehiculo->placa = $d["placa"];
       $vehiculo->año = $d["año"];
       $vehiculo->color = $d["color"];

       if (isset($d["estado"])) {
          $vehiculo->estado = $d["estado"];
       }
       if (isset($d["motor"])) {
          $vehiculo->motor = $d["motor"];
       }
       if (isset($d["chasis"])) {
          $vehiculo->chasis = $d["chasis"];
       }
       if (isset($d["vin"])) {
          $vehiculo->vin = $d["vin"];
       }
       if (isset($d["descripcion"])) {
          $vehiculo->descripcion = $d["descripcion"];
       }
       if (isset($d["id_propietario"])) {
          $vehiculo->id_propietario = $d["id_propietario"];
       }
       if (isset($d["licencia"])) {
          $vehiculo->licencia = $d["licencia"];
       }
       if (isset($d["unidad"])) {
          $vehiculo->unidad = $d["unidad"];
       }

       
        if (isset($arr["id_funcionario"])) {
            $vehiculo->id_funcionario = $arr["id_funcionario"];
        }

        if (isset($d["fecha_registro"])) {
            $vehiculo->fecha_registro = $d["fecha_registro"];
        }else{
            $vehiculo->fecha_registro = new \DateTime();
        }

       if (isset($d["id_denuncia"])) {
          $vehiculo->id_denuncia = $d["id_denuncia"];
       }
       if (isset($d["id_orden_captura"])) {
          $vehiculo->id_denuncia = $d["id_denuncia"];
       }
       if (isset($d["id_lugar"])) {
         $lugar = $this->set_lugar($arr);
         $idlugar = $lugar->id;
          $vehiculo->id_denuncia = $idlugar;
       }
        $vehiculo->save();
        //$detenido->rol()->save($rol);
        return $vehiculo;
          //fin for
        }
    }

    public function get_sujeto($sujeto,$denuncia_id, $persona_natural_id){
      //  $sujeto = $sujeto::where('denuncia_id',$denuncia_id)->whereHas('rol', function($r) use($persona_natural_id) {$r->   //where('persona_natural_id',$persona_natural_id);})->first();
        return $sujeto;
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

    public function set_lugar($arr) {
      // lugar SS
      $lugarss = new LugarSS;
      $lugar = new Lugar;


      $lugar->descripcion = "Lugar Localizacion Vehiculo";
      $lugar->caracteristicas = "";

      $lugarss->regional_id = $arr["lugar_captura"]["regional_id"];
      $lugarss->departamento_id = $arr["lugar_captura"]["departamento_id"];
      $lugarss->municipio_id = $arr["lugar_captura"]["municipio_id"];

      if (!is_null($arr["lugar_captura"]["colonia_ss_id"])) {
          $lugarss->ciudad_ss_id = $arr["lugar_captura"]["ciudad_ss_id"];
      }
      if (!is_null($arr["lugar_captura"]["colonia_ss_id"])) {
          $lugarss->colonia_ss_id = $arr["lugar_captura"]["colonia_ss_id"];
      }
      if (!is_null($arr["lugar_captura"]["aldea_ss_id"])) {
          $lugarss->aldea_ss_id = $arr["lugar_captura"]["aldea_ss_id"];
      }
      if (!is_null($arr["lugar_captura"]["sector_ss_id"])) {
          $lugarss->sector_ss_id = $arr["lugar_captura"]["sector_ss_id"];
      }
      if (!is_null($arr["lugar_captura"]["zona_ss_id"])) {
          $lugarss->zona_ss_id = $arr["lugar_captura"]["zona_ss_id"];
      }
      $lugarss->save();
      $lugarss->institucion()->save($lugar);
      return $lugarss;
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

    public function store_persona($persona) {

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
       $this->log::alert('inside apply_transition realizar captura ....');
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
