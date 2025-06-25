<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiayaOperasionalController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanKeuanganController;

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
    Route::get('/list', [ProductController::class, 'listProductByToko']);
    Route::get('/list/{id}', [ProductController::class, 'listProductByIdToko']);
    Route::get('/list-by-barcode/{barcode}', [ProductController::class, 'listProductByBarcode']);
    Route::get('/list-by-name', [ProductController::class, 'listProductByNama']);
    Route::get('/detail/{id}', [ProductController::class, 'detailProduct']);
    Route::post('/create', [ProductController::class, 'createNewProduct']);
    Route::post('/update-harga', [ProductController::class, 'updateHargaProduct']);
    Route::post('/delete', [ProductController::class, 'deleteProduct']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::post('/import', [ProductImportController::class, 'import']);
  });
});

Route::group([
  'prefix' => 'stock'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('/detail/{id}', [StockController::class, 'listCatatanStock']);
    Route::post('/tambah', [StockController::class, 'belanjaStock']);
    Route::get('/list/{id}', [StockController::class, 'listStockByIdToko']);
    Route::delete('/delete/{id}', [StockController::class, 'delete']);
  });
});

Route::group([
  'prefix' => 'dashboard'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::get('/year', [DashboardController::class, 'getYears']);
    Route::get('/laporan-per-toko', [DashboardController::class, 'laporanPerToko']);
    Route::post('/laporan-tahunan', [DashboardController::class, 'laporanSemuaToko']);
    Route::post('/laporan-tahunan', [LaporanKeuanganController::class, 'laporanSemuaToko']);
  });
});

Route::group([
  'prefix' => 'laporan'
], function () {
  Route::group([
    'middleware' => 'auth:api'
  ], function () {
    Route::post('/keuangan', [LaporanKeuanganController::class, 'laporanKeuanganByToko']);
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
    Route::get('list-by-manager/{id}', [TokoController::class, 'listByManager']);
    Route::get('keuntungan', [TokoController::class, 'keuntungan']);
    Route::get('jumlah-produk', [TokoController::class, 'jumlahProduk']);
    Route::get('transaksi-per-toko', [TokoController::class, 'transaksiPerToko']);
    Route::get('transaksi-by-toko/{id}', [TokoController::class, 'transaksiByToko']);
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
    Route::get('list-by-toko/{id}', [KasirController::class, 'listByToko']);
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
