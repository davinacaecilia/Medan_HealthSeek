<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RumahSakitController extends Controller
{
    public function show($id)
    {
        $dataRS = [
            1 => [
                'id' => 1,
                'nama' => 'RSU Royal Prima',
                'alamat' => 'Jl. Ayahanda No. 68, Sei Putih Tengah, Kec. Medan Petisah',
                'No Handphone' => '0812-3456-7890', 
                'tipe' => 'A',
                'Jenis' => 'RSU', 
                'Fasilitas' => 'ICU_Dewasa,ICCU,NICU,PICU,Lab_Patologi_Anatomi,Lab_Patologi_Klinik,CT_Scanner,MRI_Scanner,Rontgen,USG,Layanan_Ambulans,UGD_24_Jam,Apotek_24_Jam,Layanan_Rawat_Inap,Layanan_Rawat_Jalan,Ruang_Operasi,Unit_Fisioterapi',
                'Spesialis' => 'Anak,BedahUmum,GigiDanMulut,GinjalDanHipertensi,Jantung,KandunganDanGinekologi,KulitDanKelamin,Mata,Onkologi,Ortopedi,Paru,PenyakitDalam,Saraf,THT,Psikiatri,Urologi',
                'Asuransi' => 'BPJSKesehatan,BNI_Life,Prudential',
                'link_gmaps' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3494.6637212622354!2d98.68038297432491!3d3.5759673963982053!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3031304a07e42117%3A0xeb7f812c23db0b1f!2sRS%20Martha%20Friska%20Medan%20%2C!5e1!3m2!1sen!2sid!4v1763393088646!5m2!1sen!2sid',
                'website' => 'http://www.royalprima.com'
            ],
            2 => [
                'id' => 2,
                'nama' => 'RS Columbia Asia Medan',
                'alamat' => 'Jl. Listrik No. 2, Petisah Tengah, Kec. Medan Petisah',
                'No Handphone' => '4566 368',
                'tipe' => 'B',
                'Jenis' => 'RSU',
                'Fasilitas' => 'ICU_Dewasa,NICU,PICU,Bank_Darah,Lab_Patologi_Anatomi,Lab_Patologi_Klinik,CT_Scanner,Mammografi,MRI_Scanner,Rontgen,USG,Layanan_Ambulans,UGD_24_Jam,Apotek_24_Jam,Layanan_Rawat_Inap,Layanan_Rawat_Jalan,Ruang_Operasi,Unit_Fisioterapi',
                'Spesialis' => 'Anak,BedahUmum,GigiDanMulut,GinjalDanHipertensi,Jantung,KandunganDanGinekologi,KulitDanKelamin,Mata,Onkologi,Ortopedi,Paru,PenyakitDalam,Saraf,THT,Psikiatri,Urologi',
                'Asuransi' => 'AIA_Finance,Allianz,AXA_Mandiri,BNI_Life,Great_Eastern_Life,Manulife,Prudential,Sinarmas_MSIG_Life',
                'link_gmaps' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3453.1472240019375!2d98.6720793!3d3.585727!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303131c952240c2d%3A0x91633eb373cb5093!2sRS%20Columbia%20Asia%20Medan!5e1!3m2!1sid!2sid!4v1763395438496!5m2!1sid!2sid',
                'website' => 'http://columbiaasia.co.id/rs-columbia-asia-medan'
            ],
            3 => [
                'id' => 3,
                'nama' => 'RS Hermina Medan',
                'alamat' => 'Jl. Asrama No. 34, Sei Sikambing C II, Kec. Medan Helvetia',
                'No Handphone' => '80862525',
                'tipe' => 'C',
                'Jenis' => 'RSIA', 
                'Fasilitas' => 'ICU_Dewasa,ICCU,NICU,PICU,Lab_Patologi_Klinik,CT_Scanner,Rontgen,USG,Layanan_Ambulans,UGD_24_Jam,Apotek_24_Jam,Layanan_Rawat_Inap,Layanan_Rawat_Jalan,Ruang_Operasi,Unit_Fisioterapi',
                'Spesialis' => 'Anak,BedahUmum,GigiDanMulut,GinjalDanHipertensi,Jantung,KandunganDanGinekologi,KulitDanKelamin,Mata,Onkologi,Ortopedi,Paru,PenyakitDalam,Saraf,THT,Psikiatri,Urologi',
                'Asuransi' => 'BPJSKesehatan,AIA_Finance,Allianz',
                'link_gmaps' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.1461755444957!2d98.6597572!3d3.5860046000000008!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30312e29e2d50817%3A0x5a3d252e34894e86!2sRumah%20Sakit%20Khusus%20Ginjal%20Rasyida!5e1!3m2!1sid!2sid!4v1763395682144!5m2!1sid!2sid',
                'website' => 'http://herminahospitals.com/id/branch/hermina-medan'
            ],
        ];

        $rs = $dataRS[$id] ?? null;

        if (!$rs) {
            abort(404);
        }

        return view('detail', compact('rs'));
    }
}