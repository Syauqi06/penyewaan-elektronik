<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan - {{ $katalog->nama_barang }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Detail Pemesanan</h2>
            <a href="{{ route('katalog.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Katalog</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 flex flex-col md:flex-row gap-8">
            
            <div class="w-full md:w-1/3 border-b md:border-b-0 md:border-r border-gray-100 pb-6 md:pb-0 md:pr-6">
                <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden flex items-center justify-center mb-4">
                    @if($katalog->foto_barang)
                        <img src="{{ asset('storage/' . $katalog->foto_barang) }}" class="w-full h-full object-contain p-2">
                    @endif
                </div>
                <h3 class="font-bold text-lg text-gray-900">{{ $katalog->nama_barang }}</h3>
                <p class="text-orange-500 font-bold mt-2 text-xl" id="hargaPerHari" data-harga="{{ $katalog->harga_sewa_per_hari }}" data-harga-asli="{{ $katalog->harga_asli }}">
                    Rp {{ number_format($katalog->harga_sewa_per_hari, 0, ',', '.') }} <span class="text-sm text-gray-500 font-normal">/ hari</span>
                </p>
            </div>

            <div class="w-full md:w-2/3">
                <form action="#" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Alamat Pengiriman Kurir</label>
                            
                            @if($alamats->count() > 0)
                                <select name="alamat_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                                    @foreach($alamats as $alamat)
                                        <option value="{{ $alamat->id }}">
                                            {{ $alamat->label_alamat }} - {{ \Illuminate\Support\Str::limit($alamat->detail_alamat, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
                                    <span>Anda belum memiliki alamat.</span>
                                    <a href="{{ route('dashboard') }}" class="font-bold underline hover:text-red-800">Tambah Alamat</a>
                                </div>
                            @endif
                        </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai Sewa</label>
                            <input type="date" name="tgl_pesan" id="tgl_pesan" 
                                value="{{ $tglPesan ?? '' }}" 
                                class="w-full text-sm border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                                required readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Rencana Kembali</label>
                            <input type="date" name="tgl_kembali" id="tgl_kembali" 
                                value="{{ $tglKembali ?? '' }}" 
                                class="w-full text-sm border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                                required readonly>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg mt-6 border border-blue-100">
                        <h4 class="font-semibold text-blue-800 mb-3 text-sm">Rincian Biaya Otomatis</h4>
                        <div class="space-y-2 text-sm text-blue-900">
                            <div class="flex justify-between">
                                <span>Durasi Sewa:</span>
                                <span id="text_durasi" class="font-bold">0 Hari</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Biaya Sewa:</span>
                                <span id="text_total_sewa" class="font-bold">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Deposit Keamanan (Dikembalikan di akhir):</span>
                                <span id="text_deposit">Rp 300.000</span>
                            </div>
                            <hr class="border-blue-200 my-2">
                            <div class="flex justify-between text-lg font-bold text-blue-700">
                                <span>Total Bayar Awal (DP 50% + Deposit):</span>
                                <span id="text_dp">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                            @if(!$verifikasi)
                                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm flex items-start gap-3">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <div>
                                        <p class="font-bold">KTP Belum Diunggah</p>
                                        <p class="mt-1">Anda wajib mengunggah KTP sebelum dapat melanjutkan penyewaan.</p>
                                        <a href="{{ route('dashboard') }}" class="inline-block mt-2 font-bold text-red-700 underline">Upload KTP Sekarang</a>
                                    </div>
                                </div>
                                <button type="button" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3.5 px-4 rounded-xl cursor-not-allowed">Lanjutkan Pembayaran</button>

                            @elseif($verifikasi->status == 'pending')
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-start gap-3">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <p class="font-bold">Verifikasi KTP Sedang Diproses</p>
                                        <p class="mt-1">Mohon tunggu admin menyetujui dokumen KTP Anda. Anda baru bisa menyewa setelah status disetujui.</p>
                                    </div>
                                </div>
                                <button type="button" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3.5 px-4 rounded-xl cursor-not-allowed">Lanjutkan Pembayaran</button>

                            @elseif($verifikasi->status == 'ditolak')
                                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm flex items-start gap-3">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <div>
                                        <p class="font-bold">Verifikasi KTP Ditolak</p>
                                        <p class="mt-1">Alasan: {{ $verifikasi->catatan }}</p>
                                        <a href="{{ route('dashboard') }}" class="inline-block mt-2 font-bold text-red-700 underline">Upload Ulang KTP</a>
                                    </div>
                                </div>
                                <button type="button" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3.5 px-4 rounded-xl cursor-not-allowed">Lanjutkan Pembayaran</button>

                            @else
                                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-blue-700 transition duration-200 shadow-md shadow-blue-600/20">Lanjutkan Pembayaran</button>
                            @endif
                        </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Ambil nilai tanggal yang sudah dilempar dari controller
        const tglPesan = document.getElementById('tgl_pesan').value;
        const tglKembali = document.getElementById('tgl_kembali').value;
        
        // Di halaman checkout, kita bisa langsung mengambil harga dari PHP/Blade agar lebih aman
        const hargaPerHari = {{ $katalog->harga_sewa_per_hari }};
        const hargaAsliBarang = {{ $katalog->harga_asli }};
        const deposit = hargaAsliBarang * 0.3; // Deposit 30%

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        function hitungBiayaOtomatis() {
            if(tglPesan && tglKembali) {
                const date1 = new Date(tglPesan);
                const date2 = new Date(tglKembali);
                
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if(diffDays > 0) {
                    const totalSewa = diffDays * hargaPerHari;
                    const jumlahDp = totalSewa * 0.5; // DP 50%
                    const totalBayarSekarang = jumlahDp + deposit;

                    // Update tampilan di layar
                    document.getElementById('text_durasi').innerText = diffDays + ' Hari';
                    document.getElementById('text_total_sewa').innerText = formatRupiah(totalSewa);
                    document.getElementById('text_dp').innerText = formatRupiah(totalBayarSekarang);
                    
                    // Update deposit (kalau Anda memberikan id='text_deposit' di HTML rincian biayanya)
                    if(document.getElementById('text_deposit')) {
                        document.getElementById('text_deposit').innerText = formatRupiah(deposit);
                    }
                }
            }
        }

        window.addEventListener('DOMContentLoaded', (event) => {
            hitungBiayaOtomatis();
        });
    </script>
</body>
</html>