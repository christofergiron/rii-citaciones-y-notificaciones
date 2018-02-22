<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use Validator;
// use Illuminate\Http\Request;
// use App\Passport;
use App\Persona;
use App\PersonaNatural;
use App\PersonaNaturalSS;
use App\PersonaNaturalMP;
use App\PersonaNaturalPJ;
use App\Anonimo;
use App\Protegido;
use App\NoPortador;
use App\Desconocido;
use App\DeOficio;
use App\Lugar;
use App\LugarMP;
use App\LugarSS;
use App\LugarPJ;
// use App\LugarPJ;

class StorePersona //extends FormRequest
{

    private $response;
    public function __construct()
    {
        $this->log = new \Log;
        $this->response = new \stdClass;
        $this->response->code = 200;
        $this->response->message = "";
    }

    private function validate_persona_mp($arr){
        $validator = Validator::make($arr   , [
             "persona_natural_mp" => "required",
             "persona_natural_mp.categorias" => "present|nullable|array",
             "persona_natural_mp.tipo_identificacion" => "present|nullable|array",
             "persona_natural_mp.alias" => "present|nullable|array",
             "persona_natural_mp.direcciones" => "present|nullable|array",
             "persona_natural_mp.direcciones.*.descripcion" => "required",
             "persona_natural_mp.direcciones.*.caracteristicas" => "present|nullable",
             "persona_natural_mp.direcciones.*.departamento_id" => "required|nullable|numeric|exists:departamentos,id",
             "persona_natural_mp.direcciones.*.municipio_id" => "required|nullable|exists:municipios,id",
             "persona_natural_mp.direcciones.*.aldea_mp_id" => "present|nullable|exists:aldeas_mp,id",
             "persona_natural_mp.direcciones.*.barrio_mp_id" => "present|nullable|exists:barrios_mp,id",
             "persona_natural_mp.direcciones.*.caserio_mp_id" => "present|nullable|exists:caserios_mp,id"
            ]);
        return $validator;
    }

    private function validate_persona_ss($arr){
       $validator = Validator::make($arr   , [
         "persona_natural_ss" => "required",
         "persona_natural_ss.residente" => "present|nullable",
         "persona_natural_ss.direcciones" => "present|nullable|array",
         "persona_natural_ss.direcciones.*.descripcion" => "required",
         "persona_natural_ss.direcciones.*.caracteristicas" => "present|nullable",
         "persona_natural_ss.direcciones.*.departamento_id" => "required|nullable|numeric|exists:departamentos,id",
         "persona_natural_ss.direcciones.*.municipio_id" => "present|nullable|exists:municipios,id",
         "persona_natural_ss.direcciones.*.ciudad_ss_id" => "present|nullable|exists:ciudades_ss,id",
         "persona_natural_ss.direcciones.*.colonia_ss_id" => "present|nullable|exists:colonias_ss,id",
         "persona_natural_ss.direcciones.*.zona_ss_id" => "present|nullable|exists:zonas_ss,id",
         "persona_natural_ss.direcciones.*.sector_ss_id" => "present|nullable|exists:sectores_ss,id",
         "persona_natural_ss.direcciones.*.aldea_ss_id" => "present|nullable|exists:aldeas_ss,id",
         "persona_natural_ss.direcciones.*.regional_id" => "present|nullable|exists:regionales,id"                                                   
        ]);        
       return $validator;
    }

    private function validate_persona_pj($arr) {
       $validator = Validator::make($arr   , [
         "persona_natural_pj" => "required",
         "persona_natural_pj.direcciones" => "present|nullable|array",
         "persona_natural_pj.direcciones.*.descripcion" => "required",
         "persona_natural_pj.direcciones.*.caracteristicas" => "present|nullable",
         "persona_natural_pj.direcciones.*.departamento_id" => "required|nullable|exists:departamentos,id",
         "persona_natural_pj.direcciones.*.municipio_id" => "required|nullable|exists:municipios,id"
        ]);
        return $validator; 
    }

