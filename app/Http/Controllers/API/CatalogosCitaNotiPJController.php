<?php

namespace App\Http\Controllers\API;

use App\Tools;
use App\CatalogoCitaNotiTools;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;


class CatalogosCitaNotiPJController extends Controller
{
    public $successStatus = 200;
    private $root;
    private $citacion_tool;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->root = false;
        $this->citacion_tool = new CatalogoCitaNotiTools;
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
       #get orden captura
       $res = $this->citacion_tool->pj_catalogo_citaciones($id, $arr["token"]);

       # chek for nulls
       if (is_null($res)) {
         return response()->json(['error'=>'No Content due Citacion is invalid!'], 403);
       }

       # return success response
       return response()->json(['success' => true, 'message'=>json_decode($res)], $this->successStatus);
    }
}
