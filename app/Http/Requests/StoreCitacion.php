<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\Funcionario;
use App\FuncionarioPJ;
use App\Expediente;
use App\ExpedientePJ;
use App\Lugar;
use App\LugarPJ;
use App\Documento;
use App\Citacion;
use App\CanalEnvioCN;
use App\DocumentoDigital;
use App\Rol;
use App\Fiscal;
use App\Fiscalia;
use App\Dependencia;
use App\OrdenCaptura;
use App\OrdenCapturaEstado;
use App\ContraOrdenCaptura;
use App\Juez;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreCitacion
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $idcitacion;

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
         "id_funcionario" => "required",
         "citaciones" => "required",
         "citaciones.id_expediente" => "required|integer",
         "citaciones.organo_juridiccional" => "required",
         "citaciones.fecha_creacion" => "required",
         //"citaciones.proceso_judicial" => "required",
         "citaciones.parte_solicitante" => "required",
         "citaciones.asunto" => "required",
         "citaciones.tipo_acto_procesal" => "required",
         "citaciones.lugar_citacion" => "required",
         "citaciones.fecha_citacion" => "required",
         "citaciones.persona_natural" => "required",
         "citaciones.tipo" => "required",
         "documento" => "required",
         "documento.hora_creacion" => "required",
         "envio" => "required|array|min:1",
         "envio.*.canal_envio" => "required",
         "envio.*.medios_envio" => "required"
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
        //$user_details = $this->get_user($arr["token"]);
        //$user = $this->get_institucion_dependencia($user_details->funcionario_id);
        //$user->lugar = $this->get_lugar($user_details->funcionario_id);
      //  $this->log::alert(json_encode($res));

       try {
            $citacion = $this->set_citacion($arr);
            $this->log::alert(json_encode($citacion));
            $medios_citacion = $this->set_citacion_medios($arr, $this->idcitacion);
            $this->log::alert(json_encode($medios_citacion));
            $documento = $this->set_documento($arr, $this->idcitacion);
            $this->log::alert(json_encode($documento));
            //$documento_digital = $this->set_documento_digital($this->response->payload->id = $documento->id);
            //$this->log::alert(json_encode($documento_digital));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "citacion realizada Correctamente";
            $this->response->payload->id = $documento->id;

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

    public function set_documento($arr, $idorden) {

      $documento = new Documento;
      $temp = $doc = DocumentoDigital::create();
      $iddoc = $temp->id;
      $docdig = DocumentoDigital::find($iddoc);

        $citacion = Citacion::find($idorden);

        $documento->expediente_id = $arr["citaciones"]["id_expediente"];
        $documento->institucion_id = $this->get_id_institucion_from_user($arr);
        $documento->dependencia_id = $this->get_id_dependencia_from_user($arr);
        $documento->titulo = "Citacion";
        $documento->descripcion = "Citacion";
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["citaciones"]["fecha_creacion"];
        $documento->hora_recepcion = $arr["documento"]["hora_creacion"];


        $temp2 = $docdig->documento()->save($documento);
        $id_doc = $temp2->id;
        $docu = Documento::find($id_doc);
        $citacion->documento()->save($docu);

        return $citacion;
    }

    public function set_documento_digital($documentid) {

        // documento digital
        $documento = Documento::find($documentid);
        $doc_digital = DocumentoDigital::create();

       $doc_digital->documento()->save($documento);
    }

    public function set_citacion($arr) {

      $citacion = new Citacion;

      $citacion->id_expediente = $arr["citaciones"]["id_expediente"];
      $citacion->id_funcionario = $arr["id_funcionario"];
      $citacion->organo_juridiccional = $arr["citaciones"]["organo_juridiccional"];
      $citacion->fecha_creacion = $arr["citaciones"]["fecha_creacion"];

      if (!is_null($arr["citaciones"]["audiencia"])) {
          $citacion->audiencia = $arr["citaciones"]["audiencia"];
      }

      if (!is_null($arr["citaciones"]["etapa"])) {
          $citacion->etapa = $arr["citaciones"]["etapa"];
      }

      //if (!is_null($arr["citaciones"]["proceso_judicial"])) {
        //  $citacion->proceso_judicial = $arr["citaciones"]["proceso_judicial"];
      //}

      $citacion->parte_solicitante = $arr["citaciones"]["parte_solicitante"];
      $citacion->asunto = $arr["citaciones"]["asunto"];
      $citacion->tipo_acto_procesal = $arr["citaciones"]["tipo_acto_procesal"];

      $citacion->lugar_citacion = $arr["citaciones"]["lugar_citacion"];
      $citacion->fecha_citacion = $arr["citaciones"]["fecha_citacion"];

      if (!is_null($arr["citaciones"]["observaciones"])) {
          $citacion->observaciones = $arr["citaciones"]["observaciones"];
      }

      if (!is_null($arr["citaciones"]["persona_natural"])) {
          $citacion->persona_natural = $arr["citaciones"]["persona_natural"];
      }

      $citacion->tipo = $arr["citaciones"]["tipo"];

      $citacion->save();
      $temp = $citacion;
      $this->idcitacion = $temp->id;
      return $citacion;
    }

    public function set_citacion_medios($arr, $idcitacion) {

     foreach($arr["envio"] as $e) {

       $canales_envio = new CanalEnvioCN;

         $canales_envio->id_citacion = $idcitacion;
         $canales_envio->canal_envio = $e["canal_envio"];
         $canales_envio->medios_envio = $e["medios_envio"];

           $canales_envio->save();
           //return $orden_delito;
         }
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

    private function get_id_dependencia_from_user($arr) {
      $id_user  = $arr["id_funcionario"];
      $funcionariopj = FuncionarioPJ::find($id_user);
      if (is_null($funcionariopj)) {return 0;}
      $id_dependencia = $funcionariopj->institucion()->first()->dependencia_id;
      if (is_null($id_dependencia)) {return 0;}
      return $id_dependencia;
    }

    private function get_id_institucion_from_user($arr) {
      $id_user  = $arr["id_funcionario"];
      $funcionariopj = FuncionarioPJ::find($id_user);
      if (is_null($funcionariopj)) {return 0;}
      $id_institucion = $funcionariopj->institucion()->first()->dependencia_id;
      if (is_null($id_institucion)) {return 0;}
      $dependen = Dependencia::find($id_institucion);
      if (is_null($dependen)) {return 0;}
      $id = $dependen->institucion_id;
      return $id;
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
