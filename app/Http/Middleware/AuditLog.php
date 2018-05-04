<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuditLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $auditLog = new \App\AuditLog;
        $this->log = new  \Log;

        $auditLog->METHOD = $request->method();
        $auditLog->FULL_URL = $request->fullUrl();
        $auditLog->PATH = $request->path();
        $auditLog->IP = $request->ip();
        $jsonCoded = json_decode($request->getContent(), true);
        $token = JWTAuth::getToken();
        if ($token != false){
            try{
                $user = json_decode(JWTAuth::decode($token));
                $auditLog->USER_NAME = $user->name;
                $auditLog->USER_ID = $user->id;
            }catch(\Exception $e){
            
            }
        }
        
        if ($jsonCoded != null){
            if (array_key_exists("password",$jsonCoded)){
                $jsonCoded["password"] = \Hash::make($jsonCoded["password"] );
            }
            $auditLog->CONTENT = json_encode($jsonCoded);
        }
        $auditLog->TIMESTAMP = new \DateTime();
        if ($request->method() == 'GET' && $request->path() == '/') {
            $auditLog->save();
        } elseif ($request->method() == 'POST') {
            $auditLog->save();
        }
        
        return $next($request);
    }
}
