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
$router->post('/updateUser', 'User@updateUser');
$router->get('/user/info','User@getInfo');


// 忘記密碼處理
// 發送驗證碼
$router->post('/private/forgetPasswd', 'AuthController@sendResetCode');
// 確認驗證碼儲存新密碼
$router->post('/private/reset-password', 'AuthController@resetPassword');


// 權限判斷


// 影像辨識相關

$router->post('/picture/upload', [
    'as' => 'picture.upload',
    'uses' => 'PictureController@upload',
    'middleware' => 'check.permission:picture.upload',
]);

$router->get('/picture/user_all', [
    'as' => 'picture.user_all',
    'uses' => 'PictureController@user_all',
    'middleware' => 'check.permission:picture.user_all',
]);

$router->get('/picture/user_id/{id}/', [
    'as' => 'picture.user_id',
    'uses' => 'PictureController@user_id',
    'middleware' => 'check.permission:picture.user_id',
]);

$router->post('/picture/delete/', [
    'as' => 'picture.delete',
    'uses' => 'PictureController@destroy',
    'middleware' => 'check.permission:picture.delete',
]);

$router->get('/picture/manage/all/', [
    'as' => 'picture.manage.all',
    'uses' => 'PictureController@index',
    'middleware' => 'check.permission:picture.manage.all',
]);


// 照片路徑
// ? -> file name
// storage/user_pic/?
// storage/models_results/?