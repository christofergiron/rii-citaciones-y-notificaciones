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
use App\Emplazamiento;
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

class StoreEmplazamiento
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $idemplazamiento;

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
         "emplazamientos" => "required",
         "emplazamientos.id_expediente" => "required|numeric",
         "emplazamientos.organo_juridiccional" => "required",
         "emplazamientos.fecha_creacion" => "required",
         //"emplazamientos.proceso_judicial" => "required",
         "emplazamientos.parte_solicitante" => "required",
         "emplazamientos.tipo_acto_procesal" => "required|numeric",
         "emplazamientos.asunto" => "required",
         "emplazamientos.tipo_acto_procesal" => "required",
         "emplazamientos.lugar_citacion" => "required",
         "emplazamientos.fecha_citacion" => "required",
         "emplazamientos.persona_natural" => "required",
         "emplazamientos.tipo" => "required",
         "documento" => "required",
         "documento.hora_creacion" => "required"
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
            $emplazamiento = $this->set_emplazamiento($arr);
            $this->log::alert(json_encode($emplazamiento));
            $documento = $this->set_documento($arr, $this->idemplazamiento);
            $this->log::alert(json_encode($documento));
            //$documento_digital = $this->set_documento_digital($this->response->payload->id = $documento->id);
            //$this->log::alert(json_encode($documento_digital));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "emplazamiento realizado Correctamente";
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

    public function set_documento($arr, $idemplazamiento) {

      $documento = new Documento;
      $temp = $doc = DocumentoDigital::create();
      $iddoc = $temp->id;
      $docdig = DocumentoDigital::find($iddoc);

        $emplazamiento = Emplazamiento::find($idemplazamiento);

        $documento->expediente_id = $arr["emplazamientos"]["id_expediente"];
        $documento->institucion_id = $this->get_id_institucion_from_user($arr);
        $documento->dependencia_id = $this->get_id_dependencia_from_user($arr);
        $documento->titulo = "Emplazamiento";
        $documento->descripcion = "Emplazamiento";
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["emplazamientos"]["fecha_creacion"];
        $documento->hora_recepcion = $arr["documento"]["hora_creacion"];


        $temp2 = $docdig->documento()->save($documento);
        $id_doc = $temp2->id;
        $docu = Documento::find($id_doc);
        $emplazamiento->documento()->save($docu);

        return $emplazamiento;
    }

    public function set_documento_digital($documentid) {

        // documento digital
        $documento = Documento::find($documentid);
        $doc_digital = DocumentoDigital::create();

       $doc_digital->documento()->save($documento);
    }

    public function set_emplazamiento($arr) {

      $emplazamiento = new Emplazamiento;

      $emplazamiento->id_expediente = $arr["emplazamientos"]["id_expediente"];
      $emplazamiento->id_funcionario = $arr["id_funcionario"];
      $emplazamiento->organo_juridiccional = $arr["emplazamientos"]["organo_juridiccional"];
      $emplazamiento->fecha_creacion = $arr["emplazamientos"]["fecha_creacion"];

      if (!is_null($arr["emplazamientos"]["audiencia"])) {
          $emplazamiento->audiencia = $arr["emplazamientos"]["audiencia"];
      }

      if (!is_null($arr["emplazamientos"]["etapa"])) {
          $emplazamiento->etapa = $arr["emplazamientos"]["etapa"];
      }

      //if (!is_null($arr["emplazamientos"]["proceso_judicial"])) {
        //  $emplazamiento->proceso_judicial = $arr["emplazamientos"]["proceso_judicial"];
      //}

      $emplazamiento->parte_solicitante = $arr["emplazamientos"]["parte_solicitante"];
      $emplazamiento->tipo_parte_solicitante = $arr["emplazamientos"]["tipo_parte_solicitante"];
      $emplazamiento->asunto = $arr["emplazamientos"]["asunto"];
      $emplazamiento->tipo_acto_procesal = $arr["emplazamientos"]["tipo_acto_procesal"];

      $emplazamiento->lugar_citacion = $arr["emplazamientos"]["lugar_citacion"];
      $emplazamiento->fecha_citacion = $arr["emplazamientos"]["fecha_citacion"];

      if (!is_null($arr["emplazamientos"]["observaciones"])) {
          $emplazamiento->observaciones = $arr["emplazamientos"]["observaciones"];
      }

      if (!is_null($arr["emplazamientos"]["persona_natural"])) {
          $emplazamiento->persona_natural = $arr["emplazamientos"]["persona_natural"];
      }

      $emplazamiento->tipo = $arr["emplazamientos"]["tipo"];
      $emplazamiento->notificado = 0;

      $emplazamiento->save();
      $temp = $emplazamiento;
      $this->idemplazamiento = $temp->id;
      return $emplazamiento;
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
