<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medan HealthSeek</title>
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
                <!-- <div class="filter-row">
                    <select name="tipe">
                        <option value="">Tipe RS</option>
                        <option value="A">Tipe A</option>
                        <option value="B">Tipe B</option>
                        <option value="C">Tipe C</option>
                        <option value="D">Tipe D</option>
                    </select>

                    <select id="asuransi" name="asuransi">
                        <option value="">Asuransi</option>
                        @foreach($asuransiList as $item)
                            <option value="{{ $item['id_short']['value'] }}">
                                {{ $item['label']['value'] }}
                            </option>
                        @endforeach
                    </select>

                    <select id="kecamatan" name="kecamatan">
                        <option value="">Kecamatan</option>
                        @foreach($kecamatanList as $item)
                            <option value="{{ $item['id_short']['value'] }}">
                                {{ $item['label']['value'] }}
                            </option>
                        @endforeach
                    </select>

                    <select id="spesialisasi" name="spesialisasi">
                        <option value="">Spesialisasi</option>
                        @foreach($spesialisasiList as $item)
                            <option value="{{ $item['id_short']['value'] }}">
                                {{ $item['label']['value'] }}
                            </option>
                        @endforeach
                    </select>
                </div> -->
            </form>
        </div>
    </header>

    <main>
        <div class="card-container">
            @php
                $limitedRS = array_slice($rumahSakit, 0, 3);
            @endphp

            @forelse ($limitedRS as $rs)
                <a href="{{ route('rumahSakit.detail', ['id' => $rs['id']]) }}" class="card-link">
                    <div class="card">

                    <!-- Gambar Rumah Sakit -->
                    <div class="card-image">
                        <img src="https://asset-2.tribunnews.com/medan/foto/bank/images/rs-bunda-thamrin-medan-1.jpg" alt="Gambar Rumah Sakit">
                    </div>

                        <div class="card-header">
                            <h2>{{ $rs['nama'] }}</h2>
                            <span class="tipe-rs tipe-{{ strtolower($rs['tipe'] ?? 'a') }}">{{ $rs['tipe'] ?? 'A' }}</span>
                        </div>
                        <p><strong>Alamat:</strong> {{ $rs['alamat'] }}</p>
                        <p><strong>No. Handphone:</strong> {{ $rs['No Handphone'] }}</p>
                    </div>
                </a>
            @empty
                <div class="not-found">
                    <p>Tidak ada data rumah sakit ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if (count($rumahSakit) >= 1)
            <div class="see-more">
                <a href="{{ route('pencarian') }}" class="btn-see-more">See More →</a>
            </div>
        @endif
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

</body>
</html>
