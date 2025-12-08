document.addEventListener('DOMContentLoaded', function() {
    
    // Ambil elemen-elemen HTML
    const btnCari = document.getElementById('btnCari');
    const loading = document.getElementById('loading');
    const listRS = document.getElementById('listRS');
    const judulList = document.getElementById('judulList');
    const mapFrame = document.getElementById('gmap_canvas');
    const radiusInput = document.getElementById('radius');

    // Event Listener Tombol Cari
    if (btnCari) {
        btnCari.addEventListener('click', function () {
            // Cek support Geolocation
            if (!navigator.geolocation) {
                alert("Browser kamu tidak mendukung fitur lokasi.");
                return;
            }

            // Tampilkan Loading
            loading.style.display = 'block';
            loading.innerHTML = "Melacak lokasi & menghitung rute... <br><small>(Mengambil data jalan raya via OSRM)</small>";
            listRS.style.display = 'none';
            judulList.style.display = 'none';

            // Ambil Lokasi User (High Accuracy)
            navigator.geolocation.getCurrentPosition(successLocation, errorLocation, {
                enableHighAccuracy: true
            });
        });
    }

    function successLocation(position) {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;
        
        // Update Map jadi Lokasi User
        mapFrame.src = `https://maps.google.com/maps?q=${userLat},${userLng}&z=14&output=embed`;

        // Panggil fungsi hitung (Async)
        hitungDanTampilkan(userLat, userLng);
    }

    function errorLocation() {
        loading.style.display = 'none';
        alert("Gagal mengambil lokasi. Pastikan GPS aktif.");
    }

    // --- LOGIKA UTAMA (HYBRID: HAVERSINE + OSRM) ---
    async function hitungDanTampilkan(userLat, userLng) {
        let radiusVal = parseFloat(radiusInput.value);
        
        if (typeof window.allHospitals === 'undefined') {
            console.error("Data Rumah Sakit tidak ditemukan!");
            loading.style.display = 'none';
            return;
        }

        // TAHAP 1: Hitung Jarak Kasar (Garis Lurus) untuk SEMUA data
        // Tujuannya untuk menyaring mana yang "kira-kira" dekat
        let preCalculated = window.allHospitals.map(rs => {
            let dist = getDistanceFromLatLonInKm(userLat, userLng, rs.lat, rs.lng);
            return { 
                ...rs, 
                jarak: parseFloat(dist), 
                jarakTipe: 'Garis Lurus' // Status awal
            };
        });

        // TAHAP 2: Urutkan dari yang terdekat (berdasarkan garis lurus)
        preCalculated.sort((a, b) => a.jarak - b.jarak);

        // TAHAP 3: Ambil 15 Kandidat Teratas untuk di-detailkan pakai OSRM
        // Sisanya biarkan pakai jarak garis lurus agar loading cepat
        let topCandidates = preCalculated.slice(0, 15); 
        let remaining = preCalculated.slice(15);

        // TAHAP 4: Request OSRM hanya untuk 15 RS terdekat (Parallel Fetch)
        const promises = topCandidates.map(async (rs) => {
            try {
                // URL OSRM (Gratis)
                const url = `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${rs.lng},${rs.lat}?overview=false`;
                
                // Tambahkan timeout 3 detik (biar gak nunggu kelamaan kalau server lemot)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 3000);

                const response = await fetch(url, { signal: controller.signal });
                clearTimeout(timeoutId);
                
                const data = await response.json();

                if (data.code === 'Ok') {
                    // OSRM return meter, bagi 1000 jadi KM
                    return { 
                        ...rs, 
                        jarak: (data.routes[0].distance / 1000).toFixed(2), 
                        jarakTipe: 'Jalan Raya'
                    };
                }
            } catch (error) {
                // Kalau timeout/error, biarkan data lama (garis lurus)
                return { 
                    ...rs, 
                    jarak: rs.jarak.toFixed(2) 
                }; 
            }
            return { ...rs, jarak: rs.jarak.toFixed(2) };
        });

        // Tunggu 15 request selesai
        const refinedTop = await Promise.all(promises);

        // Gabungkan kembali: [15 Terdekat (Akurat)] + [Sisanya (Estimasi)]
        let finalData = [...refinedTop, ...remaining];

        // Format angka jarak untuk sisa data (yg tidak kena OSRM)
        finalData.forEach(item => {
            if (typeof item.jarak === 'number') item.jarak = item.jarak.toFixed(2);
        });

        // TAHAP 5: Filter Radius (Jika diisi user)
        let filtered = [];
        if (!isNaN(radiusVal) && radiusVal > 0) {
            filtered = finalData.filter(item => parseFloat(item.jarak) <= radiusVal);
        } else {
            filtered = finalData;
        }

        // Sort ulang untuk memastikan urutan benar setelah update OSRM
        filtered.sort((a, b) => parseFloat(a.jarak) - parseFloat(b.jarak));

        // Render
        renderList(filtered, radiusVal);
    }

    // Rumus Haversine (Matematika Garis Lurus - Cadangan)
    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        var R = 6371; 
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function renderList(data, radius) {
        loading.style.display = 'none';
        judulList.style.display = 'block';
        listRS.style.display = 'block';
        listRS.innerHTML = "";

        if (data.length === 0) {
            listRS.innerHTML = `<p style="color:#dc3545; font-weight:500; text-align:center;">
                Tidak ada RS ditemukan dalam radius ${radius} km.
            </p>`;
            return;
        }

        data.forEach(item => {
            let urlDetail = `/search/detail/${item.id}`;

            // Warna ikon & teks: Hijau (Akurat/OSRM), Orange (Estimasi/Haversine)
            let iconColor = item.jarakTipe === 'Jalan Raya' ? '#27ae60' : '#e67e22';

            listRS.innerHTML += `
                <div class="rs-item">
                    <div class="rs-row">
                        <div class="rs-nama">${item.nama}</div>
                        <div class="rs-right">
                            <div class="rs-tipe">${item.tipe}</div>
                            <div class="rs-telp">${item.telp}</div>
                            <div class="rs-jarak" style="color: ${iconColor}; font-weight:bold;">
                                <i class='bx bx-car'></i> ${item.jarak} km
                                <div style="font-size: 0.7rem; color: #777; font-weight:normal;">(${item.jarakTipe})</div>
                            </div>
                        </div>
                        <div class="rs-detail-wrapper">
                            <a href="${urlDetail}" class="btn-detail">Detail</a>
                        </div>
                    </div>
                </div>
            `;
        });
    }
});