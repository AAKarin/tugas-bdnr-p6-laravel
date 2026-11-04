<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\BukuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Tes koneksi API
Route::get('/test', function() {
    return response()->json(['status' => 'OK', 'pesan' => 'API Toko & Perpustakaan Berjalan Normal']);
});

// Route bawaan sistem Laravel
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==========================================
// LATIHAN 1: REST API Produk (MongoDB + Laravel)
// ==========================================
Route::apiResource('produk', ProdukController::class);

// ==========================================
// LATIHAN 3: REST API Produk (Firestore + Laravel)
// ==========================================
Route::prefix('firestore')->group(function () {
    Route::get('/produk', [FirebaseController::class, 'index']);
    Route::get('/produk/{id}', [FirebaseController::class, 'show']);
    Route::post('/produk', [FirebaseController::class, 'store']);
    Route::put('/produk/{id}', [FirebaseController::class, 'update']);
    Route::delete('/produk/{id}', [FirebaseController::class, 'destroy']);
});

// ==============================================================================
// TUGAS 1 (Slide 29): API Perpustakaan Buku (MongoDB + Laravel)
// Fitur: CRUD, Filter, $push, dan Agregasi $group
// ==============================================================================
Route::prefix('buku')->group(function () {
    // Agregasi $group: Laporan statistik per kategori (ditempatkan sebelum {id})
    Route::get('/laporan/kategori', [BukuController::class, 'laporanKategori']);

    // CRUD: Read All (Mendukung filter query ?kategori=... atau ?pengarang=...)
    Route::get('/', [BukuController::class, 'index']);

    // CRUD: Read Detail
    Route::get('/{id}', [BukuController::class, 'show']);

    // CRUD: Create
    Route::post('/', [BukuController::class, 'store']);

    // CRUD: Update
    Route::put('/{id}', [BukuController::class, 'update']);

    // CRUD: Delete
    Route::delete('/{id}', [BukuController::class, 'destroy']);

    // Fitur $push: Tambah riwayat peminjam ke array
    Route::post('/{id}/peminjam', [BukuController::class, 'tambahPeminjam']);

    // Fitur $push: Tambah ulasan pembaca ke array
    Route::post('/{id}/ulasan', [BukuController::class, 'tambahUlasan']);
});
