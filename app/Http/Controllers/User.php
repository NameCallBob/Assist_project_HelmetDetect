<?php
namespace App\Http\Controllers;

use App\Models\User as UserModel;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Facades\Http; // 引入 Http Facade
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;


class User extends Controller{
    protected $usermodel;
    public function __construct(){
        $this->usermodel = new UserModel();
    }


    public function updateUser(Request $request)
    {
        $name = $request->input("name");
        $phone = $request->input("phone");
        $account = $request->input("account");
        $email = $request->input("email");

        // 打印 token_phone
        error_log("token_phone: " . $request->input('token_phone'));

        $updateSuccess = $this->usermodel->updateUser($name, $phone, $account, $email);

        if ($updateSuccess) {
            $response['status'] = 200;
            $response['message'] = '更新成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '更新失敗';
        }

        return response()->json($response);
    }



    public function getRoles(Request $request){
        $phone = $request->input('token_phone');
        $response['result'] = $this->usermodel->getRoles($phone);
        if(count($response['result'])!=0){
            $response['status'] = 200;
            $response['message'] = '查詢成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '無查詢結果';
        }
        return $response;
    }
 

    
    public function register(Request $request)
    {
        $name = $request->input("name");
        $phone = $request->input("phone");
        $account = $request->input("account");
        $email = $request->input("email");
        $password = $request->input("password");


        // 密碼加密
        $password = Hash::make($password);

        $registerSuccess = $this->usermodel->register($name, $phone, $account, $email, $password);

        if ($registerSuccess) {
            $response['status'] = 200;
            $response['message'] = '註冊成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '註冊失敗';
        }

        \Log::info('Register Response: ' . json_encode($response));
        
        return response()->json($response);
    }



    public function doLogin(Request $request) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

        $credentials = $request->only('user_phone', 'user_password');
        if (auth()->attempt($credentials)) {
            return response()->json(['token' => compact('token'), 'status' => 200, 'message' => '登入成功']);
        } else {
            return response()->json(['message' => '登入失敗', 'status' => 204]);
        }
    }



    public function readimg(Request $request)
    {
        $date = $request->input('date');  // 從請求中獲取日期參數
    
        try {
            // 調用模型方法並傳遞日期參數
            $result = $this->usermodel->readimg($date);
    
            if (!empty($result)) {
                $imagePaths = [];
    
                foreach ($result as $imageData) {
                    // 定義保存圖片的路徑和文件名
                    $filePath = storage_path('app/public/images');
                    $fileName = 'image_' . time() . '.png';
    
                    // 確保目錄存在
                    if (!File::exists($filePath)) {
                        File::makeDirectory($filePath, 0755, true);
                    }
    
                    // 保存圖片
                    File::put($filePath . '/' . $fileName, $imageData);
    
                    // 添加圖片的 URL 路徑到結果中
                    $imagePaths[] = url('storage/images/' . $fileName);
                }
    
                return response()->json(['status' => 200, 'message' => '查詢成功', 'images' => $imagePaths]);
            } else {
                return response()->json(['status' => 204, 'message' => '無查詢結果']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => '內部服務器錯誤: ' . $e->getMessage()]);
        }
    }



    public function uploadImage(Request $request)
    {
        $file = $request->file('image');

        if (!$file) {
            return response()->json(['message' => 'No image file sent.'], 400);
        }

        // Assuming Flask API endpoint is running locally on port 5000
        $flaskApiUrl = 'http://localhost:5000/images';

        try {
            $response = Http::attach(
                'image',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($flaskApiUrl);

            // Optionally, you can handle the response from Flask API here
            return $response->json(); // Return the response from Flask API
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error sending request to Flask API: ' . $e->getMessage()], 500);
        }
    }
    
}



