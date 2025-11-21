<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RumahSakitController extends Controller
{
    private $fusekiEndpoint = 'http://localhost:3030/rsdb/query'; 
    private $prefix = '
        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        PREFIX rs: <http://www.semanticweb.org/user/ontologies/2025/10/rs#>
    ';

    private function queryFuseki($sparqlQuery)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/sparql-results+json'
            ])->get($this->fusekiEndpoint, [
                'query' => $this->prefix . $sparqlQuery
            ]);

            if ($response->successful()) {
                return $response->json()['results']['bindings'];
            }
            return []; 

        } catch (\Exception $e) {
            report($e); 
            return null;
        }
    }

    private function dropdown()
    {
        $kotaQuery = '
            SELECT ?id_short ?label
            WHERE {
                { ?id rdf:type rs:Kota } UNION { ?id rdf:type rs:Kabupaten } .
                ?id rdfs:label ?label .
                BIND(REPLACE(STR(?id), STR(rs:), "") AS ?id_short)
            } ORDER BY ?label
        ';

        $kecamatanQuery = '
            SELECT ?id_short ?label ?induk_id
            WHERE {
                ?id rdf:type rs:Kecamatan .
                ?id rdfs:label ?label .
                ?id rs:isPartOf ?induk .
                
                BIND(REPLACE(STR(?id), STR(rs:), "") AS ?id_short)
                BIND(REPLACE(STR(?induk), STR(rs:), "") AS ?induk_id)
            } ORDER BY ?label
        ';
        
        $spesialisasiQuery = '
            SELECT ?id_short ?label
            WHERE {
                ?id rdf:type rs:Spesialisasi .
                ?id rdfs:label ?label .
                BIND(REPLACE(STR(?id), STR(rs:), "") AS ?id_short)
            } ORDER BY ?label
        ';

        $asuransiQuery = '
            SELECT ?id_short ?label
            WHERE {
                ?id rdf:type ?tipe .
                ?tipe rdfs:subClassOf* rs:Asuransi .
                ?id rdfs:label ?label .
                BIND(REPLACE(STR(?id), STR(rs:), "") AS ?id_short)
            } ORDER BY ?label
        ';

        return [
            'kotaList' => $this->queryFuseki($kotaQuery) ?? [],
            'kecamatanList' => $this->queryFuseki($kecamatanQuery) ?? [],
            'spesialisasiList' => $this->queryFuseki($spesialisasiQuery) ?? [],
            'asuransiList' => $this->queryFuseki($asuransiQuery) ?? [],
        ];
    }

    public function home()
    {
        $dropdownData = $this->dropdown();

        $featuredQuery = '
            SELECT ?id ?nama ?tipe ?noTelp ?nama_kecamatan ?nama_kota
            WHERE {
                ?rs rdf:type ?class .
                ?class rdfs:subClassOf* rs:RumahSakit .
                
                ?rs rs:namaRS ?nama .
                ?rs rs:tipeRS ?tipe .
                ?rs rs:noTelp ?noTelp .

                OPTIONAL { 
                    ?rs rs:isLocated ?kec_id .
                    ?kec_id rdfs:label ?nama_kecamatan .
                    
                    OPTIONAL {
                        ?kec_id rs:isPartOf ?kota_id .
                        ?kota_id rdfs:label ?nama_kota .
                    }
                }
                
                BIND(REPLACE(STR(?rs), STR(rs:), "") AS ?id)
            }
            ORDER BY RAND()
            LIMIT 4
        ';

        $featuredHospitals = $this->queryFuseki($featuredQuery);

       return view('home', array_merge($dropdownData, [
            'featuredHospitals' => $featuredHospitals
        ]));
    }

    public function search(Request $request)
    {
        $q = $request->input('q'); 
        $kota = $request->input('kota');
        $kecamatan = $request->input('kecamatan');
        $spesialisasi = $request->input('spesialisasi');
        $asuransi = $request->input('asuransi');
        $tipe_rs = $request->input('tipe_rs');

        // Mulai membangun string kueri SPARQL
        $sparqlQuery = '
            SELECT DISTINCT ?nama ?tipe ?noTelp ?nama_kecamatan ?nama_kota
            WHERE {
            

                ?rs_id rs:namaRS ?nama .
                ?rs_id rs:tipeRS ?tipe .
                ?rs_id rs:noTelp ?noTelp .

                OPTIONAL { 
                    ?rs_id rs:isLocated ?kec_id .
                    ?kec_id rdfs:label ?nama_kecamatan .
                    
                    OPTIONAL {
                        ?kec_id rs:isPartOf ?kota_id .
                        ?kota_id rdfs:label ?nama_kota .
                    }
                }
        ';

        // --- FILTER DINAMIS ---

        // 1. Filter dari Dropdown (Pencarian Presisi)
        if ($request->filled('kecamatan')) {
            // Jika user memilih Kecamatan spesifik, cari RS di kecamatan itu
            $sparqlQuery .= ' ?rs_id rs:isLocated rs:' . $kecamatan . ' . ';
        } 
        elseif ($request->filled('kota')) {
            // Jika Kecamatan KOSONG, tapi Kota DIPILIH, cari RS di semua kecamatan milik kota itu
            $sparqlQuery .= ' 
                ?rs_id rs:isLocated ?kec_cek .
                ?kec_cek rs:isPartOf rs:' . $kota . ' . 
            ';
        }
        if ($request->filled('spesialisasi')) {
            $sparqlQuery .= ' ?rs_id rs:hasSpecialization rs:' . $spesialisasi . ' . ';
        }
        if ($request->filled('asuransi')) {
            $sparqlQuery .= ' ?rs_id rs:acceptsInsurance rs:' . $asuransi . ' . ';
        }
        if ($request->filled('tipe_rs')) {
            // Pastikan aman dari SPARQL injection sederhana
            $sparqlQuery .= ' ?rs_id rs:tipeRS "' . addslashes($tipe_rs) . '" . ';
        }

        // 2. Filter dari Search Bar (Pencarian Teks Bebas)
        if ($request->filled('search')) {
            // Kita perlu menghubungkan ke label-label untuk dicari
            $sparqlQuery .= '
                OPTIONAL { ?rs_id rs:isLocated ?kec_id . ?kec_id rdfs:label ?labelKecamatan . }
                OPTIONAL { ?rs_id rs:hasSpecialization ?spec_id . ?spec_id rdfs:label ?labelSpesialisasi . }
            ';
            
            // Ambil teks pencarian dan buat jadi huruf kecil
            $searchText = strtolower($q); 
            
            // Gunakan FILTER CONTAINS() untuk mencari
            $sparqlQuery .= '
                FILTER (
                    CONTAINS(LCASE(?nama), "' . $searchText . '") || 
                    CONTAINS(LCASE(?labelKecamatan), "' . $searchText . '") ||
                    CONTAINS(LCASE(?labelSpesialisasi), "' . $searchText . '")
                )
            ';
        }

        // Akhiri kueri
        $sparqlQuery .= '
                BIND(REPLACE(STR(?rs_id), STR(rs:), "") AS ?id)
            } 
        ';

        $results = $this->queryFuseki($sparqlQuery);
        $dropdownData = $this->dropdown();

        if (is_null($results)) {
            return back()->with('error', 'Gagal terhubung ke server database. Pastikan Fuseki sudah berjalan.');
        }

        // Tampilkan view 'hasil.blade.php' dan kirim data hasilnya
        return view('pencarian', array_merge($dropdownData, [
            'results' => $results,
            'inputs' => $request->all()
        ]));
    }
    
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