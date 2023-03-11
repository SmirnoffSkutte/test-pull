<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;

class authedUser
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $jwt=new JwtService();
            $token=$request->bearerToken();
            if ($token===null or trim($token)==='') {
                throw new \Exception('Нет токена',401);
            }
            $isValid=$jwt->verifyToken($token);
            if($isValid===false){
                throw new \Exception('Токен невалиден',401);
            }
            if($isValid instanceof \Illuminate\Http\JsonResponse){
                return $isValid;
            }
            return $next($request);
        }
        catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ],$exception->getCode());
        }
    }
}
