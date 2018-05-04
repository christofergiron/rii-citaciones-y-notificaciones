<?php

namespace App\Http\Controllers\API;

use App\Tools;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\AuditLog;

class BitacoraController extends Controller
{
    public $successStatus = 200;
    private $root;
    private $persona_ss_tools;

    public function __construct(LoggerInterface $logger)
    {
        $this->log = new \Log;
        $this->logger = $logger;
        $this->tools = new Tools($this->logger);
        $this->root = false;
    }

    public function index(Request $request)
    {
        $arr = $request->all();
        $header = $request->header('Authorization');
        if ($header=='') {
            $header =  $request->all()['token'];
        }
        $header = str_replace('Bearer ', '', $header);
        $arr['token'] = $header;
    
        #get denuncia_mp
        $query = DB::table('audit_log')->select()->orderBy('id', 'desc');
        if (array_key_exists("user",$arr)){
            $query -> where('USER_NAME', 'like', '%'.$arr["user"].'%');
        }
        if (array_key_exists("path",$arr)){
            $query -> where('PATH', 'like', '%'.$arr["path"].'%');
        }
        if (array_key_exists("ip",$arr)){
            $query -> where('IP', 'like', '%'.$arr["ip"].'%');
        }
        $res = $query->get();
        # chek for nulls
        if (is_null($res)) {
            return response()->json(['error'=>'No Content due to null or empty parameters'], 403);
        }

        # return success response
        $retVal = new \stdClass;
        $retVal->bitacora = $res;
        return response()->json($retVal, $this->successStatus);
    }
}
