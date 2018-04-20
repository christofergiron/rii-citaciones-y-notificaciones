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
use App\Solicitud;
use App\SolicitudContraOrden;
use App\DocumentoDigital;
use App\Anexo;
use App\Denuncia;
use App\Dependencia;
use App\Institucion;
use App\Rol;
use App\Denunciante;
use App\Imputado;
use App\Ofendido;
use App\RelacionesImputado;
use App\RelacionesImputadoDenunciantes;
use App\RelacionesImputadoOfendidos;
use App\Parentesco;
use App\Fiscal;
use App\DenunciaSS;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreSolicitudContraOrden //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $tipo;
    private $idsolicitud;

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
         "id_fiscal" => "required",
         "solicitud_contra_orden_captura" => "required",
         "solicitud_contra_orden_captura.documento" => "required",
         "solicitud_contra_orden_captura.solicitud" => "required",
         "solicitud_contra_orden_captura.solicitud.fecha" => "required",
         "solicitud_contra_orden_captura.solicitud.descripcion" => "required",
         "solicitud_contra_orden_captura.solicitud.solicitado_por" => "required",
         "solicitud_contra_orden_captura.solicitud_contra_orden.id_expediente" => "required",
         //"solicitud_contra_orden_captura.solicitud_contra_orden.id_juez" => "required",
         "solicitud_contra_orden_captura.solicitud_contra_orden.imputado" => "required",
         "solicitud_contra_orden_captura.solicitud_contra_orden.id_orden_captura" => "required"
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
        //$this->log::alert(json_encode($res));

        // save expedientes
        try {
            $solicitud = $this->set_solicitud($arr);
            $this->log::alert(json_encode($solicitud));
            $solicitud = $this->set_documento($arr, $this->idsolicitud);
            $this->log::alert(json_encode($solicitud));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "solicitud realizada correctamente";
            $this->response->payload->id = $solicitud->id;
        } catch (Exception $e) {
            $this->log::error($e);
            $this->init();
            $this->response->code = 403;
            $this->response->success = false;
            $this->response->message = "error al realizar la solicitud";
            return $this->response;
        }

        // $this->log::alert('this->response is ...');
        // $this->log::alert(json_encode($this->response));

        return $this->response;
    }

    public function set_documento($arr, $idsolicitud) {

        // documento digital
        //echo $idorden;
        $documento = new Documento;
        $temp = $doc = DocumentoDigital::create();
        $iddoc = $temp->id;
        $docdig = DocumentoDigital::find($iddoc);

        $solicitud = Solicitud::find($idsolicitud);
        $idsolicitable = $solicitud->solicitable_id;
        $contra = SolicitudContraOrden::find($idsolicitable);
        $documento->funcionario_id = $arr["id_fiscal"];
        $documento->expediente_id = $arr["solicitud_contra_orden_captura"]["solicitud_contra_orden"]["id_expediente"];
        $documento->institucion_id = $this->get_id_institucion_from_user($arr);
        $documento->dependencia_id = $this->get_id_dependencia_from_user($arr);
        $documento->titulo = "Solicitud Contra Orden Captura";
        $documento->descripcion = "solicitud contra orden captura";
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["solicitud_contra_orden_captura"]["solicitud"]["fecha"];
        $documento->hora_recepcion = $arr["solicitud_contra_orden_captura"]["documento"]["hora_solicitud"];


        $temp2 = $docdig->documento()->save($documento);
        $id_doc = $temp2->id;
        $docu = Documento::find($id_doc);
        $solicitud->documento()->save($docu);

        return $contra;
    }

    public function set_solicitud($arr) {

      $solicitud = new Solicitud;

      $solicitud->fecha = $arr["solicitud_contra_orden_captura"]["solicitud"]["fecha"];
      $solicitud->numero_oficio = $arr["solicitud_contra_orden_captura"]["solicitud"]["numero_oficio"];
      $solicitud->solicitado_por = $arr["solicitud_contra_orden_captura"]["solicitud"]["solicitado_por"];
      $solicitud->institucion = $arr["solicitud_contra_orden_captura"]["solicitud"]["institucion"];
      $solicitud->descripcion = $arr["solicitud_contra_orden_captura"]["solicitud"]["descripcion"];

          $solicitud->titulo = "Solicitud Orden Captura";
          $resul = json_decode($this->set_solicitud_orden($arr), true);
          //echo $resul->id_laboratorio;
          $solicitudContraOrden = SolicitudContraOrden::create($resul);
          $tempo = $solicitudContraOrden->solicitud()->save($solicitud);
          $this->idsolicitud = $tempo->id;
          return $solicitud;

    }

    public function set_solicitud_orden($arr) {

        $solicitud_contra_orden = new SolicitudContraOrden;

            $solicitud_contra_orden->workflow_state = "solicitud_realizada";
            $solicitud_contra_orden->id_orden_captura = $arr["solicitud_contra_orden_captura"]["solicitud_contra_orden"]["id_orden_captura"];
            $solicitud_contra_orden->id_expediente = $arr["solicitud_contra_orden_captura"]["solicitud_contra_orden"]["id_expediente"];
            $solicitud_contra_orden->id_persona = $arr["solicitud_contra_orden_captura"]["solicitud_contra_orden"]["imputado"];
            //$solicitud_contra_orden->id_juez = $arr["solicitud_contra_orden_captura"]["solicitud_contra_orden"]["id_juez"];
            $solicitud_contra_orden->id_juez = 1;
            $solicitud_contra_orden->id_fiscal = $arr["id_fiscal"];
            $solicitud_contra_orden->motivo = $arr["solicitud_contra_orden_captura"]["solicitud"]["descripcion"];

           return $solicitud_contra_orden;
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
      $id_user  = $arr["id_fiscal"];
      $fiscal = Fiscal::find($id_user);
      if (is_null($fiscal)) {return 0;}
      $id_dependencia = $fiscal->fiscalia()->first()->dependencia_id;
      if (is_null($id_dependencia)) {return 0;}
      return $id_dependencia;
    }

    private function get_id_institucion_from_user($arr) {
      $id_user  = $arr["id_fiscal"];
      $fiscal = Fiscal::find($id_user);
      if (is_null($fiscal)) {return 0;}
      $id_institucion = $fiscal->fiscalia()->first();
      if (is_null($id_institucion)) {return 0;}
      $dependen = Dependencia::find($id_institucion->dependencia_id);
      if (is_null($dependen)) {return 0;}
      $id = $dependen->institucion_id;
      return $id;
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
       $this->log::alert('inside apply_transition solicitud analisis ....');
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
