<?php
namespace App;
use Validator;
use App\Passport;

class HitoSolicitudSSTools
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

  private function headers(){
      $res = new \stdClass;
      $hdr = new \stdClass;
      $hdr->name = "id";
      $hdr->label = "ID";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "nombre";
      $hdr->label = "Nombre";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "fecha_inicio";
      $hdr->label = "Fecha Inicio";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "fecha_fin";
      $hdr->label = "Fecha Fin";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "id_solicitud";
      $hdr->label = "Id de Solicitud";
      $res->headers[] = $hdr;
      return $res->headers;
  }
  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...

    foreach (HitoSolicitudSS::all() as $hito) {
      $row = new \stdClass;
      $row->id = $hito->id;
      $row->nombre = $hito->nombre;
      $row->fecha_inicio = $hito->fecha_inicio;
      $row->fecha_fin = $hito->fecha_fin;
      $row->id_solicitud = $hito->id_solicitud;
      //$row->actions = $this->acciones($token, $solicitud);
      //$row->updated_at = date('Y/m/d',strtotime($solicitud->updated_at));
      //$row->workflow_state = $solicitud->solicitable()->get();
      $res->rows[] = $row;
    }
    return $res->rows;
  }
  private function obtener_hito($hito, $token) {
      $hito_arr = [];
      $user_email = $this->get_email_from_token($token);
      if(empty($user_email)){
        $user_email = "ssworker@gmail.com";
      }
      $this->log::alert("email: ".$user_email);

      if (!isset($user_email)) { return $hito_arr; }
      if (empty($user_email)) {return $hito_arr; }
      $item = new \stdClass;
      // $item->updated_at = date('Y/m/d',strtotime($solicitud_record_historial->updated_at));
      // $item->dependencia = 'Secretaría de Seguridad';
      if (isset($exp)) {
         $this->log::info("valor: ".print_r($exp));
      }else{
        $this->log::info("viene nulo");
      }
      //$item->titulo_documento = $this->titulo_documento($juez);
      $item->id = $hito->id;
      $item->nombre = $hito->nombre;
      $item->descripcion = $hito->descripcion;
      $item->fecha_inicio = $hito->fecha_inicio;
      $item->fecha_fin = $hito->fecha_fin;
      $item->id_solicitud = $hito->id_solicitud;
      $hito_arr[] = $item;
      return $hito_arr;
  }

  public function get_hito($hito_id, $token){
      $res = new \stdClass;

      $hito = HitoSolicitudSS::find($hito_id);
      if (is_null($hito)) {
        return json_encode($res);
      }
      $id = $hito->id;
      $this->log::alert("objeto actual: ".json_encode($hito));

      $result = $this->obtener_hito($hito, $token);

      if (!isset($result)) { return json_encode($res); }
      if (empty($result)) { return json_encode($res); }

      $res->hito = $result[0];

      $json_result = json_encode($res);
      return $json_result;
  }
  public function ss_list_hito($token) {
      $res = new \stdClass;
      $res->headers = $this->headers();
      $res->rows = $this->rows($token);
      return $res ;
  }
}
?>
