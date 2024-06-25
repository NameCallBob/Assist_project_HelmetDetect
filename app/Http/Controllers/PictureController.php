<?php

namespace App\Http\Controllers;
// put pic
use Illuminate\Support\Facades\Storage;
// models
use App\Models\Picture;
use Illuminate\Http\Request;

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
        $id = $request -> input('id');
        $picture = Picture::findOrFail($id);
        $picture->delete();
        return 204;
    }

    /**
     * 給前端上傳照片後進行辨識
     */
    public function upload(Request $request){
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // 儲存於本地
            $this -> __savePicToLocal($request);

            return response()->json(['path' => 'ok'], 200);
        }

        return response()->json(['error' => 'Invalid file upload'], 400);
    }

    /**
     * 儲存使用者上傳照片
     * 將儲存於./storage/app/public/user_pic
     */
    private function __savePicToLocal($request){
        $file = $request->file('photo');
        $filePath = $file->store('public/user_pic/');
    }

    /**
     * 查詢使用者所有上傳的照片
     */
    public function user_all(Request $request){
        return response() -> json(['data' =>Storage::files('user_pic')]);
    }


    /**
     * 使用者透過ID查詢單一照片
     */
    public function user_id($id){

    }

    /**
     * 展示照片
     */
    public function showPic(){
        // 檢查檔案是否存在
         // 定義圖片存放的目錄
        $imageUrl = Storage::disk('public')->url("user_pic/123.jpg");
        return response() -> json([
            'message' => $imageUrl
        ]);
    }


}
