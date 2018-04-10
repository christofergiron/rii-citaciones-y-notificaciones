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
use App\DocumentoDigital;
use App\Rol;
use App\Fiscal;
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

class StoreContraOrdenCaptura
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $idorden;

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
         "contra_orden_captura" => "required",
         "contra_orden_captura.id_orden" => "required",
         "contra_orden_captura.id_expediente" => "required",
         "contra_orden_captura.fecha_creacion" => "required",
         "contra_orden_captura.razon" => "required",
         "lugar" => "required",
         "lugar.departamento_id" => "required",
         "lugar.municipio_id" => "required",
         "documento" => "required"
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
             $contra_orden_captura = $this->set_contra_orden_captura($arr);
             $this->log::alert(json_encode($contra_orden_captura));
             $orden_estados = $this->set_estados_orden_captura($arr, $this->idorden);
             $this->log::alert(json_encode($orden_estados));
             $orden_estado = $this->set_estado_captura($arr);
             $this->log::alert(json_encode($orden_estado));

            $lugar = $this->set_lugar($arr);
            $this->log::alert(json_encode($lugar));
            $documento = $this->set_documento($arr, $this->response->payload->id = $lugar->id, $this->idorden);
            $this->log::alert(json_encode($documento));
            //$documento_digital = $this->set_documento_digital($this->response->payload->id = $documento->id);
            //$this->log::alert(json_encode($documento_digital));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "Contra Orden Captura realizada Correctamente";
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

    public function set_documento($arr, $lugarid, $idorden) {

        // documento digital
        $documento = new Documento;
        //$doc_digital = DocumentoDigital::create();
        $lugar = Lugar::find($lugarid);

        $contra_orden_captura = ContraOrdenCaptura::find($idorden);

        $documento->expediente_id = $arr["contra_orden_captura"]["id_expediente"];;
        $documento->institucion_id = $arr["documento"]["institucion_id"];
        $documento->dependencia_id = $arr["documento"]["id_dependencia"];
        $documento->titulo = "Contra Orden Captura";
        $documento->descripcion = "una contra orden de captura";
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["contra_orden_captura"]["fecha_creacion"];
        $documento->hora_recepcion = $arr["documento"]["hora_solicitud"];


        $contra_orden_captura->documento()->save($documento);

        return $contra_orden_captura;
    }

    public function set_documento_digital($documentid) {

        // documento digital
        $documento = Documento::find($documentid);
        $doc_digital = DocumentoDigital::create();

       $doc_digital->documento()->save($documento);
    }

    public function set_contra_orden_captura($arr) {

      $contra_orden_captura = new ContraOrdenCaptura;

      $id_orden = $arr["contra_orden_captura"]["id_orden"];
      $orden_captura = OrdenCaptura::find($id_orden);
      if (is_null($orden_captura)) {
          return null;
      }
      $contra_orden_captura->id_orden = $arr["contra_orden_captura"]["id_orden"];
      $contra_orden_captura->fecha_creacion = $arr["contra_orden_captura"]["fecha_creacion"];
      $contra_orden_captura->id_expediente = $arr["contra_orden_captura"]["id_expediente"];
      $contra_orden_captura->razon = $arr["contra_orden_captura"]["razon"];

      if (!is_null($arr["contra_orden_captura"]["id_juez"])) {
          $contra_orden_captura->id_juez = $arr["contra_orden_captura"]["id_juez"];
      }

      if (!is_null($arr["contra_orden_captura"]["id_fiscal"])) {
          $contra_orden_captura->id_fiscal = $arr["contra_orden_captura"]["id_fiscal"];
      }

      if (!is_null($arr["contra_orden_captura"]["descripcion"])) {
          $contra_orden_captura->descripcion = $arr["contra_orden_captura"]["descripcion"];
      }

      $contra_orden_captura->save();
      $temp = $contra_orden_captura;
      $this->idorden = $temp->id;
      return $contra_orden_captura;
    }

    public function set_estados_orden_captura($arr, $idcontraordencaptura) {

      $id_orden = $arr["contra_orden_captura"]["id_orden"];
      $orden_captura = OrdenCaptura::find($id_orden);
      if (is_null($orden_captura)) {
          return null;
      }
        $estado_antiguo = $orden_captura->estado;
        $orden_captura_estado = new OrdenCapturaEstado;

         $orden_captura_estado->id_orden_captura = $id_orden;
         $orden_captura_estado->id_contra_orden = $idcontraordencaptura;
         $orden_captura_estado->id_funcionario = $arr["id_funcionario"];
         $orden_captura_estado->estado_antiguo = $estado_antiguo;
         $orden_captura_estado->estado_nuevo = "Cancelada";
         $orden_captura_estado->fecha = $arr["contra_orden_captura"]["fecha_creacion"];
         $orden_captura_estado->motivo = $arr["contra_orden_captura"]["razon"];
         $orden_captura_estado->save();
    }

    public function set_estado_captura($arr) {

     $id_orden = $arr["contra_orden_captura"]["id_orden"];

     $orden_captura = OrdenCaptura::find($id_orden);
     if (is_null($orden_captura)) {
         return null;
     }
        $orden_captura->estado = "Cancelada";
        $orden_captura->save();
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

    public function set_lugar($arr) {
      // lugar SS
      $lugarpj = new LugarPJ;
      $lugar = new Lugar;


      $lugar->descripcion = "contra orden captura";
      $lugar->caracteristicas = "";

      $lugarpj->departamento_id = $arr["lugar"]["departamento_id"];
      $lugarpj->municipio_id = $arr["lugar"]["municipio_id"];
      $lugarpj->persona_natural_pj_id = $arr["contra_orden_captura"]["id_juez"];

      if (!is_null($arr["lugar"]["barrio_pj_id"])) {
          $lugarpj->barrio_pj_id = $arr["lugar"]["barrio_pj_id"];
      }
      if (!is_null($arr["lugar"]["aldea_pj_id"])) {
          $lugarpj->aldea_pj_id = $arr["lugar"]["aldea_pj_id"];
      }
      if (!is_null($arr["lugar"]["cacerio_pj_id"])) {
          $lugarpj->cacerio_pj_id = $arr["lugar"]["cacerio_pj_id"];
      }
      $lugarpj->save();
      $lugarpj->institucion()->save($lugar);
      return $lugarpj;
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
