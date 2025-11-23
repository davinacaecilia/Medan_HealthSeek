<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $rumahSakit = [
                    [
                        'id' => 1,
                        'nama' => 'Rumah Sakit Umum Royal Prima',
                        'alamat' => 'Jl. Ayahanda No.68A, Medan Petisah, Medan',
                        'No Handphone' => '0812-3456-7890',
                        'tipe' => 'A'
                    ],
                    [
                        'id' => 2,
                        'nama' => 'RS Columbia Asia Medan',
                        'alamat' => 'Jl. Listrik No.2A, Medan Petisah, Medan',
                        'No Handphone' => '0821-2345-6789',
                        'tipe' => 'B'
                    ],
                    [
                        'id' => 3,
                        'nama' => 'RS Hermina Medan',
                        'alamat' => 'Jl. Asrama No.33, Helvetia, Medan',
                        'No Handphone' => '0831-9876-5432',
                        'tipe' => 'C'
                    ],
                ];

        $keyword = $request->input('search');

        if ($keyword) {
            $rumahSakit = array_filter($rumahSakit, function ($rs) use ($keyword) {
                return stripos($rs['nama'], $keyword) !== false;
            });
        }

        return view('home', compact('rumahSakit', 'keyword'));
    }
    public function detail($id)
{
    $dataRS = [
        1 => [
            'nama' => 'RSU Royal Prima',
            'alamat' => 'Jl. Ayahanda No.68A, Medan Petisah, Medan',
            'No Handphone' => '0812-3456-7890',
            'tipe' => 'A'
        ],
        2 => [
            'nama' => 'RS Columbia Asia Medan',
            'alamat' => 'Jl. Listrik No.2A, Medan Petisah, Medan',
            'No Handphone' => '0821-2345-6789',
            'tipe' => 'B'
        ],
        3 => [
            'nama' => 'RS Hermina Medan',
            'alamat' => 'Jl. Asrama No.33, Helvetia, Medan',
            'No Handphone' => '0831-9876-5432',
            'tipe' => 'C'
        ],
    ];

    $rs = $dataRS[$id] ?? null;

    if (!$rs) {
        abort(404);
    }

    return view('detail', compact('rs'));
}

}
