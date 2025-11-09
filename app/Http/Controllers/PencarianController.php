<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PencarianController extends Controller
{
    public function index()
    {
        $rumahSakit = [
            [
                'nama' => 'RSU Medan Sehat',
                'tipe' => 'A',
                'alamat' => 'Jl. Sisingamangaraja No.45, Medan',
                'asuransi' => 'BPJS, Mandiri',
                'spesialis' => 'Umum, Anak, Bedah',
            ],
            [
                'nama' => 'RS Harapan Baru',
                'tipe' => 'B',
                'alamat' => 'Jl. Gatot Subroto No.12, Medan',
                'asuransi' => 'BPJS, Prudential',
                'spesialis' => 'Jantung, Kulit',
            ],
            [
                'nama' => 'RS Kasih Ibu',
                'tipe' => 'C',
                'alamat' => 'Jl. Iskandar Muda No.77, Medan',
                'asuransi' => 'Mandiri',
                'spesialis' => 'Kandungan, Anak',
            ],
        ];

        return view('pencarian', compact('rumahSakit'));
    }
}
