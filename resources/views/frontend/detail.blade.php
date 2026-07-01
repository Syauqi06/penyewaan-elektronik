<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $katalog->nama_barang }} - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b border-gray-100">
        <h1 class="text-xl font-bold text-blue-700">Rental.ly</h1>
        <div class="text-sm font-medium space-x-6 text-gray-600 hidden md:inline-flex">
            <a href="{{ route('katalog.index') }}" class="hover:text-blue-600">Katalog</a>
            <a href="#" class="hover:text-blue-600">Bantuan</a>
        </div>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium mr-4">Masuk</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Daftar</a>
            @endauth
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-sm text-gray-500 mb-6">
            <a href="{{ route('katalog.index') }}" class="hover:underline">Beranda</a> &rsaquo; 
            <span class="text-gray-900 font-medium">{{ $katalog->nama_barang }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-100 rounded-2xl aspect-video flex items-center justify-center overflow-hidden relative shadow-sm">
                    <span class="absolute top-4 left-4 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        Stok: {{ $katalog->stok_tersedia }} Unit
                    </span>
                    @if($katalog->foto_barang)
                        <img src="{{ asset('storage/' . $katalog->foto_barang) }}" alt="{{ $katalog->nama_barang }}" class="w-full h-full object-contain p-8">
                    @else
                        <span class="text-gray-400 font-medium">Tanpa Gambar</span>
                    @endif
                </div>

                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Produk</h2>
                    <div class="text-gray-600 leading-relaxed text-sm">
                        {!! nl2br(e($katalog->deskripsi)) !!}
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 sticky top-8">
                <div class="bg-white rounded-2xl shadow-lg shadow-blue-900/5 border border-gray-100 p-6">
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-2">{{ $katalog->nama_barang }}</h1>
                    
                    <div class="flex items-end justify-between border-b border-gray-100 pb-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Sewa Harian</p>
                            <p class="text-2xl font-bold text-blue-600" id="hargaPerHari" data-harga="{{ $katalog->harga_sewa_per_hari }}" data-harga-asli="{{ $katalog->harga_asli }}">
                                Rp {{ number_format($katalog->harga_sewa_per_hari, 0, ',', '.') }}<span class="text-sm text-gray-500 font-normal">/hari</span>
                            </p>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-bold shadow-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('booking.init', $katalog->id) }}" method="POST">
                        @csrf
                        <p class="text-sm font-semibold text-gray-900 mb-2">Durasi Sewa</p>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tanggal Mulai</label>
                                <input type="date" id="tgl_pesan" name="tgl_pesan" class="w-full text-sm border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tanggal Selesai</label>
                                <input type="date" id="tgl_kembali" name="tgl_kembali" class="w-full text-sm border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm text-gray-600 mb-6 bg-blue-50/50 p-4 rounded-xl border border-blue-50">
                            <div class="flex justify-between">
                                <span>Durasi Sewa:</span>
                                <span id="text_durasi" class="font-medium text-gray-900">0 Hari</span>
                            </div>
                            <div class="pt-3 border-t border-blue-100 flex justify-between items-center mt-2">
                                <span class="font-bold text-gray-900">Total Dibayar Lunas</span>
                                <span id="text_total_biaya" class="text-lg font-bold text-blue-600">Rp 0</span>
                            </div>
                            <p class="text-[10px] text-gray-400 text-right mt-1">*Pembayaran dilakukan secara penuh (100%) via Midtrans.</p>
                        </div>

                        @if($katalog->stok_tersedia > 0)
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-blue-700 transition duration-200 shadow-md shadow-blue-600/20 flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Sewa Sekarang
                            </button>
                        @else
                            <button type="button" disabled class="w-full bg-gray-200 text-gray-500 font-bold py-3 px-4 rounded-xl cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </form>

                    <div class="mt-4 flex justify-center gap-4 text-xs font-medium text-green-600">
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Asuransi Penuh</span>
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg> Produk Terawat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tglPesan = document.getElementById('tgl_pesan');
        const tglKembali = document.getElementById('tgl_kembali');
        const hargaPerHari = parseInt(document.getElementById('hargaPerHari').getAttribute('data-harga'));

        // Gembok input manual dari keyboard
        tglPesan.addEventListener('keydown', (e) => e.preventDefault());
        tglKembali.addEventListener('keydown', (e) => e.preventDefault());
        
        // Format Rupiah
        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // Tampilkan estimasi awal dan set minimal tanggal mulai (Hari Ini)
        const today = new Date().toISOString().split('T')[0];
        tglPesan.setAttribute('min', today);

        // Atur Gembok Tanggal Selesai otomatis setiap kali Tanggal Mulai dipilih
        tglPesan.addEventListener('change', function() {
            if(this.value) {
                let minKembali = new Date(this.value);
                minKembali.setDate(minKembali.getDate() + 1); // H+1 minimal
                
                let minKembaliStr = minKembali.toISOString().split('T')[0];
                tglKembali.setAttribute('min', minKembaliStr);
                
                // Hapus data tanggal selesai jika ternyata lebih kecil dari tanggal mulai
                if(tglKembali.value && tglKembali.value <= this.value) {
                    tglKembali.value = ''; 
                }
                hitungBiaya();
            }
        });

        // Trigger perhitungan saat tanggal kembali dipilih
        tglKembali.addEventListener('change', hitungBiaya);

        function hitungBiaya() {
            if(tglPesan.value && tglKembali.value) {
                const date1 = new Date(tglPesan.value);
                const date2 = new Date(tglKembali.value);
                
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if(diffDays > 0) {
                    const totalSewa = diffDays * hargaPerHari;

                    document.getElementById('text_durasi').innerText = diffDays + ' Hari';
                    document.getElementById('text_total_biaya').innerText = formatRupiah(totalSewa);
                }
            } else {
                document.getElementById('text_durasi').innerText = '0 Hari';
                document.getElementById('text_total_biaya').innerText = 'Rp 0';
            }
        }
    </script>
</body>
</html>