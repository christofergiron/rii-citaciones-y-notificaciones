<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
use Illuminate\Http\Request;
use App\Passport;
use App\CanalEnvioCN;
use App\Citacion;
use App\Notificacion;
use App\Emplazamiento;
use App\Requerimiento;

class StoreNotificar //extends FormRequest
{
    private $response;
    public function __construct()
    {
        $this->log = new \Log;
        $this->response = new \stdClass;
        $this->response->code = 200;
        $this->response->message = "";
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
        $validator = Validator::make($arr, [
        "canal_envio" => "required|array|min:1",
        "canal_envio.*.canales_envio" => "required",
        "canal_envio.*.medios_envio" => "required",
        "tipo" => "required|numeric",
        "identificador" => "required|numeric",
        "id_funcionario" => "required|numeric"
       ]);

        if ($validator->fails()) {
            $this->response->code = 403;
            $this->response->message = $validator->errors();
            return $this->response;
        }

        return $this->response;
    }

    public function persist($arr)
    {
        // $user_details = $this->get_user($arr["token"]);
        // $user = $this->get_institucion_dependencia($user_details->funcionario_id);
        // $this->log::alert(json_encode($res));

        // save expedientes
        try {
          $notificar = $this->set_medios_notificacion($arr);
            $this->log::alert(json_encode($notificar));

            $this->init();
            $this->response->message = "notificacion correcta";
            $this->response->payload->id = $notificar->id;
        } catch (Exception $e) {
          $this->log::error($e);
          $this->init();
          $this->response->code = 403;
          $this->response->success = false;
          $this->response->message = "error al ingresar la notificacion";
          return $this->response;
        }
        return $this->response;
    }

    public function set_medios_notificacion($arr)
    {

   foreach($arr["canal_envio"] as $d) {
      $canal_envio = new CanalEnvioCN;
      $canal_envio->id_funcionario = $arr["id_funcionario"];
      $canal_envio->canal_envio = $d["canales_envio"];
      $canal_envio->medios_envio = $d["medios_envio"];

      if (!is_null($d["observaciones"])) {
        $canal_envio->observaciones = $d["observaciones"];
      }
      $canal_envio->save();
   }

    if ($arr["tipo"] == 1) {
      $canal_envio->id_citacion = $arr["identificador"];
      $citacion = Citacion::find($arr["identificador"]);
      if (is_null($citacion)) {
          return null;
      }
        $citacion->notificado = 1;
        $citacion->save();
        return $citacion;
    }
    if ($arr["tipo"] == 2) {
      $canal_envio->id_notificacion = $arr["identificador"];
      $notificacion = Notificacion::find($arr["identificador"]);
      if (is_null($notificacion)) {
          return null;
      }
        $notificacion->notificado = 1;
        $notificacion->save();
        return $notificacion;
    }
    if ($arr["tipo"] == 3) {
      $canal_envio->id_emplazamiento = $arr["identificador"];
      $emplazamiento = Emplazamiento::find($arr["identificador"]);
      if (is_null($emplazamiento)) {
          return null;
      }
        $emplazamiento->notificado = 1;
        $emplazamiento->save();
        return $emplazamiento;
    }
    if ($arr["tipo"] == 4) {
      $canal_envio->id_requerimiento = $arr["identificador"];
      $requerimiento = Requerimiento::find($arr["identificador"]);
      if (is_null($requerimiento)) {
          return null;
      }
        $requerimiento->notificado = 1;
        $requerimiento->save();
        return $requerimiento;
    }

    }
}
