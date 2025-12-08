<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rumah Sakit - Medan HealthSeek</title>
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    {{-- STYLE TAMBAHAN --}}
    <style>
        /* KHUSUS BPJS (Hijau Mencolok) - Ukuran Badge Kecil */
        .tag.tag-bpjs {
            background-color: #27ae60 !important; /* Hijau */
            color: white !important;
            font-weight: bold;
            padding: 5px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
        }

        /* KHUSUS NON-BPJS (Merah Mencolok) - Ukuran Badge Kecil */
        .tag.tag-non {
            background-color: #e74c3c !important; /* Merah */
            color: white !important;
            font-weight: bold;
            padding: 5px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
        }
        
        /* Style Tombol Share WA */
        .btn-share-wa {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #25D366; 
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 15px;
            transition: background 0.3s;
            text-align: center;
        }
        .btn-share-wa:hover {
            background-color: #128C7E;
        }
    </style>
</head>
<body>

    @include('partials.navbar')
    <main class="container">

        <article class="detail-content">
            <figure class="detail-image-container">
                @if(isset($rs['gbr']) && $rs['gbr'])
                    <img src="{{ $rs['gbr'] }}" alt="{{ $rs['nama'] }}">
                    <figcaption>Tampak Depan Rumah Sakit</figcaption>
                @else
                    <img src="https://operaparallele.org/wp-content/uploads/2023/09/Placeholder_Image.png" alt="{{ $rs['nama'] }}">
                    <figcaption>Gambar Tidak Tersedia</figcaption>
                @endif
            </figure>

            <header class="detail-header">
                <h1>{{ $rs['nama'] }}</h1>
                <span class="tipe-rs tipe-{{ strtolower($rs['tipe']) }}">{{ $rs['tipe'] }}</span>
            </header>

            <section class="detail-section">
                <h2>Asuransi Diterima</h2>
                <ul class="tag-list">
                    @forelse ($rs['asuransi'] as $item)
                        @php
                            $text = strtoupper($item);
                            $specialClass = '';

                            // Logika Class
                            if (strpos($text, 'NON') !== false) {
                                $specialClass = 'tag-non';
                            } elseif (strpos($text, 'BPJS') !== false) {
                                $specialClass = 'tag-bpjs';
                            }
                        @endphp

                        <li class="tag {{ $specialClass }}">
                            {{-- Ikon hanya muncul di tag spesial --}}
                            @if($specialClass == 'tag-bpjs') <i class='bx bxs-check-shield'></i> @endif
                            @if($specialClass == 'tag-non') <i class='bx bxs-x-circle'></i> @endif
                            {{ $item }}
                        </li>
                    @empty
                        <li class="tag">Informasi tidak tersedia</li>
                    @endforelse
                </ul>
            </section>

            <section class="detail-section">
                <h2>Fasilitas</h2>
                <ul class="tag-list">
                    @forelse ($rs['fasilitas'] as $item)
                        <li class="tag">{{ $item }}</li>
                    @empty
                        <li class="tag">Informasi tidak tersedia</li>
                    @endforelse
                </ul>
            </section>

            <section class="detail-section">
                <h2>Spesialis Unggulan</h2>
                <ul class="tag-list">
                    @forelse ($rs['spesialisasi'] as $item)
                        <li class="tag">{{ $item }}</li>
                    @empty
                        <li class="tag">Informasi tidak tersedia</li>
                    @endforelse
                </ul>
            </section>
        </article>

        <aside class="detail-sidebar">
            <div class="info-box">
                <h2>Informasi & Lokasi</h2>

                <p><strong>Alamat:</strong><br>
                {{ $rs['alamat'] }}</p>

                <p><strong>No. Telepon:</strong><br>
                {{ $rs['telepon'] }}</p>

                <p><strong>Jenis RS:</strong><br>
                @foreach($rs['jenis'] as $jenis)
                    {{ $jenis }}
                @endforeach
                </p>

                <!-- Ini buat share ke Wa. Bisa dihapus kalo ngerasa ga butuh ya dap. -->
                <a href="https://wa.me/?text=Hai! Aku nemu informasi rumah sakit yang menarik di HealthSeek nih. Coba cek deh:%0A%0A*{{ urlencode($rs['nama']) }}*%0A{{ urlencode($rs['alamat']) }}%0A%0ASelengkapnya:%0A{{ urlencode(url()->current()) }}" 
                target="_blank" 
                class="btn-share-wa">
                    <i class='bx bxl-whatsapp' style='font-size: 1.5rem;'></i> Bagikan ke WA
                </a>

                <div id="jarak-wrapper" style="display: none; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ccc;">
                    <p style="color: #7f8c8d; font-weight: bold; margin-bottom: 5px;">
                        Estimasi Jarak:
                    </p>
                    <div id="jarak-container-inner" style="font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; gap: 8px; color: #555;">
                        <i id="jarak-icon" class='bx bx-loader-alt bx-spin'></i> 
                        <span id="jarak-text">Menghitung...</span>
                    </div>
                </div>

            </div>

            <div class="map-box">
                <h2>Lokasi di Peta</h3>
                <div class="gmaps-embed">
                    <iframe
                        src="{{ $rs['gmaps'] }}"
                        width="100%"
                        height="300"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </aside>
    </main>

    @include('partials.chatbot')

    <footer>
        <p>© {{ date('Y') }} HealthSeek. All Rights Reserved.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rsLat = {{ $rs['lat'] ?? 'null' }};
            const rsLng = {{ $rs['lng'] ?? 'null' }};
            
            const jarakWrapper = document.getElementById('jarak-wrapper');
            const jarakInner = document.getElementById('jarak-container-inner');
            const jarakText = document.getElementById('jarak-text');
            const jarakIcon = document.getElementById('jarak-icon');

            if (rsLat && rsLng && navigator.geolocation) {
                jarakWrapper.style.display = "block";

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        getRoadDistance(userLat, userLng, rsLat, rsLng);
                    }, 
                    function(error) {
                        jarakText.innerHTML = "<span style='font-size:0.8rem; color:red;'>Gagal deteksi lokasi</span>";
                        jarakIcon.className = 'bx bx-error';
                    },
                    { enableHighAccuracy: false, timeout: 5000 }
                );
            }

            function getRoadDistance(lat1, lon1, lat2, lon2) {
                const url = `https://router.project-osrm.org/route/v1/driving/${lon1},${lat1};${lon2},${lat2}?overview=false`;
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 3000);

                fetch(url, { signal: controller.signal })
                    .then(response => response.json())
                    .then(data => {
                        clearTimeout(timeoutId);
                        if (data.code === 'Ok') {
                            const distanceKm = (data.routes[0].distance / 1000).toFixed(2);
                            jarakText.innerHTML = `${distanceKm} km <small style="font-size:0.7rem; font-weight:normal;">(Jalan Raya)</small>`;
                            jarakInner.style.color = "#27ae60"; 
                            jarakIcon.className = "bx bx-car"; 
                        } else {
                            fallbackHaversine(lat1, lon1, lat2, lon2);
                        }
                    })
                    .catch(err => {
                        fallbackHaversine(lat1, lon1, lat2, lon2);
                    });
            }

            function fallbackHaversine(lat1, lon1, lat2, lon2) {
                const R = 6371; 
                const dLat = deg2rad(lat2 - lat1);
                const dLon = deg2rad(lon2 - lon1);
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                          Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                const d = (R * c).toFixed(2);
                jarakText.innerHTML = `${d} km <small style="font-size:0.7rem; font-weight:normal;">(Garis Lurus)</small>`;
                jarakInner.style.color = "#e67e22"; 
                jarakIcon.className = "bx bx-run"; 
            }
            function deg2rad(deg) { return deg * (Math.PI / 180); }
        });
    </script>

</body>
</html>