    private function simple_rules($arr) {
       $validator = Validator::make($arr, [
         "tipo_persona_natural" => "required",
         "tipo_identidad" => "required",
         "persona_natural" => "required",
         "persona_natural.nombres" => "present|nullable",
         "persona_natural.primer_apellido" => "present|nullable",
         "persona_natural.segundo_apellido" => "present|nullable",
         "persona_natural.genero" => "present|nullable",
         "persona_natural.sexo" => "present|nullable",
       ]);        

       return $validator;
    }


    private function normal_rules($arr) {
       $validator = Validator::make($arr   , [
         "token" => "required",
         "tipo_persona_natural" => "required",
         "persona_natural" => "required",
         "persona_natural.nombres" => "required",
         "persona_natural.primer_apellido" => "required",
         "persona_natural.segundo_apellido" => "present|nullable",
         "persona_natural.genero" => "present|nullable",
         "persona_natural.sexo" => "required",
         "persona_natural.nacionalidad" => "required",
         "persona_natural.tipo_documento_identidad" => "required",
         "persona_natural.numero_documento_identidad" => "required",
         "persona_natural.fecha_nacimiento" => "required|date_format:Y/m/d",
         "persona_natural.edad" => "present|nullable",
         "persona_natural.estado_civil" => "required",
         "persona_natural.ocupaciones" => "present|nullable|array",
         "persona_natural.profesiones" => "present|nullable|array",
         "persona_natural.etnias" => "present|nullable|array",
         "persona_natural.escolaridad" => "present|nullable|array",
         "persona_natural.discapacidades" => "present|nullable|array",
         "persona_natural.pueblo_indigena" => "present|nullable|array",
         "persona_natural.telefonos" => "present|nullable|array",
         "persona_natural.correos_electronicos" => "present|nullable|array",
         "persona_natural.apartados_postales" => "present|nullable|array",
         "persona_natural.telefono_notificacion" => "present|nullable",
         "persona_natural.correo_notificacion" => "present|nullable",
         "persona_natural.apartado_postal_notificacion" => "present|nullable",
         "persona_menor" => "present|nullable",
         "persona_menor.representante_legal_id" => "present|nullable|exists:personas_naturales,id",
         "persona_abogado" => "present|nullable",
         "persona_abogado.identificacion_colegio_abogados" => "present|nullable"
       ]);

       return $validator;        
    }

    public function rules($arr, $simple)
    {

       $this->log::alert(json_encode($arr));

       if ($simple) {
          $validator = $this->simple_rules($arr);
       }
       else {
          $validator = $this->normal_rules($arr);
       }
       

       if ($validator->fails()) {
         $this->response->code = 403;
         $this->response->message = $validator->errors();
         return $this->response;
       }

       if (($arr["tipo_persona_natural"] == "mp" )  and (!$simple)) {

           $validator = $this->validate_persona_mp($arr);

           if ($validator->fails()) {
             $this->response->code = 403;
             $this->response->message = $validator->errors();
             return $this->response;
           }
       }

       if (($arr["tipo_persona_natural"] == "pj") and (!$simple)) {

           $validator = $this->validate_pesona_pj($arr);

           if ($validator->fails()) {
             $this->response->code = 403;
             $this->response->message = $validator->errors();
             return $this->response;
           }
       }

       if (($arr["tipo_persona_natural"] == "ss") and (!$simple)) {

           $validator = $this->validate_persona_ss($arr);

           if ($validator->fails()) {
             $this->response->code = 403;
             $this->response->message = $validator->errors();
             return $this->response;
           }
       }

       return $this->response;
    }

    public function persist($arr, $simple) {

        try {
            // set persona
            if ($simple) {
                $persona = $this->set_persona_simple($arr);
            }
            else {
                $persona = $this->set_persona($arr);
            }
            $this->log::alert(json_encode($persona));
                        
        } catch (Exception $e) {
            $this->log::error($e);
            return false;
        }

        return $persona;
    }

