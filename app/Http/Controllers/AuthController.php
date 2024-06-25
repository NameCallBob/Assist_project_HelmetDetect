<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AuthMiddleware;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\MailController;


class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->only("account", "password");
        // search user exist
        $ob = User::where('account', $request->input("account"))
            ->get()
            ->first();
        if ($ob) {
            if ($token = auth()->attempt($credentials)) {
                return response()->json(compact("token"));
            } else {
                return response()->json(['message' => '登入失敗', 'status' => 204]);
            }
        }
        return response()->json(['msg' => 'not found'], 404);
    }

    public function checktoken(Request $request){
        $res = AuthMiddleware::verifyToken($request);
        if ($res != false){
            return response() -> json(['message' => 'ok']);
        }
        return response() -> json(['err' => 'token invalid'],401);
    }






    // forget password
    public function sendResetCode(Request $request)
    {
        $phone = $request->input('phone');
        $user = User::where('phone', $phone)->get()->first();


        if (!$user) {
            return response()->json(['message' => '找不到該用戶', 'status' => 404]);
        }

        $email = $user -> email;
        $code = Str::random(6);
        $user->reset_code = $code;
        $user->reset_expires_at = Carbon::now()->addMinutes(6); // 6分鐘後過期
        $user->save();

        if (MailController::sendMail($email, $code)) {
            return response()->json(['message' => '驗證碼已發送到您的郵箱', 'status' => 200]);
        }

        return response()->json(['message' => '郵件不存在', 'status' => 400]);
    }


    // 以token取得user , 將資料表的password改為新密碼, 並清空reset_code
    public function resetPassword(Request $request)
    {
        $password = $request->input('password');
        $reset_code = $request->input('reset_code');

        $user = User::where('reset_code', $reset_code)->get()->first();

        if (!$user) {
            return response()->json(['message' => '找不到該用戶', 'status' => 404]);
        }

        if ($user->reset_code != $reset_code) {
            return response()->json(['message' => '驗證碼錯誤', 'status' => 400]);
        }

        if (Carbon::now()->gt($user->reset_expires_at)) {
            return response()->json(['message' => '驗證碼已過期', 'status' => 400]);
        }

        $user->password = Hash::make($password);
        $user->reset_code = null;
        $user->reset_expires_at = null;
        $user->save();

        return response()->json(['message' => '密碼重置成功', 'status' => 200]);
    }


}
