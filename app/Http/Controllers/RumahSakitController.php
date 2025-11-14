<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RumahSakitController extends Controller
{
    public function show($id)
    {
        // Contoh data (sementara dari array)
        $rumahSakit = [
            [
                'id' => 1,
                'nama' => 'RSU Royal Prima',
                'alamat' => 'Jl. Ayahanda No.68A, Medan Petisah, Medan',
                'tipe' => 'B',
                'No Handphone' => '0016256',

            ],
            [
                'id' => 2,
                'nama' => 'RS Columbia Asia Medan',
                'alamat' => 'Jl. Listrik No.2A, Medan Petisah, Medan',
                'tipe' => 'A',
                'No Handphone' => '0016256',
                'deskripsi' => 'RS bertaraf internasional dengan pelayanan terbaik di Medan.'
            ],
            [
                'id' => 3,
                'nama' => 'RS Hermina Medan',
                'alamat' => 'Jl. Asrama No.33, Helvetia, Medan',
                'tipe' => 'C',
                'No Handphone' => '0016256',
                'deskripsi' => 'Rumah sakit keluarga dengan layanan ibu dan anak unggulan.'
            ],
        ];

        // Cari RS berdasarkan id
        $rs = collect($rumahSakit)->firstWhere('id', (int)$id);

        if (!$rs) {
            abort(404, 'Rumah sakit tidak ditemukan.');
        }

        return view('detail', compact('rs'));
    }
}
