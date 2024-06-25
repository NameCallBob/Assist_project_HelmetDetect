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
        return Picture::all();
    }

    public function store(Request $request)
    {
        return Picture::create($request->all());
    }

    public function show($id)
    {
        return Picture::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $picture = Picture::findOrFail($id);
        $picture->update($request->all());
        return $picture;
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $picture = Picture::findOrFail($id);
        $picture->delete();
        return 204;
    }

    /**
     * 給前端上傳照片後進行辨識
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            $file = $request->file('photo');
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
        return response()->json(['data' => Storage::files('user_pic')]);
    }


    /**
     * 使用者透過ID查詢單一照片
     */
    public function user_id($id)
    {

    }

    /**
     * 展示照片
     */
    public function showPic()
    {
        // 檢查檔案是否存在
        // 定義圖片存放的目錄
        $imageUrl = Storage::disk('public')->url("user_pic/123.jpg");
        return response()->json([
            'message' => $imageUrl
        ]);
    }


}
