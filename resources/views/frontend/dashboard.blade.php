<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Saya - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-gray-800">

    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b border-gray-100">
        <a href="{{ route('katalog.index') }}" class="text-xl font-bold text-blue-700">Rental.ly</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('katalog.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600">Kembali ke Katalog</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Logout</button>
            </form>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Halo, {{ $user->name }}!</h2>
            <p class="text-gray-500">Kelola akun, alamat pengiriman, dan riwayat sewa Anda di sini.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <p class="font-bold mb-1">Gagal menyimpan data:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Akun</h3>
                    <p class="text-sm text-gray-500 mb-1">Email</p>
                    <p class="font-medium mb-4">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 mb-1">Status Verifikasi KTP</p>
                    
                    @if(!$verifikasi)
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-md">Belum Diverifikasi</span>
                        <p class="text-xs text-gray-500 mt-2">Anda wajib upload KTP untuk bisa menyewa.</p>
                            <a href="{{ route('ktp.upload') }}" class="mt-3 w-full bg-blue-50 text-blue-700 font-semibold py-2 rounded-lg text-sm block text-center hover:bg-blue-100 transition">Upload KTP Sekarang</a>                    @elseif($verifikasi->status == 'pending')
                        <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-md">Menunggu Pengecekan Admin</span>
                    @elseif($verifikasi->status == 'disetujui')
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-md">Terverifikasi</span>
                    @else
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-md">Verifikasi Ditolak</span>
                        <p class="text-xs text-red-500 mt-1">{{ $verifikasi->catatan }}</p>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Daftar Alamat Pengiriman</h3>
                    
                    @if($alamats->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($alamats as $alamat)
                                <div class="border border-gray-200 p-4 rounded-xl flex justify-between items-start">
                                    <div>
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded mb-2 inline-block">{{ $alamat->label_alamat }}</span>
                                        <p class="text-sm text-gray-600">{{ $alamat->detail_alamat }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-xl mb-6 border border-dashed border-gray-300">
                            <p class="text-gray-500 text-sm">Belum ada alamat yang ditambahkan.</p>
                        </div>
                    @endif

                    <hr class="border-gray-100 mb-6">
                    <h4 class="font-bold text-sm text-gray-900 mb-3">+ Tambah Alamat Baru</h4>
                    <form action="{{ route('alamat.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Label Alamat (cth: Rumah)</label>
                                <input type="text" name="label_alamat" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kode Pos</label>
                                <input type="number" name="kode_pos" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kota / Kabupaten</label>
                                <select id="kota" name="kota_kabupaten" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50" required disabled>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kecamatan</label>
                                <select id="kecamatan" name="kecamatan" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kelurahan / Desa</label>
                                <select id="kelurahan" name="kelurahan" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50" required disabled>
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Detail Jalan & Patokan</label>
                            <textarea name="detail_alamat" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required placeholder="Nama Jalan, RT/RW, Patokan gedung..."></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition shadow-md">Simpan Alamat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const endpoint = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        const selProvinsi = document.getElementById('provinsi');
        const selKota = document.getElementById('kota');
        const selKecamatan = document.getElementById('kecamatan');
        const selKelurahan = document.getElementById('kelurahan');

        // 1. Load Data Provinsi saat halaman dibuka
        fetch(`${endpoint}/provinces.json`)
            .then(response => response.json())
            .then(provinces => {
                provinces.forEach(province => {
                    // Kita simpan ID-nya di data-id untuk nge-fetch data anaknya nanti, 
                    // tapi value yang dikirim ke database tetap nama provinsinya
                    let option = document.createElement('option');
                    option.value = province.name;
                    option.dataset.id = province.id;
                    option.textContent = province.name;
                    selProvinsi.appendChild(option);
                });
            });

        // 2. Saat Provinsi dipilih -> Load Kota
        selProvinsi.addEventListener('change', function() {
            selKota.innerHTML = '<option value="">Memuat...</option>';
            selKecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
            selKelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
            selKota.disabled = true; selKecamatan.disabled = true; selKelurahan.disabled = true;

            const provId = this.options[this.selectedIndex].dataset.id;
            if(!provId) return;

            fetch(`${endpoint}/regencies/${provId}.json`)
                .then(response => response.json())
                .then(regencies => {
                    selKota.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                    selKota.disabled = false;
                    selKota.classList.remove('bg-gray-50');
                    regencies.forEach(regency => {
                        let option = document.createElement('option');
                        option.value = regency.name;
                        option.dataset.id = regency.id;
                        option.textContent = regency.name;
                        selKota.appendChild(option);
                    });
                });
        });

        // 3. Saat Kota dipilih -> Load Kecamatan
        selKota.addEventListener('change', function() {
            selKecamatan.innerHTML = '<option value="">Memuat...</option>';
            selKelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
            selKecamatan.disabled = true; selKelurahan.disabled = true;

            const kotaId = this.options[this.selectedIndex].dataset.id;
            if(!kotaId) return;

            fetch(`${endpoint}/districts/${kotaId}.json`)
                .then(response => response.json())
                .then(districts => {
                    selKecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
                    selKecamatan.disabled = false;
                    selKecamatan.classList.remove('bg-gray-50');
                    districts.forEach(district => {
                        let option = document.createElement('option');
                        option.value = district.name;
                        option.dataset.id = district.id;
                        option.textContent = district.name;
                        selKecamatan.appendChild(option);
                    });
                });
        });

        // 4. Saat Kecamatan dipilih -> Load Kelurahan
        selKecamatan.addEventListener('change', function() {
            selKelurahan.innerHTML = '<option value="">Memuat...</option>';
            selKelurahan.disabled = true;

            const kecId = this.options[this.selectedIndex].dataset.id;
            if(!kecId) return;

            fetch(`${endpoint}/villages/${kecId}.json`)
                .then(response => response.json())
                .then(villages => {
                    selKelurahan.innerHTML = '<option value="">Pilih Kelurahan</option>';
                    selKelurahan.disabled = false;
                    selKelurahan.classList.remove('bg-gray-50');
                    villages.forEach(village => {
                        let option = document.createElement('option');
                        option.value = village.name;
                        option.dataset.id = village.id;
                        option.textContent = village.name;
                        selKelurahan.appendChild(option);
                    });
                });
        });
    </script>
</body>
</html>