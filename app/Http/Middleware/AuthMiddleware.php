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

// Role permissions
use App\Models\UserRole;
use App\Models\Role;
use App\Models\Action;

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
                return $next($request);
            }
            return response() -> json(['msg' => 'forbidden'],403);
        } catch (Exception $e) {
            // echo $e;
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid'], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], 401);
            } else {
                return response()->json(['status' => 'Authorization Token not found'], 401);
            }
        }


    }
    /**
     * 確認權限
     * @param $id 使用者ID
     * @param $path 權限動作
     */
    public function checkAction($request,$permissionName){
            $privateId = self::verifyToken($request);
            if ($privateId){
                $userRole = UserRole::where('user_id', $privateId)->first();

                if (!$userRole) {
                    return false; // 如果找不到使用者角色，則返回false
                }

                // 找出角色對應的所有權限ID
                $role = Role::find($userRole->role_id);

                if (!$role) {
                    return false; // 如果找不到角色，則返回false
                }

                $permissions = $role->actions()->pluck('action_id')->toArray();

                // 找出權限的ID
                $permission = Action::where('name', $permissionName)->first();

                if (!$permission) {
                    return false; // 如果找不到指定的權限，則返回false
                }

                // 檢查權限是否在角色擁有的權限中
                return in_array($permission->id, $permissions);
            }
            return response() -> json(['err' => 'token is invalid'],401);
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