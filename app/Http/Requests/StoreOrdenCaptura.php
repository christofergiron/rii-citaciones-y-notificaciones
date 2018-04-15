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
use App\OrdenCaptura;
use App\OrdenCapturaDelito;
use App\OrdenCapturaEstado;
use App\OrdenCapturaMenor;
use App\OrdenCapturaPersona;
use App\OrdenCapturaPersonaMenor;
use App\OrdenCapturaPersonaNatural;
use App\OrdenCapturaVehiculo;
use App\OrdenCapturaXVehiculo;
use App\Juez;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultAction;
use App\DefaultMPNUE;

class StoreOrdenCaptura
{

    private $response;
    private $nue_type;
    private $rii_nue_type;
    private $idorden;
    private $institucion_id;
    private $dependencia_id;

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
         "orden_captura" => "required",
         "orden_captura.fecha_creacion" => "required",
         "orden_captura.id_expediente" => "required",
         "delito" => "required|array|min:1",
         "id_funcionario" => "required",
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

        // save expedientes
       try {
             $orden_captura = $this->set_orden_captura($arr);
             $this->log::alert(json_encode($orden_captura));
             $orden_estados = $this->set_estados_orden_captura($arr, $this->idorden);
             $this->log::alert(json_encode($orden_estados));
                  $persona_orden = $this->set_personas_orden($arr, $this->response->payload->id = $orden_captura->id);
                  $delitos = $this->set_delitos($arr, $this->idorden);
            $documento = $this->set_documento($arr, $this->idorden);
            $this->log::alert(json_encode($documento));
            //$documento_digital = $this->set_documento_digital($this->response->payload->id = $documento->id);
            //$this->log::alert(json_encode($documento_digital));
            $this->log::alert('this->response is ...');
            $this->log::alert(json_encode($this->response));

            $this->init();
            $this->response->message = "Orden Captura realizada Correctamente";
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

        // documento digital
        //echo $idorden;
        $documento = new Documento;
        $temp = $doc = DocumentoDigital::create();
        $iddoc = $temp->id;
        $docdig = DocumentoDigital::find($iddoc);

        $orden_captura = OrdenCaptura::find($idorden);
        $documento->funcionario_id = $arr["id_funcionario"];
        $documento->expediente_id = $arr["orden_captura"]["id_expediente"];;
        $documento->institucion_id = $this->get_id_institucion_from_user($arr);
        $documento->dependencia_id = $this->get_id_dependencia_from_user($arr);
        $documento->titulo = "Orden Captura";
        $documento->descripcion = "orden de captura";
        //$documento->tags = Array($arr["documento"]["tipo"]);
        $documento->fecha_documento = $arr["orden_captura"]["fecha_creacion"];
        $documento->hora_recepcion = $arr["documento"]["hora_solicitud"];


        $temp2 = $docdig->documento()->save($documento);
        $id_doc = $temp2->id;
        $docu = Documento::find($id_doc);
        $orden_captura->documento()->save($docu);

        return $orden_captura;
    }

    public function set_documento_digital($documentid) {

        // documento digital
        $documento = Documento::find($documentid);
        $doc_digital = DocumentoDigital::create();

       $doc_digital->documento()->save($documento);
    }

    public function set_orden_captura($arr) {

      $orden_captura = new OrdenCaptura;

      $orden_captura->fecha_creacion = $arr["orden_captura"]["fecha_creacion"];
      $orden_captura->estado = "Vigente";
      $orden_captura->id_expediente = $arr["orden_captura"]["id_expediente"];

      if (isset($arr["orden_captura"]["audiencia"])) {
          $orden_captura->audiencia = $arr["orden_captura"]["audiencia"];
      }

      if (isset($arr["orden_captura"]["auto_motivo"])) {
          $orden_captura->auto_motivo = $arr["orden_captura"]["auto_motivo"];
      }

          $orden_captura->id_juez = $arr["id_funcionario"];

          $resul = json_decode($this->set_orden_captura_persona($arr), true);
          $orden_captura_persona = OrdenCapturaPersona::create($resul);
          $temp2 = $orden_captura_persona->orden()->save($orden_captura);
          $this->idorden = $temp2->id;
          return $orden_captura_persona;
    }
   //hijos capturas
    public function set_orden_captura_persona($arr) {

        $orden_captura_persona = new OrdenCapturaPersona;

        $orden_captura_persona->workflow_state = "Creada";

        if (!is_null($arr["orden_captura"]["observaciones"])) {
            $orden_captura_persona->observaciones = $arr["orden_captura"]["observaciones"];
        }
           return $orden_captura_persona;
    }

