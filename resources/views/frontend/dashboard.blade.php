<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Saya - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-slate-50 text-gray-800 font-sans antialiased">

    <nav class="bg-white shadow-sm py-4 px-6 md:px-10 flex justify-between items-center border-b border-gray-200 sticky top-0 z-50">
        <a href="{{ route('katalog.index') }}" class="text-2xl font-black text-blue-700 tracking-tight">Rental<span class="text-blue-400">.ly</span></a>
        <div class="flex items-center gap-6">
            <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Kembali ke Katalog</a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-sm font-bold bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 px-4 py-2 rounded-lg transition">Logout</button>
            </form>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-8 md:flex md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Halo, {{ $user->name }}! 👋</h2>
                <p class="text-gray-500 mt-2 text-sm md:text-base">Kelola akun, alamat pengiriman, dan pantau riwayat sewa Anda di sini.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl mb-8 flex items-start gap-3 shadow-sm">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="font-medium mt-0.5">{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-8 flex items-start gap-3 shadow-sm">
                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold mb-1">Gagal memproses data:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white p-7 rounded-3xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                        <div class="bg-blue-100 p-2.5 rounded-full text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Informasi Akun</h3>
                    </div>
                    
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email Terdaftar</p>
                        <p class="font-medium text-gray-800">{{ $user->email }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Status Identitas (KTP)</p>
                        
                        @if(!$verifikasi)
                            <div class="bg-red-50 border border-red-100 p-4 rounded-2xl">
                                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full mb-2">Belum Verifikasi</span>
                                <p class="text-xs text-gray-600 mb-4 leading-relaxed">Anda wajib mengunggah KTP sebelum dapat menyewa barang.</p>
                                <a href="{{ route('ktp.upload') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm block text-center transition shadow-md shadow-blue-600/20">Upload KTP Sekarang</a>
                            </div>
                        @elseif($verifikasi->status == 'pending')
                            <div class="bg-yellow-50 border border-yellow-100 p-4 rounded-2xl flex items-start gap-3">
                                <div>
                                    <span class="text-yellow-800 text-sm font-bold block mb-1">Diproses Admin</span>
                                    <span class="text-xs text-yellow-700 leading-relaxed">Dokumen sedang dicek, mohon tunggu sebentar.</span>
                                </div>
                            </div>
                        @elseif($verifikasi->status == 'disetujui')
                            <div class="bg-green-50 border border-green-100 p-4 rounded-2xl flex items-center gap-3">
                                <div>
                                    <span class="text-green-800 text-sm font-bold block">Terverifikasi</span>
                                    <span class="text-xs text-green-600">Akun siap digunakan.</span>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-100 p-4 rounded-2xl">
                                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full mb-2">Ditolak</span>
                                <p class="text-xs text-red-600 font-medium mb-3">Alasan: {{ $verifikasi->catatan }}</p>
                                <a href="{{ route('ktp.upload') }}" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-2 rounded-xl text-sm block text-center transition">Upload Ulang</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white p-7 md:p-9 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Riwayat Pesanan</h2>

                    @if(isset($peminjamans) && $peminjamans->count() > 0)
                        <div class="space-y-5">
                            @foreach($peminjamans as $pinjam)
                                @php
                                    // Ambil data pembayaran DP/Deposit yang mengandung Token Midtrans
                                    $tagihanAwal = $pinjam->pembayaran->where('jenis_pembayaran', 'tagihan_awal')->first();
                                @endphp
                                
                                <div class="border border-gray-100 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 hover:shadow-md transition duration-300 bg-white">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">ID: #{{ str_pad($pinjam->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-xs font-medium text-gray-500">{{ \Carbon\Carbon::parse($pinjam->tanggal_pesan)->format('d M Y') }}</span>
                                        </div>
                                        
                                        <ul class="space-y-1.5 mb-4">
                                            @foreach($pinjam->detail_peminjaman as $detail)
                                                @php
                                                    $namaBarang = $detail->unit_barang->katalog_barang->nama_barang ?? 'Barang tidak diketahui';
                                                    $durasi = \Carbon\Carbon::parse($pinjam->tanggal_pesan)->diffInDays(\Carbon\Carbon::parse($pinjam->tanggal_kembali_rencana));
                                                @endphp
                                                <li class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    {{ $namaBarang }} <span class="text-gray-400 font-normal">({{ $durasi }} Hari)</span>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <p class="font-bold text-blue-600 text-lg">Rp {{ number_format($pinjam->jumlah_dp + $pinjam->jumlah_deposit, 0, ',', '.') }}</p>
                                    </div>

                                    <div class="w-full md:w-auto flex flex-col items-start md:items-end border-t md:border-t-0 pt-4 md:pt-0 border-gray-100">
                                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold mb-4
                                            {{ $pinjam->status_peminjaman == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $pinjam->status_peminjaman == 'disetujui' || $pinjam->status_peminjaman == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $pinjam->status_peminjaman == 'aktif' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $pinjam->status_peminjaman == 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                            {{ strtoupper($pinjam->status_peminjaman) }}
                                        </span>
                                        
                                        @if($pinjam->status_peminjaman == 'pending' && $tagihanAwal && $tagihanAwal->snap_token)
                                            <button onclick="payWithMidtrans('{{ $tagihanAwal->snap_token }}')" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition shadow-md shadow-blue-600/20">
                                                Bayar Sekarang
                                            </button>
                                        @else
                                            <a href="#" class="text-sm font-bold text-gray-600 hover:text-blue-600 bg-gray-100 hover:bg-blue-50 px-5 py-2.5 rounded-xl transition">Detail Pesanan</a>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 px-4 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50">
                            <p class="text-gray-900 font-bold mb-1">Belum ada pesanan</p>
                            <p class="text-sm text-gray-500 mb-5">Yuk jelajahi katalog dan mulai menyewa barang incaranmu!</p>
                            <a href="{{ route('katalog.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition">Lihat Katalog</a>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-7 md:p-9 rounded-3xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                        <div class="bg-emerald-100 p-2 rounded-xl text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Daftar Alamat Pengiriman</h3>
                    </div>
                    
                    @if($alamats->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            @foreach($alamats as $alamat)
                                <div class="border border-gray-200 p-5 rounded-2xl relative hover:border-blue-300 transition group bg-white">
                                    <span class="absolute top-4 right-4 bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md">{{ $alamat->label_alamat }}</span>
                                    <svg class="w-6 h-6 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    <p class="text-sm text-gray-600 leading-relaxed pr-10">{{ $alamat->detail_alamat }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-2xl mb-8 border border-dashed border-gray-300">
                            <p class="text-gray-500 text-sm">Belum ada alamat pengiriman yang disimpan.</p>
                        </div>
                    @endif

                    <div class="bg-slate-50 p-6 rounded-2xl border border-gray-100">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Alamat Baru
                        </h4>
                        <form action="{{ route('alamat.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Label Alamat</label>
                                    <input type="text" name="label_alamat" placeholder="Cth: Rumah, Kantor" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kode Pos</label>
                                    <input type="number" name="kode_pos" placeholder="Cth: 12345" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Provinsi</label>
                                    <select id="provinsi" name="provinsi" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kota / Kabupaten</label>
                                    <select id="kota" name="kota_kabupaten" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kecamatan</label>
                                    <select id="kecamatan" name="kecamatan" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kelurahan</label>
                                    <select id="kelurahan" name="kelurahan" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Detail Jalan & Patokan</label>
                                <textarea name="detail_alamat" rows="3" class="w-full text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm" required placeholder="Nama Jalan, RT/RW, Patokan gedung..."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-3 px-8 rounded-xl text-sm transition shadow-md w-full md:w-auto">Simpan Alamat Baru</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // --- 1. SCRIPT MIDTRANS ---
        function payWithMidtrans(snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function(result){
                    alert("Pembayaran berhasil diproses Midtrans!"); 
                    window.location.reload(); 
                },
                onPending: function(result){
                    alert("Silakan selesaikan pembayaran Anda."); 
                    window.location.reload();
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                },
                onClose: function(){
                    alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                }
            });
        }

        // --- 2. SCRIPT API ALAMAT EMSIFA ---
        const endpoint = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        const selProvinsi = document.getElementById('provinsi');
        const selKota = document.getElementById('kota');
        const selKecamatan = document.getElementById('kecamatan');
        const selKelurahan = document.getElementById('kelurahan');

        fetch(`${endpoint}/provinces.json`)
            .then(response => response.json())
            .then(provinces => {
                provinces.forEach(province => {
                    let option = document.createElement('option');
                    option.value = province.name;
                    option.dataset.id = province.id;
                    option.textContent = province.name;
                    selProvinsi.appendChild(option);
                });
            });

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
                    regencies.forEach(regency => {
                        let option = document.createElement('option');
                        option.value = regency.name;
                        option.dataset.id = regency.id;
                        option.textContent = regency.name;
                        selKota.appendChild(option);
                    });
                });
        });

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
                    districts.forEach(district => {
                        let option = document.createElement('option');
                        option.value = district.name;
                        option.dataset.id = district.id;
                        option.textContent = district.name;
                        selKecamatan.appendChild(option);
                    });
                });
        });

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