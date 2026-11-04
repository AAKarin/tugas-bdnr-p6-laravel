<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\FirebaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Tes koneksi API
Route::get('/test', function() {
    return response()->json(['status' => 'OK', 'pesan' => 'API Toko Berjalan Normal']);
});

// Route bawaan sistem Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==========================================
// Latihan 1: REST API Produk (MongoDB + Laravel)
// ==========================================
Route::apiResource('produk', ProdukController::class);

// ==========================================
// Latihan 3: REST API Produk (Firestore + Laravel)
// ==========================================
Route::prefix('firestore')->group(function () {
    Route::get('/produk', [FirebaseController::class, 'index']);
    Route::get('/produk/{id}', [FirebaseController::class, 'show']);
    Route::post('/produk', [FirebaseController::class, 'store']);
    Route::put('/produk/{id}', [FirebaseController::class, 'update']);
    Route::delete('/produk/{id}', [FirebaseController::class, 'destroy']);
});
