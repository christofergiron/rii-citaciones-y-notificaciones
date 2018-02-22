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
use App\SolicitudRecordHistorial;
use App\Solicitud;
use App\HitoSS;
use App\Victima;
use App\Http\Requests\StorePersona;
use App\Workflow;
use App\Action;
use App\PolyBaseFactory;
use App\DefaultActionSS;
use App\NumeroExpediente;
use App\DefaultSSNUE;

class StoreSolicitudRecordHistorial //extends FormRequest
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

    public function mapDenunciaToDocumento($d){
        $doc = new \stdClass;
        $doc->documento = new \stdClass;
        $doc->documento->numero_documento = $d["ss_denuncia"]["generales"]["encabezado"]["numero_denuncia"];
        $doc->documento->clase_documento = "digital";
        $doc->documento->numero_expediente_referencia = $d["ss_denuncia"]["generales"]["encabezado"]["numero_expediente"];
        $doc->documento->titulo_documento = "Denuncia Recibida de Secretaría Seguridad";
        $doc->documento->descripcion = "Denuncia Recibida de la Secretaría de Seguridad";
        $doc->documento->fecha_recepcion = $d["ss_denuncia"]["generales"]["encabezado"]["fecha_denuncia"];
        $doc->documento->institucion_id = 2;
        $doc->documento->dependencia_id = 32;
        $doc->documento->funcionario_id = null;
        $doc->documento->otra_procedencia = null;
        return $doc;
    }

    public function workflow_rules(Array $arr)
    {

       $this->log::alert('inside workflow rules denuncia ss ....');
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
        // "token" => "required",
        // "anexos" => "present|array",
        // "anexos.*.documento_id" => "present|numeric|nullable|exists:documentos,id",
        //
        // "solicitud_record_historial" => "required",
        // "solicitud_record_historial.*.solicitud.fecha_solicitud" => "required|date_format:Y/m/d",
        // "solicitud_record_historial.*.solicitud.titulo" => "required",
        // "solicitud_record_historial.*.solicitud.oficio" => "required",
        // "solicitud_record_historial.*.solicitud.institucion" => "required",
        // "solicitud_record_historial.*.solicitud.solicitado_por" => "required",
        // "solicitud_record_historial.*.solicitud.descripcion" => "nullable",
        //
        // "solicitud_record_historial.*.hitos_ss.nombre" => "required",
        // "solicitud_record_historial.*.hitos_ss.descripcion" => "required",
        // "solicitud_record_historial.*.hitos_ss.fecha_inicio" => "required|date_format:Y/m/d",
        // "solicitud_record_historial.*.hitos_ss.fecha_fin" => "required|date_format:Y/m/d",
        // "solicitud_record_historial.*.hitos_ss.id_documento" => "present|numeric|nullable|exists:solicitud,id"
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

        // save expedientes
        try {
            //$expediente = $this->set_expediente($arr, $user);
            //$this->log::alert(json_encode($expediente));
            //$doc = $this->set_documento($arr, $expediente, $lugar);
            //$this->log::alert(json_encode($doc));
            //$denuncia = $this->set_denuncia($arr, $doc, $user_details, $user);
            //$this->log::alert(json_encode($denuncia));
            $anexos = $this->set_anexos($arr);
            $this->log::alert(json_encode($anexos));
            $solicitud = $this->set_solicitud($arr, $anexos);
            $this->log::alert(json_encode($solicitud));
            $hitos = $this->set_hitos($arr, $solicitud);
            $this->log::alert(json_encode($hitos));

            $this->init();
            $this->response->message = "Solicitud Creada Exitosamente";
            $this->response->payload->id = $anexos->id;//$denuncia->id;
        } catch (Exception $e) {
            $this->log::error($e);
            $this->init();
            $this->response->code = 403;
            $this->response->success = false;
            $this->response->message = "Solicitud no creada";
            return $this->response;
        }

        return $this->response;
    }

    private function get_lugar($funcionario_id) {

        $res = new \stdClass;
        $res->departamento_id = null;
        $res->municipio_id = null;
        $res->ciudad_id = null;
        $res->aldea_id = null;
        $res->zona_id = null;
        $res->sector_id = null;
        $res->regional_id = null;
        $res->colonia_id = null;

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
        $res->ciudad_id = $lugar->ciudad_ss_id;
        $res->aldea_id = $lugar->aldea_ss_id;
        $res->zona_id = $lugar->zona_ss_id;
        $res->sector_id = $lugar->sector_ss_id;
        $res->regional_id = $lugar->regional_id;
        $res->colonia_id = $lugar->colonia_ss_id;

        $result = json_decode(json_encode($res),true);
        return $result;
    }

    public function generar_numero_expediente($nue_type)
    {

       $this->log::alert('inside generar numero expediente denuncia ss ....');

       $numero_expediente = new NumeroExpediente();
       try {
            $numero_expediente_type = PolyBaseFactory::getNUE($nue_type);
       }
       catch (\Exception $e) {
            $numero_expediente_type = new DefaultSSNUE();
       }

       $this->response = $numero_expediente->generate($numero_expediente_type);

       return $this->response;
    }

    public function set_expediente($arr, $user) {
        // expediente MP
        $arr_ss = [
            "numero_expediente" => $this->generar_numero_expediente($this->nue_type),

        ];
        $exp_ss = ExpedienteSS::create($arr_ss);

        // expediente RII
        $arr = [
            "numero_expediente" => $this->generar_numero_expediente($this->rii_nue_type),
            "fecha_expediente" => $arr["ss_denuncia"]["generales"]["encabezado"]["fecha_denuncia"],
            "institucion_id" => $user->institucion_id,
            "dependencia_id" => $user->dependencia_id,
        ];
        $exp = new Expediente($arr);

        // associate father & child
        $exp_ss->institucion()->save($exp);

        return $exp;
    }

    public function set_documento($arr, $expediente, $_lugar) {
        // lugar
        //$lugar = $this->set_lugar($arr["ss_denuncia"]["generales"]["encabezado"]);
        $lugar = $this->set_lugar($_lugar);

        // documento digital
        $doc_digital = DocumentoDigital::create();
        // documento
        $doc_arr= [
            "expediente_id"=> $expediente->id,
            "institucion_id"=> $expediente->institucion_id,
            "dependencia_id" =>$expediente->dependencia_id,
            "titulo" => $arr["ss_denuncia"]["tipo"],
            "descripcion" => $arr["ss_denuncia"]["tipo"],
            "tags" => Array($arr["ss_denuncia"]["tipo"]),
            "fecha_documento" => $arr["ss_denuncia"]["generales"]["encabezado"]["fecha_denuncia"],
            "hora_recepcion" => $arr["ss_denuncia"]["generales"]["encabezado"]["hora_denuncia"],
            "workflow_state" => "nuevo"
          ];
        $doc = new Documento($doc_arr);

        //associate
        $doc_digital->documento()->save($doc);

        // associate lugar
        $doc->lugares()->save($lugar);

        return $doc;
    }

    private function set_actividades($d, $arr) {

        if (is_null($arr["ss_denuncia"]["generales"]["actividades_confirmacion"])) {
            return true;
        }
        foreach($arr["ss_denuncia"]["generales"]["actividades_confirmacion"] as $a) {
            $actividad_arr = [
                    "descripcion" => $a["nombre_actividad"],
                    "denuncia_fuente_no_formal_id" => $d->id,
            ];

            $actividad = ActividadConfirmacion::create($actividad_arr);
        }
        return true;
    }

    private function get_dependencia($dependencia_id) {
        $res = null;
        $dependencia = Dependencia::find($dependencia_id);

        if (is_null($dependencia)) {
            return $res;
        }

        return $dependencia->nombre;
    }

    public function set_denuncia($arr, $doc, $user_details, $funcionario) {
        $nombre_dependencia = $this->get_dependencia($doc->dependencia_id);
        // denuncia MP escrita|verbal|maie
        switch ($arr["ss_denuncia"]["tipo"]) {
            case "denuncia_fuente_no_formal":
                $denuncia_tipo = DenunciaFuenteNoFormal::create(['fuente_informacion' => $arr["ss_denuncia"]["generales"]["encabezado"]["fuente_informacion"]]);
                $actividades = $this->set_actividades($denuncia_tipo, $arr);
                break;
            default:
                $denuncia_fuente_formal_arr = [
                        'dependencia_id' => $doc->dependencia_id,
                        'delito_id' => $arr["ss_denuncia"]["generales"]["encabezado"]["delito_id"],
                        'unidad_competente' => $nombre_dependencia,//$arr["ss_denuncia"]["generales"]["encabezado"]["unidad_competente"],
                        'entidad' => $funcionario->entidad,//$arr["ss_denuncia"]["generales"]["receptor"]["entidad"],
                        'unidad_competente_receptor' => $nombre_dependencia,//$arr["ss_denuncia"]["generales"]["receptor"]["unidad_competente"],
                        'numero_placa_receptor' => $funcionario->placa,//$arr["ss_denuncia"]["generales"]["receptor"]["numero_placa"]
                ];

                if (!is_null( $arr["ss_denuncia"]["generales"]["encabezado"]["delito_id"])) {
                    $denuncia_fuente_formal_arr["delito_id"] = $arr["ss_denuncia"]["generales"]["encabezado"]["delito_id"];
                }

                $denuncia_tipo = DenunciaFuenteFormal::create($denuncia_fuente_formal_arr);
                break;
        }

        // denuncia SS
        $denuncia_ss_arr = [
            "numero_denuncia" => $this->generar_numero_expediente($this->denuncia_type),
            "workflow_state" => "pendiente_revision",
            'recepcionada_en' => $doc->dependencia_id,//$arr["ss_denuncia"]["generales"]["encabezado"]["recepcionada_en"],
            'funcionario_id' => $user_details->funcionario_id//$arr["ss_denuncia"]["generales"]["receptor"]["funcionario_id"]
          ];
        $denuncia_ss = new DenunciaSS($denuncia_ss_arr);
        $denuncia_tipo->denuncia()->save($denuncia_ss);

        // denuncia
        $denuncia_arr = [
            "fecha_denuncia" => $arr["ss_denuncia"]["generales"]["encabezado"]["fecha_denuncia"],
            "observaciones" => $arr["ss_denuncia"]["generales"]["observaciones"],
            "hora_denuncia" => $arr["ss_denuncia"]["generales"]["encabezado"]["hora_denuncia"],
          ];
        $denuncia = new Denuncia($denuncia_arr);
        $denuncia_ss->institucion()->save($denuncia);

        // associate documento
        $denuncia->documento()->save($doc);

        return $denuncia;
    }

    public function get_sujeto($sujeto,$denuncia_id, $persona_natural_id){
        $sujeto = $sujeto::where('denuncia_id',$denuncia_id)->whereHas('rol', function($r) use($persona_natural_id) {$r->   where('persona_natural_id',$persona_natural_id);})->first();
        return $sujeto;
    }

    public function set_anexos($arr) {
        $anexos_arr = [];
        foreach($arr["anexos"] as $d) {
            if (is_null($d["documento_id"])) { continue; }

            $doc = Documento::find($d["documento_id"]);
            $this->log::alert(json_encode($doc));

            $anexo = new Anexo;
            //$anexo->denuncia()->associate($denuncia);
            $anexo->documento()->save($doc);
            $anexos_arr[] = $anexo;
        }
        return $anexos_arr;
    }

    public function set_solicitud($arr) {

        $solicitud_record_historial_arr = [
          "workflow_state" => "pendiente_revision",
        ];
        $solicitud_record_historial = SolicitudRecordHistorial::create($solicitud_record_historial_arr);

        $solicitud_arr = [
          "fecha" => $arr["solicitud_record_historial"]["solicitud"]["fecha"],
          "titulo" => $arr["solicitud_record_historial"]["solicitud"]["titulo"],
          'numero_oficio' => $arr["solicitud_record_historial"]["solicitud"]["numero_oficio"],
          'institucion' => $arr["solicitud_record_historial"]["solicitud"]["institucion"],
          'solicitado_por' => $arr["solicitud_record_historial"]["solicitud"]["solicitado_por"],
          'descripcion' => $arr["solicitud_record_historial"]["solicitud"]["descripcion"]
        ];
        $solicitud = new Solicitud($solicitud_arr);
        $solicitud_record_historial->solicitud()->save($solicitud);

        // associate anexo
        //$solicitud->anexo()->save($anexos);
        return $solicitud;
    }

    public function set_hitos($arr, $solicitud) {
        $hitos_arr = [
          'nombre' => $arr["solicitud_record_historial"]["hitos_ss"]["nombre"],
          'descripcion' => $arr["solicitud_record_historial"]["hitos_ss"]["descripcion"],
          'fecha_inicio' => $arr["solicitud_record_historial"]["hitos_ss"]["fecha_inicio"],
          'fecha_fin' => $arr["solicitud_record_historial"]["hitos_ss"]["fecha_fin"]
        ];
        foreach($arr["solicitud_record_historial"]["hitos_ss"]["id_documento"] as $solicitud) {
            if (is_null($d["id_documento"])) { continue; }

            $solicitud = Solicitud::find($d["id_documento"]);
            $this->log::alert(json_encode($solicitud));

            $hitos = new HitoSS;
            $hitos->solicitud()->associate($denuncia);
            $hitos_arr[] = $hitos;
        }
        return $hitos_arr;
    }

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
