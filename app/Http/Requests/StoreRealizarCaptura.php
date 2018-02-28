<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\FuncionarioSS;
use App\Expediente;
use App\ExpedienteSS;
use App\Lugar;
use App\LugarSS;
use App\Documento;
use App\DocumentoDigital;
use App\Anexo;
use App\Denuncia;
use App\Rol;
use App\Denunciante;
use App\Imputado;
use App\Ofendido;
use App\RelacionesImputado;
use App\RelacionesImputadoDenunciantes;
use App\RelacionesImputadoOfendidos;
use App\Parentesco;
use App\Captura;
use App\Flagrancia;
use App\CapturaFinExtradicion;
use App\Detenido;
use App\MenorDetenido;
//use App\ordejudicial;
//use App\requerimiento fiscal;
use App\Fiscal;
use App\DenunciaSS;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreRealizarCaptura //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    //private $captura_type;

    public function __construct()
    {
        $this->log = new \Log;
        $this->StorePersona = new StorePersona;
        $this->nue_type = "ss";
        $this->rii_nue_type = "Rii_N_U_E";
        //$this->$captura_type = "Nueva_Captura";
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
         "funcionario.id_funcionario" => "required",
         "persona_capturada.*.edad" => "required|numeric",
         "captura.descripcion_captura" => "required",
         "captura.fecha_captura" => "required",
         "captura.workflow_state" => "required"
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
            $lugar = $this->set_lugar($arr);
            $this->log::alert(json_encode($lugar));
            $captura = $this->set_captura($arr, $this->response->payload->id = $lugar->id);
            $this->log::alert(json_encode($captura));
            //aqui con el response payload seria la asignacion dinamica del id captura
            $detenido = $this->set_detenido($arr, $this->response->payload->id = $captura->id);
            $this->log::alert(json_encode($detenido));
            //$rol = $this->set_detenido_rol($detenido, $arr);
            //$this->log::alert(json_encode($rol));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "Capturado registrado Correctamente";
            $this->response->payload->id = $captura->id;
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

     //recorda que esta captura solo puede ser de un tipo de herencia
    public function set_captura($arr, $idcaptura) {

      $captura = new Captura;

      if (!is_null($arr["captura"]["id_orden"])) {
          $captura->id_orden = $arr["captura"]["id_orden"];
      }
      if (!is_null($arr["captura"]["id_requerimiento"])) {
          $captura->id_requerimiento = $arr["captura"]["id_requerimiento"];
      }
      if (!is_null($arr["captura"]["id_expediente"])) {
          $captura->id_expediente = $arr["captura"]["id_expediente"];
      }
        $captura->workflow_state = $arr["captura"]["workflow_state"];

        $captura->id_lugar = $idcaptura;
        $captura->id_funcionario = $arr["funcionario"]["id_funcionario"];
        $captura->descripcion_captura = $arr["captura"]["descripcion_captura"];
        if (!is_null($arr["captura"]["observaciones"])) {
            $captura->observaciones = $arr["captura"]["observaciones"];
        }
        $captura->fecha_captura = $arr["captura"]["fecha_captura"];
        //captura flagrancia
        if ($arr["tipo_captura"]["flagrancia"]>0) {

          $resul = json_decode($this->set_captura_flagrancia($arr), true);
          $capturaflagrancia = Flagrancia::create($resul);
          $capturaflagrancia->captura()->save($captura);
          return $captura;
        }

        //captura fin extadicion
        if ($arr["tipo_captura"]["captura_fin_extradicion"]>0) {

          $resul = json_decode($this->set_captura_extradicion($arr), true);
          $capturaExtradicion = CapturaFinExtradicion::create($resul);
          $capturaExtradicion->captura()->save($captura);
          return $captura;
        }
            $captura->save();
            return $captura;
    }

    public function set_captura_flagrancia($arr) {

        $capturaflagrancia = new Flagrancia;

        if (!is_null($arr["captura"]["id_denuncia"])) {
            $capturaflagrancia->id_denuncia = $arr["captura"]["id_denuncia"];
        }
        $capturaflagrancia->workflow_state = $arr["captura"]["workflow_state"];

           return $capturaflagrancia;
    }

    public function set_captura_extradicion($arr) {

      $capturaExtradicion = new CapturaFinExtradicion;

      if (!is_null($arr["captura"]["id_nota_roja"])) {
          $capturaExtradicion->id_nota_roja = $arr["captura"]["id_nota_roja"];
      }
        $capturaExtradicion->workflow_state = $arr["captura"]["workflow_state"];

           return $capturaExtradicion;
    }

    public function set_detenido($arr, $idcaptura) {

      $detenido = new Detenido;
      $menordetenido = new MenorDetenido;
      $rol = new Rol;

     foreach($arr["persona_capturada"] as $d) {
      $rol->persona_natural_id = $d["persona_natural_id"];
      //cambio  asignar la institucion dinamicamente
      $rol->institucion_id = 3;

      if (!is_null($arr["captura"]["id_orden"])) {
          $detenido->id_orden = $arr["captura"]["id_orden"];
      }

      if (!is_null($arr["captura"]["id_requerimiento"])) {
          $detenido->id_requerimiento = $arr["captura"]["id_requerimiento"];
      }

        $detenido->id_captura = $idcaptura;

        if (!is_null($d["fecha_nacimiento"])) {
            $detenido->fecha_nacimiento = $d["fecha_nacimiento"];
        }

        if (!is_null($d["nacionalidad"])) {
            $detenido->nacionalidad = $d["nacionalidad"];
        }
        $detenido->genero = $d["genero"];
        $detenido->sexo = $d["sexo"];
        $detenido->edad = $d["edad"];
        $detenido->lugar_retencion = $arr["unidad"]["lugar_retencion"];

        if (!is_null($arr["funcionario"]["id_fiscal"])) {
            $detenido->id_fiscal = $arr["funcionario"]["id_fiscal"];
        }

        if (!is_null($arr["funcionario"]["id_investigador"])) {
            $detenido->id_investigador = $arr["funcionario"]["id_investigador"];
        }
        //menor detenido
        if ($d["edad"]<18) {
          $resul = json_decode($this->set_menor_detenido($arr, $d), true);
          $menordetenido = MenorDetenido::create($resul);

              $menordetenido->detenido()->save($detenido);
              $detenido->rol()->save($rol);
              return $detenido;
        }
          $detenido->save();
          $detenido->rol()->save($rol);
          return $detenido;
          //fin for
        }
    }

    public function set_menor_detenido($arr, $d) {

      $menordetenido = new MenorDetenido;
      if (!is_null($arr["funcionario"]["id_fiscal"])) {
          $menordetenido->fiscal_niñez = $arr["funcionario"]["id_fiscal"];
      }
       //cambio, sera asignacion dinamica?
      if (!is_null($d["apoderado"])) {
          $menordetenido->apoderado = $d["apoderado"];
      }
        $menordetenido->workflow_state = $arr["captura"]["workflow_state"];

         return $menordetenido;
    }

    public function set_rol($user, $persona) {

        $persona_natural_id = $persona["persona_natural_id"];

        if (
            (is_null($persona["persona_natural_id"])) or
            ($persona["tipo_identidad"] !== "portador")
            ){
            $persona_natural_id = $this->store_persona($user->institucion_id, $persona);
          }

          $rol_arr = [
            "institucion_id" => $user->institucion_id,
            "persona_natural_id" => $persona_natural_id,
          ];
            $rol = new Rol($rol_arr);
            return $rol;
    }

    public function set_detenido_rol($detenido, $arr) {
        $rol = new Rol;
        $rol->persona_natural_id = $arr["persona_natural_id"];
        //cambio  asignar la institucion dinamicamente
        $rol->institucion_id = 3;

      $this->response->payload->id = $detenido->id;
      //echo $this->response->payload->id = $detenido->id;
      $arr_detenido = [
          "id"=>$this->response->payload->id = $detenido->id
        ];

      //$resul = json_decode($this, true);
      //$this->log::alert(json_encode($arr));
      $temp = new Detenido($arr_detenido);
      //echo $temp;
      //$detenidos->denunciante()->associate($temp);

          $temp->rol()->save($rol);
          return $rol;
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


      $lugar->descripcion = "lugar captura";
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