    private function add_direccion($direccion, $persona, $institucion) {
        $lugar_arr = [
            "descripcion" => $direccion["descripcion"],
            "caracteristicas" => $direccion["caracteristicas"]
        ];

        $lugar = new Lugar($lugar_arr);

        if ($institucion == 'mp'){
            $lugar_mp_arr = [
                "departamento_id" => $direccion["departamento_id"],
                "municipio_id" => $direccion["municipio_id"],
                "barrio_mp_id" => $direccion["barrio_mp_id"],
                "aldea_mp_id" => $direccion["aldea_mp_id"],
                "caserio_mp_id" => $direccion["caserio_mp_id"],
                "persona_natural_mp_id" => $persona["id"]
            ];
            $lugar_tipo = LugarMP::create($lugar_mp_arr);
        }

        if ($institucion == 'pj'){
            $lugar_pj_arr = [
                "departamento_id" => $direccion["departamento_id"],
                "municipio_id" => $direccion["municipio_id"],
                "persona_natural_pj_id" => $persona["id"]
            ];
            $lugar_tipo = LugarPJ::create($lugar_pj_arr);
        }

        if ($institucion == 'ss'){
            $lugar_ss_arr = [
                "departamento_id" => $direccion["departamento_id"],
                "municipio_id" => $direccion["municipio_id"],
                "ciudad_ss_id" => $direccion["ciudad_ss_id"],
                "colonia_ss_id" => $direccion["colonia_ss_id"],
                "sector_ss_id" => $direccion["sector_ss_id"],
                "aldea_ss_id" => $direccion["aldea_ss_id"],
                "regional_id" => $direccion["regional_id"],
                "zona_ss_id" => $direccion["zona_ss_id"],                
                "persona_natural_ss_id" => $persona["id"]
            ];
            $lugar_tipo = LugarSS::create($lugar_ss_arr);
        }
        $lugar_tipo->institucion()->save($lugar);
    }

    private function set_direcciones($arr, $persona) {
        $institucion = $arr["tipo_persona_natural"];
        $institucion_key = "persona_natural_".$institucion;
        $root = $arr[$institucion_key];

        foreach($root["direcciones"] as $d) {
            $this->add_direccion($d, $persona, $institucion);
        }    
        return true;
    }

