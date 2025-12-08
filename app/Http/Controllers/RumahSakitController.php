<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RumahSakitController extends Controller
{
    private $fusekiEndpoint = 'http://localhost:3030/rumahsakit/query';
    
    // Prefix Namespace untuk SPARQL
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

        $detectedClass = null;
        $cleanQ = '';

        if ($request->filled('q')) {
            $tempQ = strtolower($q);

            if (preg_match('/\brsu\b/', $tempQ) || str_contains($tempQ, 'rumah sakit umum')) {
                $detectedClass = 'rs:RSU';
                $tempQ = str_replace(['rumah sakit umum', 'rsu'], '', $tempQ);
            } elseif (preg_match('/\brsia\b/', $tempQ) || str_contains($tempQ, 'ibu dan anak')) {
                $detectedClass = 'rs:RSIA';
                $tempQ = str_replace(['rumah sakit ibu dan anak', 'ibu dan anak', 'rsia'], '', $tempQ);
            } elseif (str_contains($tempQ, 'mata') && (str_contains($tempQ, 'rs') || str_contains($tempQ, 'sakit'))) {
                $detectedClass = 'rs:RSMata';
                $tempQ = str_replace(['rumah sakit mata', 'rs mata', 'mata'], '', $tempQ);
            } elseif (str_contains($tempQ, 'bedah') && (str_contains($tempQ, 'rs') || str_contains($tempQ, 'sakit'))) {
                $detectedClass = 'rs:RSBedah';
                $tempQ = str_replace(['rumah sakit bedah', 'rs bedah', 'bedah'], '', $tempQ);
            } elseif (preg_match('/\brsj\b/', $tempQ) || str_contains($tempQ, 'rumah sakit jiwa') || (str_contains($tempQ, 'jiwa') && (str_contains($tempQ, 'rs') || str_contains($tempQ, 'sakit')))) {
                $detectedClass = 'rs:RSJiwa';
                $tempQ = str_replace(['rumah sakit jiwa', 'rs jiwa', 'rsj', 'jiwa'], '', $tempQ);
            } elseif (str_contains($tempQ, 'ginjal') && (str_contains($tempQ, 'rs') || str_contains($tempQ, 'sakit') || str_contains($tempQ, 'khusus'))) {
                $detectedClass = 'rs:RSGinjal';
                $tempQ = str_replace(['rumah sakit ginjal', 'rs ginjal', 'rs khusus ginjal', 'khusus ginjal', 'ginjal'], '', $tempQ);
            }

            $tempQ = str_replace('rumah sakit', '', $tempQ);
            $tempQ = str_replace(['rs ', 'rs.', 'rs_'], '', $tempQ);
            $cleanQ = trim($tempQ);
        }

        $sparqlQuery = '
            SELECT DISTINCT ?id ?nama ?tipe ?noTelp ?nama_kecamatan ?nama_kota
            WHERE {
        ';

        if ($detectedClass) {
            $sparqlQuery .= ' ?rs_id rdf:type ' . $detectedClass . ' . ';
        } else {
            $sparqlQuery .= ' ?rs_id rdf:type ?rs_tipe . ?rs_tipe rdfs:subClassOf* rs:RumahSakit . ';
        }

        $sparqlQuery .= '
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

        if ($request->filled('kecamatan')) {
            $sparqlQuery .= ' ?rs_id rs:isLocated rs:' . $kecamatan . ' . ';
        }
        elseif ($request->filled('kota')) {
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
            $sparqlQuery .= ' ?rs_id rs:tipeRS "' . addslashes($tipe_rs) . '" . ';
        }

        if ($cleanQ !== '') {
            $sparqlQuery .= '
                OPTIONAL { ?rs_id rs:hasSpecialization ?spec_id . ?spec_id rdfs:label ?labelSpesialisasi . }

                FILTER (
                    CONTAINS(LCASE(?nama), "' . $cleanQ . '") ||
                    CONTAINS(LCASE(?labelSpesialisasi), "' . $cleanQ . '")
                )
            ';
        }

        $sparqlQuery .= '
                BIND(REPLACE(STR(?rs_id), STR(rs:), "") AS ?id)
            }
        ';

        $results = $this->queryFuseki($sparqlQuery);
        $dropdownData = $this->dropdown();

        if (is_null($results)) {
            return back()->with('error', 'Gagal terhubung ke server database. Pastikan Fuseki sudah berjalan.');
        }

        return view('pencarian', array_merge($dropdownData, [
            'results' => $results,
            'inputs' => $request->all()
        ]));
    }

    public function detail($id)
    {
        $sparqlQuery = '
            SELECT * WHERE {
                BIND(rs:' . $id . ' AS ?rs_id)

                ?rs_id rs:namaRS ?nama .
                ?rs_id rs:tipeRS ?tipe .
                ?rs_id rs:alamat ?alamat .
                ?rs_id rs:noTelp ?telepon .
                ?rs_id rs:linkGmaps ?gmaps .

                # --- TAMBAHAN: Ambil Koordinat (Sesuai RDF rs:lat dan rs:long) ---
                OPTIONAL { ?rs_id rs:lat ?lat . }
                OPTIONAL { ?rs_id rs:long ?long . }
                # -------------------------------------------------------------

                OPTIONAL {
                    ?rs_id rs:linkGbr ?gbr .
                }

                OPTIONAL {
                    ?rs_id rdf:type ?kelas_rs .
                    ?kelas_rs rdfs:subClassOf* rs:RumahSakit .
                    FILTER(?kelas_rs != rs:RumahSakit)
                    ?kelas_rs rdfs:label ?jenis_rs .
                }

                ?rs_id rs:hasSpecialization ?spec_id .
                ?spec_id rdfs:label ?spesialisasi .

                ?rs_id rs:providesFacility ?fac_id .
                ?fac_id rdfs:label ?fasilitas .

                ?rs_id rs:acceptsInsurance ?ins_id .
                ?ins_id rdfs:label ?asuransi .
            }
        ';
        $results = $this->queryFuseki($sparqlQuery);

        if (is_null($results)) {
            return redirect()->route('search.form')->with('error', 'Gagal terhubung ke server database.');
        }

        if (empty($results)) {
            abort(404, 'Rumah Sakit tidak ditemukan');
        }

        $rs = [
            'nama' => $results[0]['nama']['value'],
            'tipe' => $results[0]['tipe']['value'],
            'alamat' => $results[0]['alamat']['value'],
            'telepon' => $results[0]['telepon']['value'],
            'gmaps' => $results[0]['gmaps']['value'],
            'gbr' => $results[0]['gbr']['value'] ?? null,
            
            // --- TAMBAHAN: Masukkan ke Array agar bisa dibaca View ---
            'lat' => isset($results[0]['lat']) ? (float)$results[0]['lat']['value'] : null,
            'lng' => isset($results[0]['long']) ? (float)$results[0]['long']['value'] : null,
            // -------------------------------------------------------

            'spesialisasi' => [],
            'fasilitas' => [],
            'asuransi' => [],
            'jenis' => []
        ];

        foreach ($results as $row) {
            if (isset($row['spesialisasi'])) $rs['spesialisasi'][] = $row['spesialisasi']['value'];
            if (isset($row['fasilitas'])) $rs['fasilitas'][] = $row['fasilitas']['value'];
            if (isset($row['asuransi'])) $rs['asuransi'][] = $row['asuransi']['value'];
            if (isset($row['jenis_rs'])) $rs['jenis'][] = $row['jenis_rs']['value'];
        }

        $rs['spesialisasi'] = array_unique($rs['spesialisasi']);
        $rs['fasilitas'] = array_unique($rs['fasilitas']);
        $rs['asuransi'] = array_unique($rs['asuransi']);
        $rs['jenis'] = array_unique($rs['jenis']);

        return view('detail', ['rs' => $rs]);
    }

    private function detectSymptom($text)
    {
        $text = strtolower($text);
        $rules = config('symptoms');

        if (!$rules) return [];

        $foundSpecializations = [];

        foreach ($rules as $keyword => $specializationID) {
            if (str_contains($text, $keyword)) {
                $foundSpecializations[] = $specializationID;
            }
        }

        return array_unique($foundSpecializations);
    }

    public function chat(Request $request)
    {
        $message = strtolower($request->input('message'));

        $symptomSpecs = $this->detectSymptom($message);

        if (empty($symptomSpecs)) {
            if (str_contains($message, 'halo') || str_contains($message, 'hai')) {
                return response()->json([
                    'reply' => "Haloww! Aku asisten medis HealthSeek, <strong>EIMI</strong>. <br><br>
                    Ceritain aja keluhan kamu (contoh: 'mata buram' atau 'demam'), aku bakal carikan Rumah Sakit yang cocok buat kamu!",
                    'recommendations' => []
                ]);
            }
            return response()->json([
                'reply' => "Maaf, aku masih belum mengerti gejala yang kamu bilang 😔<br><br>
                Coba pakai kata kunci yang lebih umum, dan aku bakalan cari Rumah Sakit yang sesuai kebutuhan kamu!",
                'recommendations' => []
            ]);
        }

        $isGeneralSymptom = in_array('Umum', $symptomSpecs);

        $rankingLogic = '';

        if ($isGeneralSymptom) {
            $rankingLogic = '
                BIND(
                    IF(EXISTS {
                        ?rs rdf:type ?cek_tipe .
                        ?cek_tipe rdfs:subClassOf* rs:RSU
                    }, 1, 2)
                AS ?rank)
            ';
        } else {
            $rankingLogic = '
                BIND(
                    IF(EXISTS {
                        ?rs rdf:type ?cek_tipe .
                        ?cek_tipe rdfs:subClassOf* rs:RSK
                    }, 1, 2)
                AS ?rank)
            ';
        }

        $sparqlList = [];
        foreach($symptomSpecs as $spec) {
            $sparqlList[] = "rs:" . $spec;
        }
        $sparqlInString = implode(', ', $sparqlList);

        $sparqlQuery = '
            SELECT DISTINCT ?nama ?id ?tipe
            WHERE {
                ?rs rdf:type ?type . ?type rdfs:subClassOf* rs:RumahSakit .
                ?rs rs:namaRS ?nama .
                ?rs rs:tipeRS ?tipe .

                ?rs rs:hasSpecialization ?spec .
                FILTER (?spec IN (' . $sparqlInString . '))

                ' . $rankingLogic . '

                BIND(REPLACE(STR(?rs), STR(rs:), "") AS ?id)
            }
            ORDER BY ASC(?rank) ASC(?tipe) ASC(?nama)
            LIMIT 5
        ';

        $results = $this->queryFuseki($sparqlQuery);

        if (!empty($results)) {
            $specsText = implode(', ', $symptomSpecs);

            $introText = $isGeneralSymptom
                ? "Untuk keluhan seperti itu, ini aku kasih beberapa rekomendasi Rumah Sakit buat kamu!"
                : "Sepertinya kamu butuh penanganan di spesialis <strong>$specsText</strong>!<br><br>
                Ini aku kasih beberapa rekomendasi Rumah Sakit buat kamu";

            return response()->json([
                'reply' => $introText,
                'recommendations' => $results
            ]);
        } else {
            return response()->json([
                'reply' => "Aku mendeteksi gejala <strong>" . implode(', ', $symptomSpecs) . "</strong>,
                namun sayangnya untuk saat ini belum ada data Rumah Sakit yang cocok di database 😔",
                'recommendations' => []
            ]);
        }
    }

    // UPDATED: Fungsi Terdekat dengan Koordinat
    
    public function terdekat()
    {
        $sparqlQuery = '
            SELECT ?id ?nama ?tipe ?noTelp ?lat ?long
            WHERE {
                ?rs rdf:type ?type . ?type rdfs:subClassOf* rs:RumahSakit .
                ?rs rs:namaRS ?nama .
                ?rs rs:tipeRS ?tipe .
                ?rs rs:noTelp ?noTelp .
                
                # Mengambil Koordinat dari Fuseki (Nama properti sesuai RDF)
                OPTIONAL { ?rs rs:lat ?lat . }
                OPTIONAL { ?rs rs:long ?long . }

                BIND(REPLACE(STR(?rs), STR(rs:), "") AS ?id)
            }
        ';

        $results = $this->queryFuseki($sparqlQuery);
        $cleanData = [];

        if ($results) {
            foreach ($results as $row) {
                if (isset($row['lat']) && isset($row['long'])) {
                    $cleanData[] = [
                        'id' => $row['id']['value'],
                        'nama' => $row['nama']['value'],
                        'tipe' => $row['tipe']['value'],
                        'telp' => $row['noTelp']['value'],
                        'lat' => (float) $row['lat']['value'],
                        'lng' => (float) $row['long']['value']
                    ];
                }
            }
        }

        return view('terdekat', ['allHospitals' => $cleanData]);
    }
}