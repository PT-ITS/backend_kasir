<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

Route::group([
    'prefix' => 'auth'
  ], function () {
    Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::group([
      'middleware' => 'auth:api'
    ], function(){
      Route::post('logout', [AuthController::class,'logout']);
      Route::post('refresh', [AuthController::class, 'refresh']);
      Route::get('me', [AuthController::class,'me']);
      Route::get('list-user', [AuthController::class,'listUser']);
      Route::post('update-users/{id}', [AuthController::class,'resetPassword']);
      Route::post('update-pw/{id}', [AuthController::class,'updatePw']);
      Route::delete('delete-user/{id}', [AuthController::class,'deleteUser']);
      Route::post('delete-users', [AuthController::class,'deleteUsers']);
      
      Route::post('ganti-password', [AuthController::class,'ubahPassword']);

      Route::get('active-token/{id}', [AuthController::class,'getActiveToken']);
      
      
    });
  });
  

  Route::group([
    'prefix' => 'product'
], function () {
    // Route::group([
    //     'middleware' => ['auth:api', 'signature']
    // ], function () {
    Route::get('/list', [ProductController::class, 'listProduct']);
    Route::get('/detail/{id}', [ProductController::class, 'detailProduct']);
    Route::post('/create', [ProductController::class, 'createProduct']);
    Route::post('/buy/{id}', [ProductController::class, 'buyProduct']);
    Route::post('/sell/{id}', [ProductController::class, 'sellProduct']);
    // });
});
  
  