    public function set_orden_captura_menor($arr) {

        $orden_captura_menor = new OrdenCapturaMenor;

        $orden_captura_menor->workflow_state = "Creada";

        if (!is_null($arr["orden_captura"]["observaciones"])) {
            $orden_captura_menor->observaciones = $arr["orden_captura"]["observaciones"];
        }
           return $orden_captura_menor;
    }

    public function set_orden_captura_vehiculo($arr) {

        $orden_captura_vehiculo = new OrdenCapturaVehiculo;

        $orden_captura_vehiculo->workflow_state = "Creada";

        if (!is_null($arr["orden_captura"]["observaciones"])) {
            $orden_captura_vehiculo->observaciones = $arr["orden_captura"]["observaciones"];
        }
           return $orden_captura_vehiculo;
    }

    public function set_delitos($arr, $idordencaptura) {

     foreach($arr["delito"] as $d) {
       $orden_delito = new OrdenCapturaDelito;

         $orden_delito->id_orden_captura = $idordencaptura;

        if (isset($d["tipo_delito"])) {
            $orden_delito->tipo_delito = $d["tipo_delito"];
        }

        if (isset($d["delito"])) {
            $orden_delito->delito = $d["delito"];
        }

        if (isset($d["id_victima"])) {
            $orden_delito->id_victima = $d["id_victima"];
        }

        if (isset($d["nombre_victima"])) {
            $orden_delito->nombre_victima = $d["nombre_victima"];
        }

           $orden_delito->save();
           //return $orden_delito;
         }
    }
   //tablas n a n
    public function set_personas_orden($arr, $idordencaptura) {

        $orden_persona = new OrdenCapturaPersonaNatural;

         $orden_persona->id_orden_captura = $idordencaptura;

        if (isset($arr["orden_captura"]["id_persona"])) {
            $orden_persona->id_persona = $arr["orden_captura"]["id_persona"];
        }

           $orden_persona->save();
    }

    public function set_menor_orden($arr, $idordencaptura) {

        $orden_menor = new OrdenCapturaPersonaMenor;

         $orden_menor->id_orden_captura = $idordencaptura;

        if (isset($arr["orden_captura"]["id_persona"])) {
            $orden_menor->id_persona_menor = $arr["orden_captura"]["id_persona"];
        }

           $orden_menor->save();
    }

    public function set_vehiculo_orden($arr, $idordencaptura) {

        $orden_vehiculo = new OrdenCapturaXVehiculo;

         $orden_vehiculo->id_orden_captura = $idordencaptura;

        if (isset($arr["orden_captura"]["id_vehiculo"])) {
            $orden_vehiculo->id_vehiculo = $arr["orden_captura"]["id_vehiculo"];
        }

           $orden_vehiculo->save();
    }

    public function set_estados_orden_captura($arr, $idordencaptura) {

        $orden_captura_estado = new OrdenCapturaEstado;

         $orden_captura_estado->id_orden_captura = $idordencaptura;
         $orden_captura_estado->id_funcionario = $arr["id_funcionario"];
         $orden_captura_estado->estado_nuevo = "Vigente";
         $orden_captura_estado->fecha = $arr["orden_captura"]["fecha_creacion"];
         $orden_captura_estado->motivo = "Creacion Orden Captura";
         $orden_captura_estado->save();
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

    private function get_email_from_token($token) {
      $user_email = "";
      $passport = new Passport;
      $res = $passport->details($token);

      if (!isset($res)) {return $user_email; }
      if (!($res->code == 200)) {return $user_email; }

      $this->log::alert('inside get_email_from_token');
      $this->log::alert(json_encode($res));

      $contents = json_decode($res->contents);
      if (property_exists($contents, "success")) {
        $user_email = $contents->success->email;
      }

      return $user_email;
    }

    private function get_id_dependencia_from_user($arr) {
      $id_user  = $arr["id_funcionario"];
      $juez = Juez::find($id_user);
      if (is_null($juez)) {return 0;}
      $id_dependencia = $juez->id_dependencia;
      return $id_dependencia;
    }

    private function get_id_institucion_from_user($arr) {
      $id_user  = $arr["id_funcionario"];
      $juez = Juez::find($id_user);
      if (is_null($juez)) {return 0;}
      $id_dependencia = $juez->id_dependencia;
      $dependencia = Dependencia::find($id_dependencia);
      if (is_null($dependencia)) {return 0;}
      $id_institucion = $dependencia->institucion_id;
      return $id_institucion;
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


      $lugar->descripcion = "orden captura";
      $lugar->caracteristicas = "";

      $lugarpj->departamento_id = $arr["lugar"]["departamento_id"];
      $lugarpj->municipio_id = $arr["lugar"]["municipio_id"];
      $lugarpj->persona_natural_pj_id = $arr["orden_captura"]["id_juez"];

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