    public function set_persona($arr) {
        // tipo funcionario
        $persona_tipo = PersonaNaturalPJ::create();

        if ($arr["tipo_persona_natural"] == "mp") {
            $arr_mp = $arr["persona_natural_mp"];
            $persona_tipo_arr = [
                "categorias" => $arr_mp["categorias"],
                "tipo_identificacion" => $arr_mp["tipo_identificacion"],
                "alias" => $arr_mp["alias"]
            ];
            $persona_tipo = PersonaNaturalMP::create($persona_tipo_arr);
            // manejar direcciones aquí   
            $this->set_direcciones($arr, $persona_tipo);    
        }
        if ($arr["tipo_persona_natural"] == "ss") {
            $arr_ss = $arr["persona_natural_ss"];
            $persona_tipo_arr = [
                "residente" => $arr_ss["residente"]
            ];
            $persona_tipo = PersonaNaturalSS::create($persona_tipo_arr);
            // manejar direcciones aquí
        }


        // persona 
        $pn = $arr["persona_natural"];
        $persona_arr = [
            "nombres" => $pn["nombres"],
            "primer_apellido" => $pn["primer_apellido"],
            "segundo_apellido" => $pn["segundo_apellido"],
            "genero" => $pn["genero"],
            "sexo" => $pn["sexo"],
            "nacionalidad" => $pn["nacionalidad"],
            "tipo_documento_identidad" => $pn["tipo_documento_identidad"],
            "numero_documento_identidad" => $pn["numero_documento_identidad"],            
            "fecha_nacimiento" => $pn["fecha_nacimiento"],
            "edad" => $pn["edad"],
            "estado_civil" => $pn["estado_civil"],
            "telefono_notificacion" => $pn["telefono_notificacion"],
            "correo_notificacion" => $pn["correo_notificacion"],
            "apartado_postal_notificacion" => $pn["apartado_postal_notificacion"]
          ];
        $persona_arr["ocupaciones"] = [];
        $persona_arr["profesiones"] = [];
        $persona_arr["etnias"] = [];
        $persona_arr["escolaridad"] = [];
        $persona_arr["discapacidades"] = [];
        $persona_arr["pueblo_indigena"] = [];
        $persona_arr["telefonos"] = [];
        $persona_arr["correos_electronicos"] = [];
        $persona_arr["apartados_postales"] = [];
        $persona_arr["profesiones"] = $pn["profesiones"];
        $persona_arr["ocupaciones"] = $pn["ocupaciones"];
        $persona_arr["etnias"] = $pn["etnias"];
        $persona_arr["escolaridad"] = $pn["escolaridad"];
        $persona_arr["discapacidades"] = $pn["discapacidades"];
        $persona_arr["pueblo_indigena"] = $pn["pueblo_indigena"];
        $persona_arr["telefonos"] = $pn["telefono_notificacion"];
        $persona_arr["correos_electronicos"] = $pn["correos_electronicos"];
        $persona_arr["apartados_postales"] = $pn["apartados_postales"];

        // $is_array = is_array($persona_arr["ocupaciones"] = []);
        // $this->log::alert('$persona_arr->ocupaciones');
        // $this->log::alert(json_encode($is_array));

        // \DB::enableQueryLog();
        $persona = new Persona;
        $persona_natural = new PersonaNatural($persona_arr);
        $persona_tipo->institucion()->save($persona_natural);

        // $laQuery = \DB::getQueryLog();
        // $this->log::alert($laQuery);
        // \DB::disableQueryLog();

        $persona_natural->persona()->save($persona);

        // set abogados
        // set menor

        return $persona_natural;
    }


    public function set_persona_simple($arr) {
        $pn = $arr["persona_natural"];        
        // tipo funcionario
        $persona_tipo = PersonaNaturalPJ::create();

        if ($arr["tipo_persona_natural"] == "mp") {
            $persona_tipo = PersonaNaturalMP::create(); 
        }
        if ($arr["tipo_persona_natural"] == "ss") {
            $persona_tipo = PersonaNaturalSS::create();
            // manejar direcciones aquí
        }

        $tipo_identidad = $arr["tipo_identidad"];

        if (preg_match('/anonimo/',$tipo_identidad)) {
            $identidad_tipo = Anonimo::create();
        }    

        if (preg_match('/protegido/',$tipo_identidad)) {
            $identidad_tipo = Protegido::create();
        }    

        if (preg_match('/no_portador/',$tipo_identidad)) {
            $identidad_tipo = NoPortador::create();
        }    

        if (preg_match('/desconocido/',$tipo_identidad)) {
            $identidad_tipo = Anonimo::create();
        }    

        if (preg_match('/de_oficio/',$tipo_identidad)) {
            $identidad_tipo = Anonimo::create();
        }    

        // persona 
        $persona_arr = [
            "nombres" => $pn["nombres"],
            "primer_apellido" => $pn["primer_apellido"],
            "segundo_apellido" => $pn["segundo_apellido"],
            "genero" => $pn["genero"],
            "sexo" => $pn["sexo"],
          ];


        // \DB::enableQueryLog();
        $persona = new Persona;
        $persona_natural = new PersonaNatural($persona_arr);
        $persona_tipo->institucion()->save($persona_natural);


        // $laQuery = \DB::getQueryLog();
        // $this->log::alert($laQuery);
        // \DB::disableQueryLog();

        $persona_natural->persona()->save($persona);

        // set tipo_identidad class
        if (isset($identidad_tipo)) {
            $identidad_tipo->identidad()->save($persona_natural);
        }

        return $persona_natural;
    }


}
