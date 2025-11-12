<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class HomeController extends Controller
{
    private $fusekiEndpoint = 'http://localhost:3030/rsdb/query'; 
    private $prefix = '
        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        PREFIX rs: <http://www.semanticweb.org/user/ontologies/2025/10/rumahsakit#>
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
    
    public function index(Request $request)
    {
        $kecamatanQuery = '
            SELECT ?id_short ?label
            WHERE {
                ?id rdf:type rs:Kecamatan .
                ?id rdfs:label ?label .
                BIND(REPLACE(STR(?id), STR(rs:), "") AS ?id_short)
            } 
            ORDER BY ?label
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

        $kecamatanList = $this->queryFuseki($kecamatanQuery);
        $spesialisasiList = $this->queryFuseki($spesialisasiQuery);
        $asuransiList = $this->queryFuseki($asuransiQuery);

        return view('home', [
            'kecamatanList' => $kecamatanList,
            'spesialisasiList' => $spesialisasiList,
            'asuransiList' => $asuransiList,
        ]);
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
