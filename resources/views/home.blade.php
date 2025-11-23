<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medan HealthSeek</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>


    <!-- Hero Section -->
    <header class="hero">
        <div class="floating-icons">
            <span class="icon">💊</span>
            <span class="icon">🩺</span>
            <span class="icon">💉</span>
            <span class="icon">🏥</span>
            <span class="icon">⚕</span>
            <span class="icon">🧑‍⚕</span>
            <span class="icon">❤‍🩹</span>
            <span class="icon">💉</span>
        </div>
          <div class="logo">
            <img src="{{ asset('/logoo.png') }}" alt="HealthSeek Logo">
        </div>

        <nav class="nav">
            <a href="{{ route('rumahSakit.home') }}" class="nav-link active">Beranda</a>
        </nav>

        <div class="hero-content">
            <h1>HealthSeek</h1>
            <p class="subtitle">Temukan rumah sakit terbaik di Sumatera Utara dengan cepat dan mudah</p>

                <!-- Baris pencarian -->
            <form method="GET" action="{{ route('rumahSakit.list') }}" class="search-box">
                <div class="searching">
                    <input type="text" id="search" name="q" placeholder="Cari rumah sakit..." value="{{ $keyword ?? '' }}">
                    <button type="submit">Cari</button>
                </div>

                <!-- FILTER BAR -->
                <div class="filter-top">
                    <select id="tipe_rs" name="tipe_rs">
                        <option value="">Semua Tipe</option>
                        <option value="A">Tipe A</option>
                        <option value="B">Tipe B</option>
                        <option value="C">Tipe C</option>
                        <option value="D">Tipe D</option>
                    </select>

                    <select id="asuransi" name="asuransi">
                        <option value="">Semua Asuransi</option>
                        @foreach($asuransiList as $item)
                            <option value="{{ $item['id_short']['value'] }}">
                                {{ $item['label']['value'] }}
                            </option>
                        @endforeach
                    </select>

                    <select id="spesialisasi" name="spesialisasi">
                        <option value="">Semua Spesialisasi</option>
                        @foreach($spesialisasiList as $item)
                            <option value="{{ $item['id_short']['value'] }}">
                                {{ $item['label']['value'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-bottom">

            <!-- Kabupaten/Kota -->
            <select id="kota" name="kota">
                <option value="">Semua Kabupaten/Kota</option>
                @foreach($kotaList as $item)
                    <option value="{{ $item['id_short']['value'] }}">
                        {{ $item['label']['value'] }}
                    </option>
                @endforeach
            </select>

            <!-- Kecamatan (akan dinamis sesuai kabupaten) -->
            <select id="kecamatan" name="kecamatan" >
                <option value="">Semua Kecamatan</option>
            </select>
        </div>
        </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="card-container">
        @forelse($featuredHospitals as $rs)
            <div class="card">
                <div class="card-header">
                    <h2>{{ $rs['nama']['value'] }}</h2>
                    <span class="tipe tipe-{{ strtolower($rs['tipe']['value']) }}">{{ $rs['tipe']['value'] }}</span>
                </div>

                <div class="card-location">
                    <i class='bx bx-map'></i>
                    <span>Kec. {{ $rs['nama_kecamatan']['value'] }}, {{ $rs['nama_kota']['value'] }}</span>
                </div>

                <div class="card-phone">
                    <i class='bx bx-phone'></i>
                    <span>{{ $rs['noTelp']['value'] }}</span>
                </div>

                <div class="card-footer-center">
                    <a href="{{ route('rumahSakit.detail', ['id' =>$rs['id']['value'] ]) }}" class="btn-detail">Detail</a>
                </div>
            </div>
        @empty
            <div class="not-found">
                <p>Tidak ada data rumah sakit ditemukan.</p>
            </div>
        @endforelse
    </main>  

    @if (count($featuredHospitals) >= 1)
        <div class="see-more">
            <a href="{{ route('rumahSakit.list') }}" class="btn-see-more">See More →</a>
        </div>
    @endif

    <footer>
        <p>&copy; {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

    <script>
        const semuaKecamatan = @json($kecamatanList);

        const kotaDropdown = document.getElementById('kota');
        const kecamatanDropdown = document.getElementById('kecamatan');

        // 2. Event saat Kota dipilih
        kotaDropdown.addEventListener('change', function() {
            const kotaIdTerpilih = this.value;

            // Kosongkan dropdown kecamatan
            kecamatanDropdown.innerHTML = '<option value="">Semua Kecamatan</option>';
            
            if (kotaIdTerpilih) {
                // 3. FILTER data di memori browser (Tanpa loading ke server)
                // Cari kecamatan yang 'induk_id'-nya sama dengan kota yang dipilih
                const kecamatanTersaring = semuaKecamatan.filter(item => 
                    item.induk_id.value === kotaIdTerpilih
                );

                // Masukkan hasil filter ke dropdown
                kecamatanTersaring.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id_short.value;
                    option.textContent = item.label.value;
                    kecamatanDropdown.appendChild(option);
                });

                kecamatanDropdown.disabled = false;
            } else {
                kecamatanDropdown.innerHTML = '<option value="">Pilih Kota Terlebih Dahulu</option>';
                kecamatanDropdown.disabled = true;
            }
        });
    </script>
</body>
</html>
