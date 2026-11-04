<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // GET /api/produk (Menampilkan semua data + filter kategori)
    public function index(Request $request)
    {
        $query = Produk::query();
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        return response()->json($query->get());
    }

    // POST /api/produk (Tambah data dengan validasi sesuai slide)
    public function store(Request $r)
    {
        $data = $r->validate([
            'nama' => 'required|string',
            'harga' => 'required|integer|min:0',
            'kategori' => 'nullable|string',
            'stok' => 'nullable|integer'
        ]);
        
        return response()->json(Produk::create($data), 201);
    }

    // GET /api/produk/{id}
    public function show($id)
    {
        $produk = Produk::find($id);
        return $produk ? response()->json($produk) : response()->json(['message' => 'Not found'], 404);
    }

    // PUT /api/produk/{id}
    public function update(Request $r, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Not found'], 404);

        $produk->update($r->all());
        return response()->json($produk);
    }

    // DELETE /api/produk/{id}
    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['message' => 'Not found'], 404);

        $produk->delete();
        return response()->json(['message' => 'Deleted']);
    }
}