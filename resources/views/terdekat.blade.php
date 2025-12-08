<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Lokasi Terdekat - Medan HealthSeek</title>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/terdekat.css') }}">
</head>
<body>

    @include('partials.navbar')

    <div class="container">

        <h2 class="judul-terdekat">
            Pencarian Berdasarkan Lokasi Terdekat
        </h2>

        {{-- MAP --}}
        <div class="map-container">
            <iframe
                id="gmap_canvas"
                src="https://maps.google.com/maps?q=Sumatera%20Utara&t=&z=9&ie=UTF8&iwloc=&output=embed"
                width="100%"
                height="300"
                style="border:0; border-radius:12px;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

        <div class="radius-input-container">
            <label for="radius">Radius Maksimal (km):</label>
            <input type="number" id="radius" placeholder="radius..." min="0" step="0.1">
        </div>

        <button id="btnCari" class="btn-cari-rs">
            <i class='bx bx-current-location'></i> Cari RS di Sekitar Saya
        </button>

        <h4 id="judulList" class="judul-list" style="display:none;">Hasil Rumah Sakit Terdekat</h4>
        <div id="loading" style="display:none; text-align:center; margin-top:20px;">Sedang melacak lokasi & menghitung jarak...</div>
        
        {{-- List RS --}}
        <div id="listRS" class="rs-list" style="display:none;"></div>

    </div>

    @include('partials.chatbot')

    <footer>
        <p>© {{ date('Y') }} HealthSeek. All Rights Reserved.</p>
    </footer>

    {{-- 
        BAGIAN PENTING: 
        1. Kita definisikan variabel global window.allHospitals pakai PHP Blade.
        2. Baru kita panggil file JS eksternal.
    --}}
    <script>
        // Data dari Controller dikirim ke Global Variable JavaScript
        window.allHospitals = @json($allHospitals);
    </script>
    
    {{-- Panggil File JS Terpisah --}}
    <script src="{{ asset('script/terdekat.js') }}"></script>

</body>
</html>