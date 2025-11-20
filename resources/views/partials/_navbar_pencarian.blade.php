
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

         <button onclick="window.history.back()" class="btn-beranda">Kembali</button>
    </nav>