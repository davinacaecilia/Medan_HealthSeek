<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - Medan HealthSeek</title>
    <link rel="stylesheet" href="{{ asset('css/pencarian.css') }}">
</head>
<body>

    <nav class="navbar">

    <div class="floating-icons">
            <span class="icon">💊</span>
            <span class="icon">🩺</span>
            <span class="icon">💉</span>
            <span class="icon">🏥</span>
        </div>

        <form class="nav-form" method="GET" action="{{ route('pencarian') }}">
            <input type="text" name="search" placeholder="Cari rumah sakit..." class="search-input">

             <button type="submit" class="btn-cari">Cari</button>

            <select name="tipe">
                <option value="">Tipe RS</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <select name="asuransi">
                <option value="">Asuransi</option>
                <option>BPJS</option>
                <option>Prudential</option>
                <option>Mandiri</option>
                <option>Allianz</option>
            </select>

            <select name="kecamatan">
                <option value="">Kecamatan</option>
                <option>Medan Petisah</option>
                <option>Medan Baru</option>
                <option>Medan Johor</option>
                <option>Medan Timur</option>
            </select>

            <select name="spesialis">
                <option value="">Spesialis</option>
                <option>Anak</option>
                <option>Bedah</option>
                <option>Jantung</option>
                <option>Mata</option>
                <option>Umum</option>
            </select>
        </form>

         <a href="{{ route('home') }}" class="btn-beranda">Beranda</a>

    </nav>


    <main class="card-container" id="cardContainer">
    @php
        // ==========================================================
        // PERUBAHAN DI SINI: Data dummy ini disamakan dengan Controller
        // ==========================================================
        $rumahSakit = [
            [
                'id' => 1,
                'nama' => 'RSU Royal Prima',
                'alamat' => 'Jl. Ayahanda No. 68, ...',
                'no_hp' => '0812-3456-7890',
                'tipe' => 'A'
            ],
            [
                'id' => 2,
                'nama' => 'RS Columbia Asia Medan',
                'alamat' => 'Jl. Listrik No. 2, ...',
                'no_hp' => '4566 368',
                'tipe' => 'B'
            ],
            [
                'id' => 3,
                'nama' => 'RS Hermina Medan',
                'alamat' => 'Jl. Asrama No. 34, ...',
                'no_hp' => '80862525',
                'tipe' => 'C'
            ],
        ];

        // Filter ini akan memfilter $rumahSakit yang dikirim dari Controller
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
            <a href="{{ route('rumahSakit.detail', ['id' => $rs['id']]) }}" class="card-link">
                <div class="card">

                <div class="card-image">
                    <img src="https://asset-2.tribunnews.com/medan/foto/bank/images/rs-bunda-thamrin-medan-1.jpg" alt="Gambar Rumah Sakit">
                </div>

                    <div class="card-header">
                        <h2>{{ $rs['nama'] }}</h2>
                        <span class="tipe tipe-{{ strtolower($rs['tipe']) }}">{{ $rs['tipe'] }}</span>
                    </div>
                    <p><strong>Alamat:</strong> {{ $rs['alamat'] }}</p>
                    <p><strong>No. HP:</strong> {{ $rs['no_hp'] }}</p>
                </div>
            </a>
        @endforeach
    @endif
</main>

<div class="pagination">
    <button id="prevBtn">Previous</button>
    <div id="pageNumbers"></div>
    <button id="nextBtn">Next</button>
</div>

<footer>
        <p>© {{ date('Y') }} Medan HealthSeek. All Rights Reserved.</p>
    </footer>

<script>
    // Script pagination ini sudah benar, tidak perlu diubah
    const cardLinks = Array.from(document.querySelectorAll('.card-link'));
    const cardsPerPage = 2;
    let currentPage = 1;

    function showPage(page) {
        const start = (page - 1) * cardsPerPage;
        const end = start + cardsPerPage;

        cardLinks.forEach((cardLink, index) => {
            cardLink.style.display = (index >= start && index < end) ? 'block' : 'none';
        });

        updatePageNumbers();
        updateButtons();
    }

    function updateButtons() {
        const totalPages = Math.ceil(cardLinks.length / cardsPerPage);
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (currentPage === 1) {
            prevBtn.style.visibility = "hidden";
        } else {
            prevBtn.style.visibility = "visible";
        }

        if (currentPage === totalPages) {
            nextBtn.style.visibility = "hidden";
        } else {
            nextBtn.style.visibility = "visible";
        }
    }

    function updatePageNumbers() {
        const totalPages = Math.ceil(cardLinks.length / cardsPerPage);
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.classList.add("page-btn");

            if (i === currentPage) {
                btn.classList.add("active-page");
            }

            btn.addEventListener("click", () => {
                currentPage = i;
                showPage(currentPage);
            });

            pageNumbersContainer.appendChild(btn);
        }
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        const totalPages = Math.ceil(cardLinks.length / cardsPerPage);
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