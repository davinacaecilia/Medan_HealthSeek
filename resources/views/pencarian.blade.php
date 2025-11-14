<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - Medan HealthSeek</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/pencarian.css') }}">
</head>
<body>

    <!-- Navbar -->
    @include('partial.navbar')

    <!-- Konten Utama -->
    <div class="main-content">
        <!-- Sidebar kiri: Filter -->
        <div class="filter-card">
            <form method="GET" action="{{ route('pencarian') }}">
            <input type="text" name="search" placeholder="Cari rumah sakit..." class="filter-search">

            <label for="tipe">Tipe Rumah Sakit</label>
            <select id="tipe" name="tipe">
                <option value="">Pilih Tipe</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <label for="asuransi">Asuransi</label>
            <select id="asuransi" name="asuransi">
                <option value="">Pilih Asuransi</option>
                <option>BPJS</option>
                <option>Prudential</option>
                <option>Mandiri</option>
                <option>Allianz</option>
            </select>

            <label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan">
                <option value="">Pilih Kecamatan</option>
                <option>Medan Petisah</option>
                <option>Medan Baru</option>
                <option>Medan Johor</option>
                <option>Medan Timur</option>
            </select>

            <label for="spesialis">Spesialis</label>
            <select id="spesialis" name="spesialis">
                <option value="">Pilih Spesialis</option>
                <option>Anak</option>
                <option>Bedah</option>
                <option>Jantung</option>
                <option>Mata</option>
                <option>Umum</option>
            </select>

            <label for="urutkan">Urutkan Berdasarkan</label>
            <select id="urutkan" name="urutkan">
                <option value="">Pilih Urutan</option>
                <option value="nama">Nama</option>
                <option value="tipe">Tipe</option>
            </select>

            <button type="submit" class="btn-filter-cari">Cari</button>
        </form>
        </div>

        <!-- Kanan: Card hasil pencarian -->
        <main class="card-container" id="cardContainer">
        @php
            $rumahSakit = [
                ['nama' => 'Rumah Sakit Umum Medan Sehat', 'tipe' => 'A', 'alamat' => 'Jl. Sisingamangaraja No.45', 'no_hp' => '0812-3456-7890'],
                ['nama' => 'RS Harapan Sehat', 'tipe' => 'B', 'alamat' => 'Jl. Gatot Subroto No.12', 'no_hp' => '0813-2222-8888'],
                ['nama' => 'RS Kasih Ibu', 'tipe' => 'C', 'alamat' => 'Jl. Iskandar Muda No.77', 'no_hp' => '0811-9999-5555'],
                ['nama' => 'RS Mitra Medika', 'tipe' => 'D', 'alamat' => 'Jl. Ayahanda No.68A, Medan Petisah, Medan',
                        'No Handphone', 'no_hp' => '0813-1111-2222'],
                ['nama' => 'RS Mitra Medika', 'tipe' => 'D', 'alamat' => 'Jl. Setia Budi No.33', 'no_hp' => '0813-1111-2222'],
                ['nama' => 'RS Mitra Medika', 'tipe' => 'D', 'alamat' => 'Jl. Setia Budi No.33', 'no_hp' => '0813-1111-2222'],
                ['nama' => 'RS Mitra Medika', 'tipe' => 'D', 'alamat' => 'Jl. Setia Budi No.33', 'no_hp' => '0813-1111-2222'],
            ];

            $filtered = collect($rumahSakit)->filter(function($rs) {
                return request('search')
                    ? str_contains(strtolower($rs['nama']), strtolower(request('search')))
                    : true;
            });
        @endphp

        @if ($filtered->isEmpty())
            <div class="not-found">
                <p>Rumah sakit yang kamu cari tidak ditemukan.</p>
            </div>
        @else
            @foreach ($filtered as $rs)
                <div class="card">
                    <div class="card-image">
                        <img src="https://asset-2.tribunnews.com/medan/foto/bank/images/rs-bunda-thamrin-medan-1.jpg" alt="Gambar Rumah Sakit">
                    </div>

                    <div class="card-header">
                        <h2>{{ $rs['nama'] }}</h2>
                        <span class="tipe tipe-{{ strtolower($rs['tipe']) }}">{{ $rs['tipe'] }}</span>
                    </div>

                    <div class="card-location">
                        <i class='bx bx-map'></i>
                        <span>{{ $rs['alamat'] }}</span>
                    </div>

                    <!-- Tombol detail -->
                    <div class="card-footer">
                        <a href="#" class="btn-detail">Detail</a>
                    </div>
                </div>
            @endforeach
        @endif
        </main>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <button id="prevBtn">Previous</button>
        <div id="pageNumbers"></div>
        <button id="nextBtn">Next</button>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

<script>
    const cards = Array.from(document.querySelectorAll('.card'));
    const cardsPerPage = 4;
    let currentPage = 1;

    function showPage(page) {
        const start = (page - 1) * cardsPerPage;
        const end = start + cardsPerPage;

        cards.forEach((card, index) => {
            card.style.display = (index >= start && index < end) ? 'block' : 'none';
        });

        updatePageNumbers();
        updateButtons();
    }

    function updateButtons() {
        const totalPages = Math.ceil(cards.length / cardsPerPage);
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.style.visibility = (currentPage === 1) ? "hidden" : "visible";
        nextBtn.style.visibility = (currentPage === totalPages) ? "hidden" : "visible";
    }

    function updatePageNumbers() {
        const totalPages = Math.ceil(cards.length / cardsPerPage);
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.classList.add("page-btn");
            if (i === currentPage) btn.classList.add("active-page");

            btn.addEventListener("click", () => {
                currentPage = i;
                showPage(currentPage);
            });
            pageNumbersContainer.appendChild(btn);
        }
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        const totalPages = Math.ceil(cards.length / cardsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    showPage(currentPage);
</script>
</body>
</html>
