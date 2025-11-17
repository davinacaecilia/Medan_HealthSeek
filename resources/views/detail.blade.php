<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rumah Sakit - Medan HealthSeek</title>
    
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar_pencarian.css') }}">
     <link rel="stylesheet" href="{{ asset('css/home.css') }}">

</head>
<body>
@include('partials._navbar_pencarian')
    <main class="container">
        
        <article class="detail-content">
            
            <figure class="detail-image-container">
                <img src="https://asset-2.tribunnews.com/medan/foto/bank/images/rs-bunda-thamrin-medan-1.jpg" alt="Gambar Rumah Sakit">
                <figcaption>Tampak depan rumah sakit</figcaption>
            </figure>

            <header class="detail-header">
                <h1>{{ $rs['nama'] }}</h1>
                <span class="tipe-rs tipe-{{ strtolower($rs['tipe']) }}">{{ $rs['tipe'] }}</span>
            </header>

            <section class="detail-section">
                <h2>Fasilitas</h2>
                <ul class="tag-list">
                    @php
                        $fasilitasList = (isset($rs['Fasilitas']) && $rs['Fasilitas'] != '') ? explode(',', $rs['Fasilitas']) : [];
                    @endphp
                    @forelse($fasilitasList as $fasilitas)
                        <li class="tag">{{ trim(str_replace('_', ' ', $fasilitas)) }}</li>
                    @empty
                        <li class="tag">Informasi tidak tersedia</li>
                    @endforelse
                </ul>
            </section>

            <section class="detail-section">
                <h2>Spesialis Unggulan</h2>
                <ul class="tag-list">
                    @php
                        $spesialisList = (isset($rs['Spesialis']) && $rs['Spesialis'] != '') ? explode(',', $rs['Spesialis']) : [];
                    @endphp
                    @forelse($spesialisList as $spesialis)
                        <li class="tag">{{ trim($spesialis) }}</li>
                    @empty
                        <li class="tag">Informasi tidak tersedia</li>
                    @endforelse
                </ul>
            </section>

            <section class="detail-section">
                <h2>Asuransi yang Diterima</h2>
                <ul class="tag-list">
                     @php
                        $asuransiList = (isset($rs['Asuransi']) && $rs['Asuransi'] != '') ? explode(',', $rs['Asuransi']) : [];
                    @endphp
                    @forelse($asuransiList as $asuransi)
                        <li class="tag">{{ trim(str_replace('_', ' ', $asuransi)) }}</li>
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
                {{ $rs['No Handphone'] }}</p>
                
                <p><strong>Jenis RS:</strong><br>
                {{ $rs['Jenis'] ?? 'Informasi tidak tersedia' }}
                </p>

                @if(isset($rs['website']) && $rs['website'] != '')
                <p><strong>Website:</strong><br>
                <a href="{{ $rs['website'] }}" target="_blank" class="website-link">{{ $rs['website'] }}</a></p>
                @endif
            </div>

            <div class="map-box">
                <h3>Lokasi di Peta</h3>
                <div class="gmaps-embed">
                    <iframe 
                        src="{{ $rs['link_gmaps'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15929.61058474246!2d98.66699197992446!3d3.593764024320735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303131c9a59ef835%3A0x69f21f1f727c6314!2sRSUD%20Dr.%20Pirngadi%20Kota%20Medan!5e0!3m2!1sid!2sid!4v1678888888888!5m2!1sid!2sid' }}" 
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