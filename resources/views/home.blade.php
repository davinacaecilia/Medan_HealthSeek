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
            <a href="{{ route('home') }}" class="nav-link active">Beranda</a>
        </nav>

        <div class="hero-content">
            <h1>Medan HealthSeek</h1>
            <p class="subtitle">Temukan rumah sakit terbaik di Medan dengan cepat dan mudah</p>

                <!-- Baris pencarian -->
                <form method="GET" action="{{ route('home') }}" class="search-box">
                <div class="searching">
                    <input type="text" id="search" name="search" placeholder="Masukkan nama rumah sakit..." value="{{ $keyword ?? '' }}">
                    <button type="submit">Cari</button>
                </div>
            <!-- FILTER BAR -->
            <div class="filter-top">
                <select name="tipe">
                    <option value="">Tipe RS</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <select name="asuransi">
                    <option value="">Asuransi</option>
                    <option>BPJS Kesehatan</option>
                    <option>AIA Finance</option>
                    <option>Allianz</option>
                    <option>AXA Mandiri</option>
                    <option>BNI Life</option>
                    <option>Chubb Life</option>
                    <option>Great Eastern Life</option>
                    <option>Manulife</option>
                    <option>Prudential</option>
                    <option>Sinarmas MSIG Life</option>
                </select>

                <select name="spesialis">
                    <option value="">Spesialis</option>
                    <option>Anak</option>
                    <option>Jantung</option>
                    <option>THT</option>
                    <option>Mata</option>
                    <option>Saraf</option>
                    <option>Penyakit Dalam</option>
                    <option>Bedah Umum</option>
                    <option>Kandungan</option>
                    <option>Psikiatri</option>
                </select>
            </div>
                    <div class="filter-bottom">

            <!-- Kabupaten/Kota -->
            <select id="kabupaten" name="kabupaten">
                <option value="">Kabupaten/Kota</option>
                   <!-- KOTA -->
        <option value="Kota Medan">Kota Medan</option>
        <option value="Kota Binjai">Kota Binjai</option>
        <option value="Kota Tebing Tinggi">Kota Tebing Tinggi</option>
        <option value="Kota Pematang Siantar">Kota Pematang Siantar</option>
        <option value="Kota Tanjung Balai">Kota Tanjung Balai</option>
        <option value="Kota Padangsidimpuan">Kota Padangsidimpuan</option>
        <option value="Kota Gunungsitoli">Kota Gunungsitoli</option>
        <option value="Kota Sibolga">Kota Sibolga</option>

        <!-- KABUPATEN -->
        <option value="Kabupaten Asahan">Kabupaten Asahan</option>
        <option value="Kabupaten Batu Bara">Kabupaten Batu Bara</option>
        <option value="Kabupaten Dairi">Kabupaten Dairi</option>
        <option value="Kabupaten Deli Serdang">Kabupaten Deli Serdang</option>
        <option value="Kabupaten Humbang Hasundutan">Kabupaten Humbang Hasundutan</option>
        <option value="Kabupaten Karo">Kabupaten Karo</option>
        <option value="Kabupaten Labuhanbatu">Kabupaten Labuhanbatu</option>
        <option value="Kabupaten Labuhanbatu Selatan">Kabupaten Labuhanbatu Selatan</option>
        <option value="Kabupaten Labuhanbatu Utara">Kabupaten Labuhanbatu Utara</option>
        <option value="Kabupaten Langkat">Kabupaten Langkat</option>
        <option value="Kabupaten Mandailing Natal">Kabupaten Mandailing Natal</option>
        <option value="Kabupaten Nias">Kabupaten Nias</option>
        <option value="Kabupaten Nias Barat">Kabupaten Nias Barat</option>
        <option value="Kabupaten Nias Selatan">Kabupaten Nias Selatan</option>
        <option value="Kabupaten Nias Utara">Kabupaten Nias Utara</option>
        <option value="Kabupaten Padang Lawas">Kabupaten Padang Lawas</option>
        <option value="Kabupaten Padang Lawas Utara">Kabupaten Padang Lawas Utara</option>
        <option value="Kabupaten Pakpak Bharat">Kabupaten Pakpak Bharat</option>
        <option value="Kabupaten Samosir">Kabupaten Samosir</option>
        <option value="Kabupaten Serdang Bedagai">Kabupaten Serdang Bedagai</option>
        <option value="Kabupaten Simalungun">Kabupaten Simalungun</option>
        <option value="Kabupaten Tapanuli Selatan">Kabupaten Tapanuli Selatan</option>
        <option value="Kabupaten Tapanuli Tengah">Kabupaten Tapanuli Tengah</option>
        <option value="Kabupaten Tapanuli Utara">Kabupaten Tapanuli Utara</option>
        <option value="Kabupaten Toba">Kabupaten Toba</option>
            </select>

            <!-- Kecamatan (akan dinamis sesuai kabupaten) -->
            <select id="kecamatan" name="kecamatan">
                <option value="">Kecamatan</option>
            </select>
        </div>
        </header>
            <script>
            fetch("{{ asset('data/sumut.json') }}")
                .then(res => res.json())
                .then(data => {
                    const kab = document.getElementById("kabupaten");
                    const kec = document.getElementById("kecamatan");

                    // Isi dropdown kabupaten
                    kab.innerHTML = `<option value="">-- Pilih Kabupaten/Kota --</option>`;
                    Object.keys(data).forEach(k => {
                        kab.innerHTML += `<option value="${k}">${k}</option>`;
                    });

                    // Jika kabupaten berubah → isi kecamatan sesuai JSON
                    kab.addEventListener("change", function () {
                        kec.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
                        if (data[this.value]) {
                            data[this.value].forEach(namaKec => {
                                kec.innerHTML += `<option value="${namaKec}">${namaKec}</option>`;
                            });
                        }
                    });
                });
            </script>


    <!-- ===== MAIN CONTENT ===== -->
<main class="card-container">
        @php
            $limitedRS = array_slice($rumahSakit, 0, 3);
        @endphp

        @forelse ($limitedRS as $rs)
            <div class="card">

                <div class="card-header">
                    <h2>{{ $rs['nama'] }}</h2>
                    <span class="tipe tipe-{{ strtolower($rs['tipe'] ?? 'a') }}">{{ $rs['tipe'] ?? 'A' }}</span>
                </div>

                <div class="card-location">
                    <i class='bx bx-map'></i>
                    <span>{{ $rs['alamat'] }}</span>
                </div>

                <div class="card-phone">
                    <i class='bx bx-phone'></i>
                    <span>{{ $rs['No Handphone'] }}</span>
                </div>

                <div class="card-footer-center">
                    <a href="#" class="btn-detail">Detail</a>
                </div>
            </div>
        @empty
            <div class="not-found">
                <p>Tidak ada data rumah sakit ditemukan.</p>
            </div>
        @endforelse
    </main>

        @if (count($rumahSakit) >= 1)
            <div class="see-more">
                <a href="{{ route('pencarian') }}" class="btn-see-more">See More →</a>
            </div>
        @endif

    <footer>
        <p>&copy; {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

</body>
</html>
