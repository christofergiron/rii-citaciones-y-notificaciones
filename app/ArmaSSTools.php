<?php
namespace App;
use Validator;
use App\Passport;
class ArmaSSTools
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
  private function acciones($token, $arma) {
      $acciones = [];
      $user_email = $this->get_email_from_token($token);
      if (!isset($user_email)) { return $acciones; }
      if (empty($user_email)) {return $acciones; }
      $acciones = $this->workflow_actions($arma, $user_email);
      $acciones[] = 'Arma';
      return $acciones;
  }

  private function headers(){
      $res = new \stdClass;
      $hdr = new \stdClass;
      $hdr->name = "id";
      $hdr->label = "ID";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "id_tipo_arma";
      $hdr->label = "Tipo Arma";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "calibre";
      $hdr->label = "Calibre";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "modelo";
      $hdr->label = "Modelo";
      $res->headers[] = $hdr;
      $hdr = new \stdClass;
      $hdr->name = "serial";
      $hdr->label = "Serial";
      $res->headers[] = $hdr;
      return $res->headers;
  }
  private function rows($token) {
    $res = new \stdClass;
    //$res->rows[]=[]; this fails is no data is returned...
      foreach (ArmaSS::All() as $arma) {
        $row = new \stdClass;
        $row->id = $arma->id;
        $row->id_tipo_arma = $arma->id_tipo_arma;
        $row->calibre = $arma->calibre;
        $row->modelo = $arma->modelo;
        $row->serial = $arma->serial;
        //$row->actions = $this->acciones($token, $solicitud);
        //$row->updated_at = date('Y/m/d',strtotime($solicitud->updated_at));
        //$row->workflow_state = $solicitud->solicitable()->get();
        $res->rows[] = $row;
      }
      return $res->rows;
  }
  private function obtener_arma($arma, $token) {
      $arma_arr = [];
      $user_email = $this->get_email_from_token($token);
      if(empty($user_email)){
        $user_email = "ssworker@gmail.com";
      }
      $this->log::alert("email: ".$user_email);

      if (!isset($user_email)) { return $arma_arr; }
      if (empty($user_email)) {return $arma_arr; }
      $item = new \stdClass;
      // $item->updated_at = date('Y/m/d',strtotime($solicitud_record_historial->updated_at));
      // $item->dependencia = 'Secretaría de Seguridad';
      if (isset($exp)) {
         $this->log::info("valor: ".print_r($exp));
      }else{
        $this->log::info("viene nulo");
      }
      //$item->titulo_documento = $this->titulo_documento($juez);
      $item->id = $arma->id;
      $item->id_tipo_arma = $arma->id_tipo_arma;
      $item->descripcion = $arma->descripcion;
      $item->calibre = $arma->calibre;
      $item->modelo = $arma->modelo;
      $item->nombre = $arma->nombre;
      $item->serial = $arma->serial;
      $item->marca = $arma->marca;
      $arma_arr[] = $item;
      return $arma_arr;
  }

  public function get_arma($arma_id, $token){
      $res = new \stdClass;

      $arma = ArmaSS::find($arma_id);
      if (is_null($arma)) {
        return json_encode($res);
      }
      $id = $arma->id;
      $this->log::alert("objeto actual: ".json_encode($arma));

      $result = $this->obtener_arma($arma, $token);

      if (!isset($result)) { return json_encode($res); }
      if (empty($result)) { return json_encode($res); }

      $res->arma = $result[0];

      $json_result = json_encode($res);
      return $json_result;
  }
  public function ss_list_arma($token) {
      $res = new \stdClass;
      $res->headers = $this->headers();
      $res->rows = $this->rows($token);
      return $res ;
  }
}
?>
