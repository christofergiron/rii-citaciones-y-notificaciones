<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\cmbdependenciassTools;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;


class cmbdependenciassController extends Controller
{
    public $successStatus = 200;
    private $root;
    private $dependencia_tools;
    private $StoreAlertaAmber;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->dependencia_tools = new cmbdependenciassTools;
        $this->root = false;
    }

    public function index(Request $request)
    {
    
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
      // $id = $request->params->id;

        $res = $this->dependencia_tools->dependencias_ss($arr["token"]);

        # chek for nulls
        if (is_null($res)) {
            return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
        }

        # return success response
        return response()->json(['dependencias'=>$res], $this->successStatus);
    }
}
