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

    {{-- NAVBAR --}}
    @include('partial.navbar')

    <div class="container">

        {{-- Judul Pencarian --}}
        <h2 class="judul-terdekat">
            Pencarian Berdasarkan Lokasi Terdekat
        </h2>

        {{-- MAP --}}
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15960.466779963084!2d98.65!3d3.5952!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303131ba5ceb1ee5%3A0x3039d80b2206020!2sMedan!5e0!3m2!1sid!2sid!4v0000000000"
                width="100%"
                height="260"
                style="border:0; border-radius:12px;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

        <div class="radius-input-container">
            <label for="radius">Radius (km):</label>
            <input type="number" id="radius" placeholder="Misal 5" min="0" step="0.1">
        </div>

        {{-- Tombol Cari RS --}}
        <button id="btnCari" class="btn-cari-rs">
            Cari RS Terdekat Berdasarkan Lokasi Saya
        </button>

        {{-- Judul List RS --}}
        <h4 id="judulList" class="judul-list" style="display:none;">Hasil Rumah Sakit Terdekat</h4>

        {{-- List RS --}}
        <div id="listRS" class="rs-list" style="display:none;"></div>

    </div>

    {{-- CHATBOT --}}
    @include('partials.chatbot')

    {{-- FOOTER --}}
    <footer>
        <p>© {{ date('Y') }} HealthSeek. All Rights Reserved.</p>
    </footer>

    {{-- JS Dinamis --}}
    <script>
        document.getElementById('btnCari').addEventListener('click', function () {
        let radius = parseFloat(document.getElementById('radius').value); // ambil value input
        let dummy = [
            { id: 1, nama: "RSU Sehat Selalu sehat sukses", tipe: "Rumah Sakit Umum", telp: "061-111111", jarak: 0.8 },
            { id: 2, nama: "RS Mitra Medika", tipe: "Rumah Sakit Umum", telp: "061-222222", jarak: 1.4 },
            { id: 3, nama: "RS Kasih Ibu", tipe: "Rumah Sakit Ibu & Anak", telp: "061-333333", jarak: 2.1 },
            { id: 4, nama: "RS Umum Pusat Hijau", tipe: "Rumah Sakit Swasta", telp: "061-444444", jarak: 3.0 }
        ];

        // Filter berdasarkan radius, jika diisi
        let filtered = (!isNaN(radius)) ? dummy.filter(item => item.jarak <= radius) : dummy;

        // Sort dari terdekat ke terjauh
        filtered.sort((a, b) => a.jarak - b.jarak);

        let listBox = document.getElementById('listRS');
        let judulList = document.getElementById('judulList');

        listBox.innerHTML = "";
        listBox.style.display = "block";
        judulList.style.display = "block";

        filtered.forEach(item => {
            listBox.innerHTML += `
                <div class="rs-item">
                <div class="rs-row">

                    <div class="rs-nama">${item.nama}</div>

                    <div class="rs-right">
                        <div class="rs-tipe">${item.tipe}</div>
                        <div class="rs-telp">${item.telp}</div>
                        <div class="rs-jarak">${item.jarak} km</div>
                    </div>

                    <div class="rs-detail-wrapper">
                        <button class="btn-detail">Detail</button>
                    </div>

                </div>
            </div>
            `;
        });

        if(filtered.length === 0){
            listBox.innerHTML = `<p style="color:#dc3545; font-weight:500;">Tidak ada RS dalam radius ${radius} km</p>`;
        }
    });

    </script>

</body>
</html>
