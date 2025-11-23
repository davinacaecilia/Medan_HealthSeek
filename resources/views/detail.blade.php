<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rumah Sakit - Medan HealthSeek</title>
    
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">

</head>
<body>
    <main class="container">
        
        <article class="detail-content">
            <header class="detail-header">
                <h1>{{ $rs['nama'] }}</h1>
                <span class="tipe-rs tipe-{{ strtolower($rs['tipe']) }}">{{ $rs['tipe'] }}</span>
            </header>

            <section class="detail-section">
                <h2>Asuransi Diterima</h2>
                <ul class="tag-list">
                    @forelse ($rs['asuransi'] as $item)
                        <li class="tag">{{ $item }}</li>
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
    <footer>
        <p>© {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

</body>
</html>