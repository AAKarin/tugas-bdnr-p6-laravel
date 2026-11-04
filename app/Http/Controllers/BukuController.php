<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    // GET /api/buku (CRUD Read All + Fitur Filter Kategori / Pengarang)
    public function index(Request $request)
    {
        $query = Buku::query();

        // Fitur Filter: filter berdasarkan kategori jika ada di query param
        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }

        // Fitur Filter tambahan: filter berdasarkan pengarang jika ada
        if ($request->has('pengarang') && !empty($request->pengarang)) {
            $query->where('pengarang', 'like', '%' . $request->pengarang . '%');
        }

        return response()->json($query->get());
    }

    // GET /api/buku/{id} (CRUD Read Detail)
    public function show($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }
        return response()->json($buku);
    }

    // POST /api/buku (CRUD Create)
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'pengarang' => 'required|string',
            'kategori' => 'required|string',
            'tahun' => 'required|integer',
            'stok' => 'required|integer|min:0',
            'peminjam' => 'nullable|array',
            'ulasan' => 'nullable|array'
        ]);

        $buku = Buku::create($data);
        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ], 201);
    }

    // PUT /api/buku/{id} (CRUD Update)
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $buku->update($request->all());
        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $buku
        ]);
    }

    // DELETE /api/buku/{id} (CRUD Delete)
    public function destroy($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $buku->delete();
        return response()->json(['message' => 'Buku berhasil dihapus']);
    }

    // POST /api/buku/{id}/peminjam (Fitur $push: menambah peminjam ke array)
    public function tambahPeminjam(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $dataPeminjam = $request->validate([
            'nama' => 'required|string',
            'tanggal_pinjam' => 'nullable|string',
            'durasi_hari' => 'nullable|integer'
        ]);

        if (empty($dataPeminjam['tanggal_pinjam'])) {
            $dataPeminjam['tanggal_pinjam'] = date('Y-m-d H:i:s');
        }

        // Operasi MongoDB $push ke atribut array 'peminjam'
        $buku->push('peminjam', $dataPeminjam);

        return response()->json([
            'message' => 'Data peminjam berhasil ditambahkan via $push',
            'data_peminjam' => $dataPeminjam,
            'buku' => $buku->fresh()
        ]);
    }

    // POST /api/buku/{id}/ulasan (Fitur $push alternatif: menambah ulasan ke array)
    public function tambahUlasan(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $dataUlasan = $request->validate([
            'pembaca' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string'
        ]);

        $dataUlasan['tanggal'] = date('Y-m-d H:i:s');

        // Operasi MongoDB $push ke atribut array 'ulasan'
        $buku->push('ulasan', $dataUlasan);

        return response()->json([
            'message' => 'Ulasan berhasil ditambahkan via $push',
            'data_ulasan' => $dataUlasan,
            'buku' => $buku->fresh()
        ]);
    }

    // GET /api/buku/laporan/kategori (Fitur Agregasi $group)
    public function laporanKategori()
    {
        // Menjalankan agregasi MongoDB dengan tahap $group dan $sort
        $laporan = Buku::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$kategori',
                        'totalJudul' => ['$sum' => 1],
                        'totalStok' => ['$sum' => '$stok'],
                        'rataRataTahun' => ['$avg' => '$tahun']
                    ]
                ],
                [
                    '$sort' => ['totalStok' => -1]
                ]
            ]);
        });

        // Mapping cursor menjadi array data rapi
        $hasil = [];
        foreach ($laporan as $item) {
            $hasil[] = [
                'kategori' => $item['_id'] ?? 'Tanpa Kategori',
                'totalJudul' => $item['totalJudul'],
                'totalStok' => $item['totalStok'],
                'rataRataTahun' => round($item['rataRataTahun'], 1)
            ];
        }

        return response()->json([
            'judul_laporan' => 'Laporan Perpustakaan Buku Berdasarkan Kategori',
            'waktu_generate' => date('Y-m-d H:i:s'),
            'data' => $hasil
        ]);
    }
}
