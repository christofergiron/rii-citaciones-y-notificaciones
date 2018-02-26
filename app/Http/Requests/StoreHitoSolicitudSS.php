<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\Funcionario;
use App\Expediente;
use App\ExpedienteSS;
use App\Lugar;
use App\LugarSS;
use App\Documento;
use App\DocumentoDigital;
use App\Anexo;
use App\Denuncia;
use App\DenunciaSS;
use App\DenunciaFuenteFormal;
use App\DenunciaFuenteNoFormal;
use App\Dependencia;
use App\ActividadConfirmacion;
use App\Hecho;
use App\Rol;
use App\Denunciante;
use App\Sospechoso;
use App\Testigo;
use App\HitoSolicitudSS;
use App\Victima;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultActionSS;
use App\NumeroExpediente;
use App\DefaultSSNUE;

class StoreHitoSolicitudSS //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $denuncia_type;

    public function __construct()
    {
        $this->log = new \Log;
        $this->StorePersona = new StorePersona;
        $this->nue_type = "ss";
        $this->rii_nue_type = "Rii_N_U_E";
        $this->denuncia_type = "Denuncia_S_S_N_U_E";
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

    public function workflow_rules(Array $arr)
    {

       $this->log::alert('inside workflow rules solicitud ss ....');
       $this->log::alert(json_encode($arr));

       $action = new Action($arr);
       try {
            $rule = PolyBaseFactory::getAction($arr["action"]);
       }
       catch (\Exception $e) {
            $rule = new DefaultActionSS();
       }

       $this->response = $action->validate($rule);

       return $this->response;
    }

    public function rules($arr)
    {
       $validator = Validator::make($arr   , [
        "token" => "required",
        "procesos_investigativos" => "required",
        "procesos_investigativos" => "required|array|min:1",
        "procesos_investigativos.*.nombre" => "required",
        "procesos_investigativos.*.descripcion" => "required",
        "procesos_investigativos.*.fecha_inicio" => "required|date_format:Y/m/d",
        "procesos_investigativos.*.fecha_fin" => "required|date_format:Y/m/d",
        "procesos_investigativos.*.id_solicitud" => "required"
     ]);

       if ($validator->fails()) {
         $this->response->code = 403;
         $this->response->message = $validator->errors();
         return $this->response;
       }

       return $this->response;
    }

    public function persist($arr) {
        $user_details = $this->get_user($arr["token"]);
        //$user = $this->get_institucion_dependencia($user_details->funcionario_id);
        //$lugar = $this->get_lugar($user_details->funcionario_id);
        // $this->log::alert(json_encode($res));

        try {
            $hito = $this->set_hitos($arr);
            $this->log::alert(json_encode($hito));

            $this->init();
            $this->response->message = "Procesos Creados Exitosamente";            
        } catch (Exception $e) {
            $this->log::error($e);
            $this->init();
            $this->response->code = 403;
            $this->response->success = false;
            $this->response->message = "Proceso no creado";
            return $this->response;
        }

        return $this->response;
    }

    public function set_hitos($arr) {
        $_arr = [];
        foreach($arr["procesos_investigativos"] as $d) {
            $hitos_arr = [
                "nombre" => $d["nombre"],
                "descripcion" => $d["descripcion"],
                "fecha_inicio" => $d["fecha_inicio"],
                "fecha_fin" => $d["fecha_fin"],
                "fecha_fin" => $d["fecha_fin"],
                "id_solicitud" => $d["id_solicitud"],
              ];
            $hitos = new HitoSolicitudSS($hitos_arr);
            $hitos->save();
            $_arr[] = $hitos;
        }
        return $_arr;
    }

    // public function set_hitos($arr, $solicitud) {
    //   $_arr = [];
    //   foreach($arr["solicitud_record_historial"]["procesos_investigativos"] as $d) {
    //       $hitos_arr = [
    //           "nombre" => $d["nombre"],
    //           "descripcion" => $d["descripcion"],
    //           "fecha_inicio" => $d["fecha_inicio"],
    //           "fecha_fin" => $d["fecha_fin"],
    //         ];
    //       $hitos = new HitoSolicitudSS($hitos_arr);
    //       $hitos->solicitud()->associate($solicitud);
    //       $hitos->save();
    //       $_arr[] = $hitos;
    //   }
    //   return $_arr;
    // }

    public function set_lugar($arr) {
        // lugar MP

        $arr_lugar_ss = [
            "departamento_id"=>$arr["departamento_id"]
          ];

        if (isset($arr["municipio_id"])) {
            $arr_lugar_ss["municipio_id"] = $arr["municipio_id"];
        }
        if (isset($arr["ciudad_id"])) {$arr_lugar_ss["ciudad_ss_id"] = $arr["ciudad_id"];}
        if (isset($arr["colonia_id"])) {$arr_lugar_ss["colonia_ss_id"] = $arr["colonia_id"];}
        if (isset($arr["regional_id"])) {$arr_lugar_ss["regional_id"] = $arr["regional_id"];}
        if (isset($arr["sector_id"])) {$arr_lugar_ss["sector_ss_id"] = $arr["sector_id"]; }
        if (isset($arr["aldea_id"])) {$arr_lugar_ss["aldea_ss_id"] = $arr["aldea_id"];}

        $this->log::alert($arr_lugar_ss);

        $lugar_ss = LugarSS::create($arr_lugar_ss);

        // lugar
        $arr_lugar = [
            "descripcion"=> "",
            "caracteristicas" => ""
        ];
        $lugar = new Lugar($arr_lugar);
        $lugar_ss->institucion()->save($lugar);

        return $lugar;
    }

    public function get_institucion_dependencia($id) {
        $result = new \stdClass;
        try {
            $funcionario = Funcionario::findOrFail($id);
        } catch (\Exception $e) {
            $this->log::error($e);
            return $result;
        }
        $funcionario_ss = $funcionario->institucionable()->first();
        $result->dependencia_id = $funcionario->dependencia_id;
        $result->institucion_id = $funcionario->dependencia()->first()->institucion_id;
        $result->placa = $funcionario_ss->placa;
        $result->entidad = $funcionario_ss->entidad;
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
       $this->log::alert('inside apply_transition denuncia ss ....');
       $this->log::alert(json_encode($arr));

       $act = new Action($arr);
       try {
            $action = PolyBaseFactory::getAction($arr["action"]);
       }
       catch (\Exception $e) {
            $action = new DefaultActionSS();
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
