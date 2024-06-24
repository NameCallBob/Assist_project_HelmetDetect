<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// 紀錄
use Log;


// JWT
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

use Exception;

// inner
use App\Models\User;

class AuthMiddleware {
    public function handle(Request $request, Closure $next ,$permission=null) {
        if ($permission == null){
            return $next($request);
        }
        try {
            if(!self::getUserId($request)){
                return response()->json([
                    "err" => "token is valid"
                ],401);
            }
            if ($this -> checkAction($request,$permission)) {
                $user = JWTAuth::parseToken()->authenticate();
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid'], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], 401);
            } else {
                return response()->json(['status' => 'Authorization Token not found'], 401);
            }
        }
        return $next($request);

    }
    /**
     * 確認權限
     * @param $id 使用者ID
     * @param $path 權限動作
     */
    public function checkAction($id,$path){
        return true;
    }

    public static function getUserId($request){
        try {
            $token = new Token($request->bearerToken());
            $payload = JWTAuth::decode($token);
            // Validate payload data
            $id = isset($payload['id']) ? $payload['id'] : null;

            if ($id ) {
                $privateModel = User::where('id', $id)
                    ->first();
                if ($privateModel) {
                    return $id;
                }
            }
            return false;
        } catch (JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * 確認JWTtoken是否有效
     * return 使用者id
     */
    public static function verifyToken(Request $request)
    {
        try {
            $token = new Token($request->bearerToken());
            $payload = JWTAuth::decode($token);

            // Validate payload data
            $id = isset($payload['id']) ? $payload['id'] : null;

            if ($id){
                $privateModel = User::where('id', $id)
                ->first();

                if ($privateModel) {
                    return $id;
                }
            }

            return false;
        } catch (JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage());
            return false;
        }
    }
}