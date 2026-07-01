<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }} - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-gray-800 font-sans antialiased pb-12">

    <nav class="bg-white shadow-sm py-4 px-6 md:px-10 flex justify-between items-center border-b border-gray-200">
        <a href="{{ route('katalog.index') }}" class="text-2xl font-black text-blue-700 tracking-tight">Rental<span class="text-blue-400">.ly</span></a>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">&larr; Kembali ke Dashboard</a>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="bg-blue-600 p-8 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Detail Pesanan</h2>
                    <p class="text-blue-200 text-sm font-medium">Order ID: #RENT-{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <span class="inline-flex px-4 py-2 rounded-xl text-sm font-bold shadow-sm bg-white/20 text-white border border-white/30">
                    STATUS: {{ strtoupper($pesanan->status_peminjaman) }}
                </span>
            </div>

            <div class="p-8">
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

                <div class="mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Daftar Barang Disewa</p>
                    <div class="space-y-4">
                        @foreach($pesanan->detail_peminjaman as $detail)
                            @php
                                $katalog = $detail->unit_barang->katalog_barang;
                                $durasi = \Carbon\Carbon::parse($pesanan->tanggal_pesan)->diffInDays(\Carbon\Carbon::parse($pesanan->tanggal_kembali_rencana));
                                
                                // HITUNG SUBTOTAL SECARA OTOMATIS DI SINI:
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

                <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Pembayaran</p>
                    
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-lg">Total Dibayar Lunas</span>
                        <span class="font-black text-blue-700 text-2xl">Rp {{ number_format($pesanan->total_biaya_sewa, 0, ',', '.') }}</span>
                    </div>

                    @if($pesanan->pengembalian && $pesanan->pengembalian->total_denda > 0)
                    <div class="mt-4 pt-4 border-t border-red-200">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-red-600 text-sm">Tagihan Denda (Telat / Cacat Fisik)</span>
                            <span class="font-black text-red-600 text-xl">Rp {{ number_format($pesanan->pengembalian->total_denda, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs font-medium text-red-500 mt-1">Status: {{ str_replace('_', ' ', strtoupper($pesanan->pengembalian->status_denda)) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Harap segera lunasi tagihan denda ini dengan menghubungi Admin kami.</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</body>
</html>