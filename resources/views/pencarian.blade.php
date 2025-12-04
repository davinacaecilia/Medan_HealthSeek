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
    @include('partial.navbar')

    <div class="main-content">
        <div class="filter-card">
            <form method="GET" action="{{ route('rumahSakit.list') }}">
            <input type="text" name="q" placeholder="Cari nama atau jenis rumah sakit..." class="filter-search">

            <label for="tipe">Tipe Rumah Sakit</label>
            <select id="tipe_rs" name="tipe_rs">
                <option value="">Semua Tipe</option>
                <option value="A">Tipe A</option>
                <option value="B">Tipe B</option>
                <option value="C">Tipe C</option>
                <option value="D">Tipe D</option>
            </select>

            <label for="asuransi">Asuransi</label>
            <select id="asuransi" name="asuransi">
                <option value="">Semua Asuransi</option>
                    @foreach($asuransiList as $item)
                        <option value="{{ $item['id_short']['value'] }}">
                            {{ $item['label']['value'] }}
                        </option>
                    @endforeach
            </select>

            <label for="spesialisasi">Spesialisasi</label>
            <select id="spesialisasi" name="spesialisasi">
                <option value="">Semua Spesialisasi</option>
                    @foreach($spesialisasiList as $item)
                        <option value="{{ $item['id_short']['value'] }}">
                            {{ $item['label']['value'] }}
                        </option>
                    @endforeach
            </select>

            <label for="kota">Kabupaten / Kota</label>
            <select id="kota" name="kota">
                <option value="">Semua Kabupaten/Kota</option>
                @foreach($kotaList as $item)
                <option value="{{ $item['id_short']['value'] }}">
                    {{ $item['label']['value'] }}
                </option>
                @endforeach
            </select>

            <label for="kecamatan">Kecamatan</label>
            <select id="kecamatan" name="kecamatan" >
                <option value="">Semua Kecamatan</option>
            </select>

            <button type="submit" class="btn-filter-cari">Cari</button>
        </form>
        </div>

        <main class="card-container" id="cardContainer">

        @forelse ($results as $item)
            <div class="card">
                <div class="card-header">
                    <h2>{{ $item['nama']['value'] }}</h2>
                    <span class="tipe tipe-{{ strtolower($item['tipe']['value'] ) }}">{{ $item['tipe']['value'] }}</span>
                </div>

                <div class="card-location">
                    <i class='bx bx-map'></i>
                    <span>{{ $item['nama_kecamatan']['value'] }}, {{ $item['nama_kota']['value'] }}</span>
                </div>

                <!-- TELEPON -->
                <div class="card-phone">
                    <i class='bx bx-phone'></i>
                    <span>{{ $item['noTelp']['value'] }}</span>
                </div>

                <!-- Tombol detail (tengah) -->
                <div class="card-footer-center">
                    <a href="{{ route('rumahSakit.detail', ['id' =>$item['id']['value'] ]) }}" class="btn-detail">Detail</a>
                </div>
            </div>
        @empty
            <div class="not-found">
                <p>Tidak ada data rumah sakit ditemukan.</p>
            </div>
        @endforelse

        </main>
    </div>

    <div class="pagination">
        <button id="prevBtn">Previous</button>
        <div id="pageNumbers"></div>
        <button id="nextBtn">Next</button>
    </div>

    @include('partials.chatbot')

    <footer>
        <p>© {{ date('Y') }} HealthSeek. All Rights Reserved.</p>
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
            }
        });
    </script>

    <script>
        const cards = Array.from(document.querySelectorAll('.card'));
        const cardsPerPage = 12;
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
