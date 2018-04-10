<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\OrdenCaptura;
//use App\ordejudicial;
//use App\requerimiento fiscal;
use App\BuscarOrdenCapturaTools;
use App\Fiscal;
use App\FuncionarioSS;
use App\DenunciaSS;
use App\PersonaNatural;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreOrdenCaptura;

class BuscarOrdenCapturaMenorController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $orden_captura_tools;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->orden_captura_tools = new BuscarOrdenCapturaTools;
        $this->root = false;
    }

    public function show_menor(Request $request, $id) {
      # parsing
      $arr = $request->all();
      $this->log::alert($arr);


       $validator = Validator::make($arr, [
         "token" => "required"
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }
       #get orden captura
       $res = $this->orden_captura_tools->buscar_orden_captura_menor($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due Orden Captura is invalid!'], 403);
       }

       # return success response
       //return $res;
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
