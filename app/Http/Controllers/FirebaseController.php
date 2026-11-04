<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;

class FirebaseController extends Controller
{
    protected $db;
    protected $koleksi = 'produk';

    // Menggunakan Dependency Injection resmi Laravel
    public function __construct(Firestore $firestore)
    {
        $this->db = $firestore->database();
    }

    // GET /api/firestore/produk (Tampilkan semua dokumen)
    public function index()
    {
        $dokumen = $this->db->collection($this->koleksi)->documents();
        $hasil = [];
        foreach ($dokumen as $doc) {
            if ($doc->exists()) {
                $hasil[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }
        return response()->json($hasil);
    }

    // GET /api/firestore/produk/{id} (Detail satu dokumen)
    public function show($id)
    {
        $doc = $this->db->collection($this->koleksi)->document($id)->snapshot();
        if (!$doc->exists()) {
            return response()->json(['pesan' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(array_merge(['id' => $doc->id()], $doc->data()));
    }

    // POST /api/firestore/produk (Tambah dokumen ID otomatis)
    public function store(Request $request)
    {
        $data = $request->all();
        $data['dibuat'] = date('c');
        $referensi = $this->db->collection($this->koleksi)->add($data);
        
        return response()->json([
            'pesan' => 'Data berhasil ditambahkan',
            'id' => $referensi->id()
        ], 201);
    }

    // PUT /api/firestore/produk/{id} (Update sebagian dengan merge: true)
    public function update(Request $request, $id)
    {
        $referensi = $this->db->collection($this->koleksi)->document($id);
        if (!$referensi->snapshot()->exists()) {
            return response()->json(['pesan' => 'Data tidak ditemukan'], 404);
        }

        $referensi->set($request->except(['id']), ['merge' => true]);
        return response()->json(['pesan' => 'Data berhasil diubah']);
    }

    // DELETE /api/firestore/produk/{id} (Hapus dokumen)
    public function destroy($id)
    {
        $referensi = $this->db->collection($this->koleksi)->document($id);
        if (!$referensi->snapshot()->exists()) {
            return response()->json(['pesan' => 'Data tidak ditemukan'], 404);
        }

        $referensi->delete();
        return response()->json(['pesan' => 'Data berhasil dihapus']);
    }
}
