<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\BusquedaOrdenTools;
use App\Fiscal;
use App\FuncionarioSS;
use App\DenunciaSS;
use App\PersonaNatural;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class BusquedaOrdenCapturaController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $busqueda;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->busqueda = new BusquedaOrdenTools;
        $this->root = false;
    }

    public function solicitud_orden(Request $request){
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      // $id = $request->params->id

       $validator = Validator::make($arr   , [
         "token" => "required",
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       #get orden captura
       $res = $this->busqueda->busqueda_orden($arr);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['documentsTable'=>$res], $this->successStatus);
    }

    public function solicitud_contra_orden(Request $request){
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      // $id = $request->params->id

       $validator = Validator::make($arr   , [
         "token" => "required",
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       #get orden captura
       $res = $this->solicitudes->list_solicitud_contra_orden($arr);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['documentsTable'=>$res], $this->successStatus);
    }
}
