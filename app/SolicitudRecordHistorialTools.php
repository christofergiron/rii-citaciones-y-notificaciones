<?php
namespace App;
use Validator;
use App\Passport;
class SolicitudRecordHistorialTools
{
  private $ss_require;
    private $log;
    public function __construct()
    {
        $this->log = new  \Log;
        //$this->$ss_require =  Array("");
        //$this->$ss_require =  Array("nacionalidad", "tipo_documento_identidad", "numero_documento_identidad","nombres", "primer_apellido", "segundo_apellido", "fecha_nacimiento", "sexo", "genero", "categoria");
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
  private function acciones($token, $solicitud_record_historial) {
      $acciones = [];
      $user_email = $this->get_email_from_token($token);
      if (!isset($user_email)) { return $acciones; }
      if (empty($user_email)) {return $acciones; }
      $acciones = $this->workflow_actions($solicitud_record_historial, $user_email);
      $acciones[] = 'Solicitud';
      return $acciones;
  }

  private function headers(){
      $res = new \stdClass;
      $hdr = new \stdClass;
      $hdr->name = "id";
      $hdr->label = "ID";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "fecha";
      $hdr->label = "Fecha";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "titulo";
      $hdr->label = "Proceso Investigativo";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "numero_oficio";
      $hdr->label = "Numero de Oficio";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "institucion";
      $hdr->label = "Institucion";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "solicitado_por";
      $hdr->label = "Solicitado Por: ";
      $res->headers[] = $hdr;
      return $res->headers;
  }
  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
      $iteracion = Solicitud::where('solicitable_type','App\SolicitudRecordHistorial')->get();

      foreach ( $iteracion as $solicitud) {
        $row = new \stdClass;
        $row->id = $solicitud->id;
        $row->fecha = $solicitud->fecha;
        $row->titulo = $solicitud->titulo;
        $row->numero_oficio = $solicitud->numero_oficio;
        $row->institucion = $solicitud->institucion;
        $row->solicitado_por = $solicitud->solicitado_por;
        //$row->actions = $this->acciones($token, $solicitud);
        //$row->updated_at = date('Y/m/d',strtotime($solicitud->updated_at));
        //$row->workflow_state = $solicitud->solicitable()->get();
        $res->rows[] = $row;
      }
      return $res->rows;
  }
  private function obtener_solicitud($solicitud, $token) {
      $solicitud_arr = [];
      $user_email = $this->get_email_from_token($token);
      if(empty($user_email)){
        $user_email = "ssworker@gmail.com";
      }
      $this->log::alert("email: ".$user_email);

      if (!isset($user_email)) { return $solicitud_arr; }
      if (empty($user_email)) {return $solicitud_arr; }
      $item = new \stdClass;
      // $item->updated_at = date('Y/m/d',strtotime($solicitud_record_historial->updated_at));
      // $item->dependencia = 'Secretaría de Seguridad';
      if (isset($exp)) {
         $this->log::info("valor: ".print_r($exp));
      }else{
        $this->log::info("viene nulo");
      }
      //$item->titulo_documento = $this->titulo_documento($juez);
      $item->fecha = $solicitud->fecha;
      $item->titulo = $solicitud->titulo;
      $item->numero_oficio = $solicitud->numero_oficio;
      $item->institucion = $solicitud->institucion;
      $item->solicitado_por = $solicitud->solicitado_por;
      $item->solicitud_record_historial = $solicitud->solicitable()->get();
      $item->procesos_investigativos = $solicitud->hitos_ss()->get();
      $solicitud_arr[] = $item;
      return $solicitud_arr;
  }

  public function get_solicitud($solicitud_id, $token){
      $res = new \stdClass;

      $solicitud = Solicitud::find($solicitud_id);
      if (is_null($solicitud)) {
        return json_encode($res);
      }
      $id = $solicitud->id;
      $this->log::alert("objeto actual: ".json_encode($solicitud));

      $result = $this->obtener_solicitud($solicitud, $token);

      if (!isset($result)) { return json_encode($res); }
      if (empty($result)) { return json_encode($res); }

      $res->solicitud = $result[0];

      $json_result = json_encode($res);
      return $json_result;
  }
  public function ss_list_solicitudes($token) {
      $res = new \stdClass;
      $res->headers = $this->headers();
      $res->rows = $this->rows($token);
      return $res ;
  }
}
?>
