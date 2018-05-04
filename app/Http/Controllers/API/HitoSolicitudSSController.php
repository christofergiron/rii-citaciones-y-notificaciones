<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\HitoSolicitudSS;
use App\HitoSolicitudSSTools;
use App\Http\Requests\StoreHitoSolicitudSS;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class HitoSolicitudSSController extends Controller
{

    public  $successStatus = 200;
    private $root;
    private $hito_solicitud_ss_tools;
    private $StoreHitoSolicitudSS;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->hito_solicitud_ss_tools = new HitoSolicitudSSTools;
        $this->root = false;
        $this->StoreHitoSolicitudSS = new StoreHitoSolicitudSS;
    }

    public function index(Request $request){
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

       #get denuncia_mp
       $res = $this->hito_solicitud_ss_tools->ss_list_hito($arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
       }

       # return success response
       return response()->json(['procesoTable'=>$res], $this->successStatus);
    }

    public function show(Request $request, $id) {
      # parsing
      // $parsed_request = $this->tools->parse_request($request);
      // $arr = $parsed_request[1];
      $arr = $request->all();

      $header = $request->header('Authorization');
      if ($header=='') {
          $header =  $request->all()['token'];
      }
      $header = str_replace('Bearer ', '', $header);
      $arr['token'] = $header;

      $this->log::alert($arr);

       #get denuncia_mp
       $res = $this->hito_solicitud_ss_tools->get_hito($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due denuncia mp is invalid!'], 403);
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

      $this->logger->alert('inside Store Hito Solicitud');
      $this->log::alert(json_encode($arr));

      $res = $this->StoreHitoSolicitudSS->rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      $res = $this->StoreHitoSolicitudSS->persist($arr);

      if (!$res->success) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => $res->success, 'message'=>$res->message, 'payload' => $res->payload], $this->successStatus);
    }

    public function workflow(Request $request) {
      $arr = $request->all();
      $this->logger->alert('inside workflow service DenunciaMP');
      $this->logger->alert(json_encode($arr)) ;

      $res = $this->$StoreHitoSolicitudSS->workflow_rules($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

      //$res = $this->StoreDenunciaMP->asignar_delitos($arr);
      $res = $this->$StoreHitoSolicitudSS->apply_transition($arr);

      if ($res->code != 200) {
        return response()->json(['error'=>$res->message], 403);
      }

       # return success response
       return response()->json(['success' => true, 'message'=>$res], $this->successStatus);
    }

}
