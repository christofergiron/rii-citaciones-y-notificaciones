<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\Captura;
//use App\ordejudicial;
//use App\requerimiento fiscal;
use App\RealizarCapturaTools;
use App\Fiscal;
use App\FuncionarioSS;
use App\DenunciaSS;
use App\PersonaNatural;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreRealizarCaptura;

class RealizarCapturaController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $captura_tools;
    private $StoreRealizarCaptura;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->captura_tools = new RealizarCapturaTools;
        $this->root = false;
        $this->StoreRealizarCaptura = new StoreRealizarCaptura;
    }

    public function index(Request $request){
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

       #get captura
       $res = $this->captura_tools->ss_list_captura($arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['documentsTable'=>$res], $this->successStatus);
    }

    public function show(Request $request, $id) {
      # parsing
      $arr = $request->all();
      $this->log::alert($arr);


       $validator = Validator::make($arr, [
         "token" => "required"
       ]);

       if ($validator->fails()) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }
        //echo $id;
       #get captura
       $res = $this->captura_tools->ss_captura($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due captura is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }

    public function store(Request $request) { //StoreCaptura $request
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      $this->logger->alert('inside Store RealizarCaptura');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreRealizarCaptura->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreRealizarCaptura->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }
       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }

    public function workflow(Request $request) {
      $arr = $request->all();
      $this->logger->alert('inside workflow service RealizarCaptura');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreRealizarCaptura->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreRealizarCaptura->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
