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
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Alamat Pengiriman Kurir</label>
                        @if($alamats->count() > 0)
                            <select name="alamat_user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Alamat --</option>
                                @foreach($alamats as $alamat)
                                    <option value="{{ $alamat->id }}">{{ $alamat->label_alamat }} - {{ $alamat->detail_alamat }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="text-sm text-red-600 bg-red-50 p-3 rounded-md">
                                Anda belum memiliki alamat. <a href="#" class="font-bold underline">Tambah Alamat</a>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Sewa</label>
                            <input type="date" id="tgl_pesan" name="tanggal_pesan" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Rencana Kembali</label>
                            <input type="date" id="tgl_kembali" name="tanggal_kembali_rencana" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required disabled>
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

                    <button type="button" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition mt-4">
                        Lanjutkan Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tglPesan = document.getElementById('tgl_pesan');
        const tglKembali = document.getElementById('tgl_kembali');
        const hargaPerHari = parseInt(document.getElementById('hargaPerHari').getAttribute('data-harga'));
        const hargaAsliBarang = parseInt(document.getElementById('hargaPerHari').getAttribute('data-harga-asli'));
        const deposit = hargaAsliBarang * 0.3;

        // Format Rupiah
        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // Set minimal tanggal pesan adalah hari ini
        const today = new Date().toISOString().split('T')[0];
        tglPesan.setAttribute('min', today);

        document.getElementById('text_deposit').innerText = formatRupiah(deposit);
        tglPesan.addEventListener('change', function() {
            tglKembali.disabled = false;
            // Tanggal kembali minimal 1 hari setelah tanggal pesan
            let minKembali = new Date(this.value);
            minKembali.setDate(minKembali.getDate() + 1);
            tglKembali.setAttribute('min', minKembali.toISOString().split('T')[0]);
            tglKembali.value = ''; // Reset tanggal kembali jika tanggal pesan diubah
            hitungBiaya();
        });

        tglKembali.addEventListener('change', hitungBiaya);

        function hitungBiaya() {
            if(tglPesan.value && tglKembali.value) {
                const date1 = new Date(tglPesan.value);
                const date2 = new Date(tglKembali.value);
                
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if(diffDays > 0) {
                    const totalSewa = diffDays * hargaPerHari;
                    const jumlahDp = totalSewa * 0.5; // DP 50% dari biaya sewa
                    const totalBayarSekarang = jumlahDp + deposit; // (50% Sewa) + Deposit 30% harga asli

                    document.getElementById('text_durasi').innerText = diffDays + ' Hari';
                    document.getElementById('text_total_sewa').innerText = formatRupiah(totalSewa);
                    document.getElementById('text_dp').innerText = formatRupiah(totalBayarSekarang);
                }
            } else {
                document.getElementById('text_durasi').innerText = '0 Hari';
                document.getElementById('text_total_sewa').innerText = 'Rp 0';
                document.getElementById('text_dp').innerText = 'Rp 0';
            }
        }
    </script>
</body>
</html>