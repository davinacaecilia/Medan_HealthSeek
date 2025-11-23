<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rumah Sakit - Medan HealthSeek</title>
    
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
<!--     
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">  -->
</head>
<body>

    @include('partials.navbar')

    <main class="container">
        
        <article class="detail-content">
            
            <!-- bagian gambar yang dihapus -->

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
                        src="{{ $rs['link_gmaps'] ?? '' }}" 
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