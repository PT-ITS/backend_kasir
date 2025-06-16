<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiayaOperasionalController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\TransaksiController;

Route::group([
  'prefix' => 'auth'
], function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('list-user', [AuthController::class, 'listUser']);
    Route::post('update-users/{id}', [AuthController::class, 'resetPassword']);
    Route::post('update-pw/{id}', [AuthController::class, 'updatePw']);
    Route::delete('delete-user/{id}', [AuthController::class, 'deleteUser']);
    Route::post('delete-users', [AuthController::class, 'deleteUsers']);

    Route::post('ganti-password', [AuthController::class, 'ubahPassword']);

    Route::get('active-token/{id}', [AuthController::class, 'getActiveToken']);
  });
});


Route::group([
  'prefix' => 'product'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('/list', [ProductController::class, 'listProduct']);
    Route::get('/detail/{id}', [ProductController::class, 'detailProduct']);
    Route::post('/create', [ProductController::class, 'createProduct']);
    Route::post('/buy/{id}', [ProductController::class, 'buyProduct']);
    Route::post('/sell/{id}', [ProductController::class, 'sellProduct']);
  });
});

Route::group([
  'prefix' => 'manager'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('list', [ManagerController::class, 'list']);
    Route::get('detail/{id}', [ManagerController::class, 'detail']);
    Route::post('create', [ManagerController::class, 'create']);
    Route::post('update/{id}', [ManagerController::class, 'update']);
    Route::delete('delete/{id}', [ManagerController::class, 'delete']);
  });
});

Route::group([
  'prefix' => 'toko'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('list', [TokoController::class, 'list']);
    Route::get('keuntungan', [TokoController::class, 'keuntungan']);
    Route::get('jumlah-produk', [TokoController::class, 'jumlahProduk']);
    Route::get('jumlah-terjual', [TokoController::class, 'jumlahTerjual']);
    Route::get('detail/{id}', [TokoController::class, 'detail']);
    Route::post('create', [TokoController::class, 'create']);
    Route::post('update/{id}', [TokoController::class, 'update']);
    Route::delete('delete/{id}', [TokoController::class, 'delete']);
  });
});

Route::group([
  'prefix' => 'kasir'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('list', [KasirController::class, 'list']);
    Route::get('detail/{id}', [KasirController::class, 'detail']);
    Route::post('create', [KasirController::class, 'create']);
    Route::post('update/{id}', [KasirController::class, 'update']);
    Route::delete('delete/{id}', [KasirController::class, 'delete']);
  });
});

Route::group([
  'prefix' => 'biaya-operasional'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('list', [BiayaOperasionalController::class, 'list']);
    Route::get('detail/{id}', [BiayaOperasionalController::class, 'detail']);
    Route::post('create', [BiayaOperasionalController::class, 'create']);
    Route::post('update/{id}', [BiayaOperasionalController::class, 'update']);
    Route::delete('delete/{id}', [BiayaOperasionalController::class, 'delete']);
  });
});

Route::group([
  'prefix' => 'transaksi'
], function () {
  // Route::group([
  //     'middleware' => 'auth:api'
  // ], function () {
  Route::get('/list', [TransaksiController::class, 'listTransaksiByToko']);
  Route::get('/detail/{id}', [TransaksiController::class, 'detailTransaksi']);
  Route::post('/create', [TransaksiController::class, 'createTransaksi']);
  // });
});
