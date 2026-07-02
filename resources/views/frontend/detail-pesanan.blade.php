<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }} - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-slate-50 text-gray-800 font-sans antialiased pb-12">

    <nav class="bg-white shadow-sm py-4 px-6 md:px-10 flex justify-between items-center border-b border-gray-200">
        <a href="{{ route('katalog.index') }}" class="text-2xl font-black text-blue-700 tracking-tight">Rental<span class="text-blue-400">.ly</span></a>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">&larr; Kembali ke Dashboard</a>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- HEADER DENGAN STATUS --}}
            <div class="bg-blue-600 p-8 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Detail Pesanan</h2>
                    <p class="text-blue-200 text-sm font-medium">Order ID: #RENT-{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <span class="inline-flex px-4 py-2 rounded-xl text-sm font-bold shadow-sm bg-white/20 text-white border border-white/30 uppercase">
                    STATUS: {{ str_replace('_', ' ', strtoupper($pesanan->status_peminjaman)) }}
                </span>
            </div>

            <div class="p-8">
                {{-- JADWAL SEWA & ALAMAT --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Jadwal Sewa</p>
                        <p class="text-sm font-medium text-gray-800 mb-1">
                            <span class="text-gray-500 w-20 inline-block">Mulai</span> : {{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y') }}
                        </p>
                        <p class="text-sm font-medium text-gray-800">
                            <span class="text-gray-500 w-20 inline-block">Selesai</span> : {{ \Carbon\Carbon::parse($pesanan->tanggal_kembali_rencana)->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alamat Pengiriman</p>
                        <p class="text-sm font-bold text-gray-800">{{ $pesanan->alamat_user->label_alamat ?? 'Alamat' }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $pesanan->alamat_user->detail_alamat ?? 'Detail alamat tidak ditemukan.' }}</p>
                    </div>
                </div>

                {{-- DAFTAR BARANG --}}
                <div class="mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Daftar Barang Disewa</p>
                    <div class="space-y-4">
                        @foreach($pesanan->detail_peminjaman as $detail)
                            @php
                                $katalog = $detail->unit_barang->katalog_barang;
                                $durasi = \Carbon\Carbon::parse($pesanan->tanggal_pesan)->diffInDays(\Carbon\Carbon::parse($pesanan->tanggal_kembali_rencana));
                                $subtotalBarang = $durasi * $detail->harga_sewa_satuan; 
                            @endphp
                            <div class="flex items-center justify-between border border-gray-100 p-4 rounded-2xl bg-slate-50/50">
                                <div class="flex items-center gap-4">
                                    <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $katalog->nama_barang ?? 'Barang' }}</p>
                                        <p class="text-sm text-gray-500">{{ $durasi }} Hari x Rp {{ number_format($detail->harga_sewa_satuan ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">Rp {{ number_format($subtotalBarang, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- INFO PENGEMBALIAN (Jika Sudah Ada) --}}
                @if($pesanan->pengembalian)
                    <div class="bg-green-50 p-6 rounded-2xl border border-green-200 mb-8">
                        <p class="text-xs font-bold text-green-600 uppercase tracking-wider mb-4">Informasi Pengembalian</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tanggal Pengembalian</p>
                                <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($pesanan->pengembalian->tanggal_kembali_aktual)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Kondisi Barang</p>
                                <p class="font-bold text-gray-900">
                                    @if($pesanan->pengembalian->kondisi_barang_kembali == 'baik')
                                        <span class="text-green-600">✓ Baik</span>
                                    @elseif($pesanan->pengembalian->kondisi_barang_kembali == 'rusak_ringan')
                                        <span class="text-yellow-600">⚠ Rusak Ringan</span>
                                    @else
                                        <span class="text-red-600">✗ Rusak Berat</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Keterlambatan</p>
                                <p class="font-bold text-gray-900">
                                    {{ $pesanan->pengembalian->jumlah_hari_telat }} Hari
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Status Denda</p>
                                <p class="font-bold text-gray-900">
                                    @if($pesanan->pengembalian->status_denda == 'menunggu_verifikasi')
                                        <span class="text-orange-600">Menunggu Verifikasi Admin</span>
                                    @elseif($pesanan->pengembalian->status_denda == 'belum_bayar')
                                        <span class="text-red-600">Belum Bayar</span>
                                    @elseif($pesanan->pengembalian->status_denda == 'sudah_bayar')
                                        <span class="text-green-600">Sudah Bayar</span>
                                    @else
                                        <span class="text-green-600">Tidak Ada Denda</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($pesanan->pengembalian->foto_kondisi_kembali)
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Foto Kondisi Barang</p>
                                <img src="{{ asset('storage/' . $pesanan->pengembalian->foto_kondisi_kembali) }}" 
                                     alt="Foto Pengembalian" 
                                     class="max-w-xs rounded-lg shadow-md">
                            </div>
                        @endif
                    </div>
                @endif

                {{-- RINCIAN PEMBAYARAN --}}
                <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Pembayaran</p>
                    
                    <div class="flex justify-between items-center mb-6">
                        <span class="font-bold text-gray-900 text-lg">
                            @if(in_array($pesanan->status_peminjaman, ['menunggu_pelunasan', 'menunggu_konfirmasi_denda']))
                                Total Denda yang Harus Dibayar
                            @else
                                Total Pembayaran Sewa
                            @endif
                        </span>
                        <span class="font-black text-blue-700 text-2xl">
                            @if(in_array($pesanan->status_peminjaman, ['menunggu_pelunasan', 'menunggu_konfirmasi_denda']))
                                Rp {{ number_format($pesanan->pengembalian->total_denda ?? 0, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($pesanan->total_biaya_sewa, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    
                    {{-- TOMBOL AKSI BERDASARKAN STATUS --}}
                    
                    {{-- STATUS: Pending (Belum Bayar Sewa) --}}
                    @if($pesanan->status_peminjaman == 'pending' && $pesanan->pembayaran->first())
                        <button onclick="payWithMidtrans('{{ $pesanan->pembayaran->first()->snap_token }}')" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-md">
                            Bayar Sewa Sekarang
                        </button>

                    {{-- STATUS: Menunggu Pelunasan Denda --}}
                    @elseif($pesanan->status_peminjaman == 'menunggu_pelunasan')
                        <form action="{{ route('pesanan.bayar-denda', $pesanan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition shadow-md">
                                Bayar Denda Sekarang
                            </button>
                        </form>

                    {{-- STATUS: Menunggu Konfirmasi Denda (Setelah Bayar Denda) --}}
                    @elseif($pesanan->status_peminjaman == 'menunggu_konfirmasi_denda')
                        <div class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-xl text-center border border-gray-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Menunggu Konfirmasi Pembayaran Denda dari Admin
                        </div>

                    {{-- STATUS: Menunggu Konfirmasi Pengembalian --}}
                    @elseif($pesanan->status_peminjaman == 'menunggu_konfirmasi_pengembalian')
                        <div class="w-full bg-orange-100 text-orange-800 font-bold py-3 rounded-xl text-center border border-orange-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Menunggu Verifikasi Pengembalian dari Admin
                        </div>

                    {{-- STATUS: Disetujui/Aktif (Bisa Kembalikan Barang) --}}
                    @elseif(in_array($pesanan->status_peminjaman, ['aktif']))
                        @if(!$pesanan->pengembalian)
                            <button onclick="document.getElementById('modal-kembali').classList.remove('hidden')" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Kembalikan Barang
                            </button>
                        @endif

                    {{-- STATUS: Selesai --}}
                    @elseif($pesanan->status_peminjaman == 'selesai')
                        <div class="w-full bg-green-100 text-green-800 font-bold py-3 rounded-xl text-center border border-green-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Transaksi Selesai
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM KEMBALIKAN BARANG --}}
    <div id="modal-kembali" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center p-8 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-900">Kembalikan Barang</h3>
                <button onclick="document.getElementById('modal-kembali').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Form dengan Scroll --}}
            <div class="p-8 overflow-y-auto flex-1">
                <form id="form-pengembalian" action="{{ route('pesanan.kembalikan', $pesanan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tanggal Pengembalian
                        </label>
                        <input type="date" 
                            name="tanggal_kembali" 
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Kondisi Barang Saat Dikembalikan
                        </label>
                        <select name="kondisi_barang" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="baik">Baik - Tidak ada kerusakan</option>
                            <option value="rusak_ringan">Rusak Ringan - Ada goresan/lecet</option>
                            <option value="rusak_berat">Rusak Berat - Ada kerusakan signifikan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Foto Kondisi Barang <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-500 transition">
                            <input type="file" 
                                name="foto_pengembalian" 
                                id="foto-pengembalian"
                                accept="image/*"
                                class="hidden"
                                required
                                onchange="previewImage(this)">
                            <label for="foto-pengembalian" id="upload-label" class="cursor-pointer block">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-600 font-medium">Klik untuk upload foto</span>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 5MB)</p>
                            </label>
                            <div id="preview-container" class="hidden relative mt-3">
                                <div class="relative">
                                    <img id="preview" class="w-full h-auto max-h-64 object-contain rounded-lg shadow-md">
                                    <button type="button" 
                                            onclick="removeImage()" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 shadow-lg transition transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-green-600 font-medium mt-2">✓ Foto berhasil diupload</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer dengan Tombol (Sticky di Bawah) --}}
            <div class="p-8 border-t border-gray-200 flex-shrink-0 bg-gray-50 rounded-b-3xl">
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="document.getElementById('modal-kembali').classList.add('hidden')"
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            form="form-pengembalian"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md">
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview');
                    const previewContainer = document.getElementById('preview-container');
                    const uploadLabel = document.getElementById('upload-label');
                    
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadLabel.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('foto-pengembalian');
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');
            const uploadLabel = document.getElementById('upload-label');
            
            input.value = '';
            preview.src = '';
            previewContainer.classList.add('hidden');
            uploadLabel.classList.remove('hidden');
        }

        function payWithMidtrans(snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function(result){
                    fetch(`/midtrans/check-status/${result.order_id}`)
                        .then(res => res.json())
                        .then(() => {
                            window.location.href = "{{ route('pesanan.show', $pesanan->id) }}";
                        })
                        .catch(() => {
                            window.location.href = "{{ route('pesanan.show', $pesanan->id) }}";
                        });
                },
                onPending: function(result){
                    window.location.href = "{{ route('pesanan.show', $pesanan->id) }}";
                },
                onError: function(result){
                    alert("Pembayaran gagal, silakan coba lagi.");
                    window.location.href = "{{ route('pesanan.show', $pesanan->id) }}";
                },
                onClose: function(){
                    window.location.href = "{{ route('pesanan.show', $pesanan->id) }}";
                }
            });
        }
    </script>
</body>
</html>