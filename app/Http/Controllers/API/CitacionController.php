<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\OrdenCaptura;
//use App\ordejudicial;
//use App\requerimiento fiscal;
use App\CitacionTools;
use App\Fiscal;
use App\FuncionarioSS;
use App\DenunciaSS;
use App\PersonaNatural;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests\StoreCitacion;

class CitacionController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $citacion_tool;
    private $StoreCitacion;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->citacion_tool = new CitacionTools;
        $this->root = false;
        $this->StoreCitacion = new StoreCitacion;
    }

    public function index(Request $request){
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();
      // $id = $request->params->id

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

       #get orden captura
       $res = $this->citacion_tool->pj_list_citaciones($arr["token"]);

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

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

      $this->log::alert($arr);

       #get orden captura
       $res = $this->citacion_tool->pj_citaciones($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due Citacion is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }

    public function store(Request $request) {
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

      $this->logger->alert('inside Store Citacion');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreCitacion->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreCitacion->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }
       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }

    public function workflow(Request $request) {
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;
      
      $this->logger->alert('inside workflow service Citacion');
      $this->logger->alert(json_encode($arr)) ;
      $res = $this->StoreCitacion->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreCitacion->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
