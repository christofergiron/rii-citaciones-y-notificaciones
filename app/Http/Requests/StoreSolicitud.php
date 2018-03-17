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
use App\SolicitudAllanamiento;
use App\SolicitudAnalisis;
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
use App\Fiscal;
use App\DenunciaSS;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreSolicitud //extends FormRequest
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $tipo;

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
         "documento" => "required",
         "documento.institucion_id" => "required",
         "lugar_solicitud" => "required",
         "lugar_solicitud.departamento_id" => "required",
         "lugar_solicitud.municipio_id" => "required",
         "solicitud" => "required",
         "solicitud.tipo_solicitud" => "required",
         "solicitud.fecha" => "required",
         "solicitud.id_lugar" => "required",
         "solicitud.descripcion" => "required",
         "solicitud.solicitado_por" => "required"
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
            $lugar = $this->set_lugar($arr);
            $this->log::alert(json_encode($lugar));
            $solicitud = $this->set_documento($arr, $this->response->payload->id = $lugar->id);
            $this->log::alert(json_encode($solicitud));
            //$solicitud = $this->set_solicitud($arr);
            //$this->log::alert(json_encode($solicitud));
            //aqui con el response payload seria la asignacion dinamica del id captura
            //$detenido = $this->set_detenido($arr, $this->response->payload->id = $solicitud->id);
            //$this->log::alert(json_encode($detenido));
            //$rol = $this->set_detenido_rol($detenido, $arr);
            //$this->log::alert(json_encode($rol));
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

    public function set_documento($arr, $lugarid) {

        // documento digital
        $documento = new Documento;
        $doc_digital = DocumentoDigital::create();
        $lugar = Lugar::find($lugarid);

        $temp = $this->set_solicitud($arr);
        $solicitud_id = $temp->id;
        $solicitud = Solicitud::find($solicitud_id);

        $documento->expediente_id = 0;
        //cambio
        //$documento->tipoable_id = 0;
        //$documento->tipoable_type = "asdasd";
        $documento->expediente_id = 0;
        $documento->institucion_id = $arr["documento"]["institucion_id"];
        $documento->dependencia_id = $arr["solicitud"]["id_lugar"];
        $documento->titulo = $this->tipo;
        $documento->descripcion = $arr["solicitud"]["descripcion"];;
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["solicitud"]["fecha"];
        $documento->hora_recepcion = $arr["solicitud"]["hora_solicitud"];

        $solicitud->documento()->save($documento);

        return $solicitud;
    }

    public function set_solicitud($arr) {

      $solicitud = new Solicitud;

      $solicitud->fecha = $arr["solicitud"]["fecha"];
      $solicitud->numero_oficio = $arr["solicitud"]["numero_oficio"];
      $solicitud->solicitado_por = $arr["solicitud"]["solicitado_por"];
      $solicitud->institucion = $arr["solicitud"]["id_lugar"];
      $solicitud->descripcion = $arr["solicitud"]["descripcion"];

      //solicitud allanamiento
        if ($arr["solicitud"]["tipo_solicitud"]==0) {
          $solicitud->titulo = "allanamiento";
          $this->tipo = "allanamiento";
          $resul = json_decode($this->set_solicitud_allanamiento($arr), true);
          $solicitudAllanamiento = SolicitudAllanamiento::create($resul);
          $solicitudAllanamiento->solicitud()->save($solicitud);
          return $solicitud;
        }

      //solicitud analisis
        if ($arr["solicitud"]["tipo_solicitud"]==1) {
          $solicitud->titulo = "analisis";
          $this->tipo = "analisis";
          $resul = json_decode($this->set_solicitud_analisis($arr), true);
          //echo $resul->id_laboratorio;
          $solicitudAnalisis = SolicitudAnalisis::create($resul);
          $solicitudAnalisis->solicitud()->save($solicitud);
          return $solicitud;
        }

      //solicitud dictamen
        if ($arr["solicitud"]["tipo_solicitud"]==2) {
          $solicitud->titulo = "dictamen_vehicular";
          $this->tipo = "dictamen_vehicular";
          //cambio dictamen vehicular
          //$resul = json_decode($this->set_solicitud_dictamen_vehicular($arr), true);
          //$solicitudAnalisis = $solicitudAnalisis::create($resul);
          //$solicitudAnalisis->solicitud()->save($solicitud);
          //return $solicitud;
        }

    }

    public function set_solicitud_allanamiento($arr) {

        $solicitud_allanamiento = new SolicitudAllanamiento;

        $solicitud_allanamiento->workflow_state = "solicitud_recibida";

        if (!is_null($arr["solicitud"]["descripcion"])) {
            $solicitud_allanamiento->descripcion = $arr["solicitud"]["descripcion"];
        }

        if (isset($arr["solicitud_allanamiento"]["numero_evidencias_encontradas"])) {
            $solicitud_allanamiento->numero_evidencias_encontradas = $arr["solicitud_allanamiento"]["numero_evidencias_encontradas"];
        }

        if (isset($arr["solicitud_allanamiento"]["descripcion_evidencias"])) {
            $solicitud_allanamiento->descripcion_evidencias = $arr["solicitud_allanamiento"]["descripcion_evidencias"];
        }
           return $solicitud_allanamiento;
    }

    public function set_solicitud_analisis($arr) {

        $solicitud_analisis = new SolicitudAnalisis;

        $solicitud_analisis->workflow_state = "solicitud_recibida";

        if (!is_null($arr["solicitud"]["descripcion"])) {
            $solicitud_analisis->descripcion = $arr["solicitud"]["descripcion"];
        }
        if (isset($arr["solicitud_analisis"]["id_laboratorio"])) {
            $solicitud_analisis->id_laboratorio = $arr["solicitud_analisis"]["id_laboratorio"];
        }
        if (isset($arr["solicitud_analisis"]["nombre_laboratorio"])) {
            $solicitud_analisis->nombre_laboratorio = $arr["solicitud_analisis"]["nombre_laboratorio"];
        }
        if (isset($arr["solicitud_analisis"]["tipo_analisis"])) {
            $solicitud_analisis->tipo_analisis = $arr["solicitud_analisis"]["tipo_analisis"];
        }
        if (isset($arr["solicitud_analisis"]["nombre_analisis"])) {
            $solicitud_analisis->nombre_analisis = $arr["solicitud_analisis"]["nombre_analisis"];
        }
        if (isset($arr["solicitud_analisis"]["detalle_analisis"])) {
            $solicitud_analisis->detalle_analisis = $arr["solicitud_analisis"]["detalle_analisis"];
        }
           return $solicitud_analisis;
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
      $lugarss = new LugarSS;
      $lugar = new Lugar;


      $lugar->descripcion = "lugar documento";
      $lugar->caracteristicas = "";

      $lugarss->regional_id = $arr["lugar_solicitud"]["regional_id"];
      $lugarss->departamento_id = $arr["lugar_solicitud"]["departamento_id"];
      $lugarss->municipio_id = $arr["lugar_solicitud"]["municipio_id"];

      if (!is_null($arr["lugar_solicitud"]["ciudad_ss_id"])) {
          $lugarss->ciudad_ss_id = $arr["lugar_solicitud"]["ciudad_ss_id"];
      }
      if (!is_null($arr["lugar_solicitud"]["colonia_ss_id"])) {
          $lugarss->colonia_ss_id = $arr["lugar_solicitud"]["colonia_ss_id"];
      }
      if (!is_null($arr["lugar_solicitud"]["aldea_ss_id"])) {
          $lugarss->aldea_ss_id = $arr["lugar_solicitud"]["aldea_ss_id"];
      }
      if (!is_null($arr["lugar_solicitud"]["sector_ss_id"])) {
          $lugarss->sector_ss_id = $arr["lugar_solicitud"]["sector_ss_id"];
      }
      if (!is_null($arr["lugar_solicitud"]["zona_ss_id"])) {
          $lugarss->zona_ss_id = $arr["lugar_solicitud"]["zona_ss_id"];
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
