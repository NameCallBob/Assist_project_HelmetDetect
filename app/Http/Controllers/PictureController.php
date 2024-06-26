<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Middleware\AuthMiddleware;
// put pic
use Illuminate\Support\Facades\Storage;
// models
use App\Models\Picture;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PictureController extends Controller
{
    public function index()
    {
        return Picture::with('results')->get();
    }

    public function destroy(Request $request)
    {
        $id = AuthMiddleware::getUserId($request);
        if (!$id){
            return response() -> json(['err' => 'user not found'] , 404);
        }

        $pic_id = $request->input('id');

        try{
            $picture = Picture::where('user_id',$id)
            ->where('id',$pic_id)
            ->get()
            ->first();
            $picture->delete();

            return response() -> json(['msg' => 'ok'] , 200);

        }catch(Exception $e){

            return response() -> json(['err' => 'pic not found'] , 404);

        }
    }

    /**
     * 給前端上傳照片後進行辨識
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            $file = $request->file('photo');
            if (!AuthMiddleware::getUserId($request)){
                return response() -> json(['msg' => 'user not found'],400);
             }
            $user_id = AuthMiddleware::getUserId($request);
            // 儲存於本地
            $filename = $this->__savePicToLocal($request);
            // 新增到資料庫
            Picture::savePic($user_id, $filename);

            // Assuming Flask API endpoint is running locally on port 5000
            $flaskApiUrl = 'http://127.0.0.1:5000/images';

            try {
                $response = Http::attach(
                    'image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($flaskApiUrl);
                // Optionally, you can handle the response from Flask API here
                $res = $response->json();
                if ($res['detection']) {
                    // 若未戴安全帽
                    Result::saveResult(
                        Picture::where("file_name", $filename)
                            ->where("user_id", $user_id)
                            ->get()
                            ->first()->id,
                        $res['detection'],
                        $res['filename']
                    );
                } else {
                    // 若有戴安全帽
                    Result::saveResult(
                        Picture::where("file_name", $filename)
                            ->where("user_id", $user_id)
                            ->get()
                            ->first()->id,
                        $res['detection'],
                        null
                    );
                }
                return response()->json(['message' => 'ok'], 200); // Return the response from Flask API
            } catch (Exception $e) {
                return response()->json(['message' => 'Error sending request to Flask API: ' . $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'File Not Found'], 400);
    }

    /**
     * 儲存使用者上傳照片
     * 將儲存於./storage/app/public/user_pic
     */
    private function __savePicToLocal($request)
    {
        $file = $request->file('photo');
        $filePath = $file->store('public/user_pic/');
        $filename = basename($filePath);
        return $filename;
    }

    /**
     * 查詢使用者所有上傳的照片
     */
    public function user_all(Request $request)
    {
        $id = AuthMiddleware::getUserId($request);
        try{
            return response() -> json(
                Picture::where('user_id',$id)
                ->with('results')
                ->get()
            );
        }catch(Exception $e){
            return response() -> json(['err' => 'not found'],404);
        }
    }


    /**
     * 使用者透過ID查詢單一照片
     */
    public function user_id(Request $request,$id)
    {
        $uid = AuthMiddleware::getUserId($request);
        try{
            return response() -> json(
                Picture::where('user_id',$uid)
                ->where('id',$id)
                ->with('results')
                ->get()
            );
        }catch(Exception $e){
            return response() -> json(['err' => 'not found'],404);
        }
    }

}
