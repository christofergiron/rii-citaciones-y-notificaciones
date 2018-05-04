<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as tokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException as tokenInvalidException;

class AuthenticationCheck
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
        $this->log = new  \Log;
        try{
            $token = JWTAuth::getToken();
            if ($token == false){
                throw new tokenInvalidException();
            }
            $user = JWTAuth::decode($token);
            $objectUser = json_decode($user,true);
            $activeLogin = \App\ActiveLogins::where([
                ["email","=",$objectUser["email"]]
            ])->first();

            if ($activeLogin == null || $activeLogin->token != $token){
                throw new tokenInvalidException();
            }
            
        }catch(\Exception $e){
            if ($e instanceof tokenExpiredException) {
                $retVal = new \stdClass;
                $retVal->tokenExpired = true;
                return response()->json($retVal, '403');
            } else if ($e instanceof tokenInvalidException) {
                $retVal = new \stdClass;
                $retVal->tokenInvalid = true;
                return response()->json($retVal, '403');
            }else{
                throw $e;
            }
        }
        return $next($request);
    }
}
