<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

/** @var \Laravel\Lumen\Routing\Router $router */

$router -> get('/',function(){
    return "hello";
});




// $router->group(['middleware' => ['auth', 'checkPrivilege']], function () use ($router) {

// JWTtoken驗證 -> 取得ID
$router->get("/api/token/verify/",'AuthController@checkToken');


// 使用者相關

// 登入
$router->post('/login', 'AuthController@login');
// 註冊
$router->post('/register', 'User@register');

// 使用者個資變更
$router->put('/updateUser', 'User@updateUser');

// 忘記密碼處理
// 發送驗證碼
$router->post('/private/forgetPasswd', 'AuthController@sendResetCode');
// 確認驗證碼儲存新密碼
$router->post('/private/reset-password', 'AuthController@resetPassword');



// 影像辨識相關

// 上傳照片影像辨識
$router->post('/picture/upload','PictureController@upload');

// 使用者查看上傳的所有照片
$router->get('/picture/user_all','PictureController@user_all');
// 使用者查看單一照片(id)
$router->get('/picture/user_id/{id}/','PictureController@user_id');


// 照片路徑
$router->get('/picture/show/', 'PictureController@showPic');
