<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased">

    <nav class="bg-white/80 backdrop-blur-md sticky w-full z-50 top-0 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('beranda') }}" class="text-2xl font-extrabold text-blue-700 tracking-tight">Rental.ly</a>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('beranda') }}" class="text-gray-500 hover:text-gray-900 font-medium transition">Beranda</a>
                    <a href="{{ route('katalog.index') }}" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Katalog Produk</a>
                    <a href="{{ route('kategori.index') }}" class="text-gray-500 hover:text-gray-900 font-medium transition">Kategori</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-700 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-700 font-medium hidden sm:block">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 font-medium transition shadow-md shadow-blue-700/20">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-8 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Eksplor Katalog</h1>
                    <p class="text-gray-500 text-sm">
                        @if(request('search'))
                            Menampilkan hasil pencarian untuk: <span class="font-bold text-gray-900">"{{ request('search') }}"</span>
                        @else
                            Temukan berbagai gadget yang siap menemani aktivitasmu.
                        @endif
                    </p>
                </div>
                
                <form action="{{ route('katalog.index') }}" method="GET" class="w-full md:w-80">
                    <div class="relative flex items-center">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang atau kategori..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm transition">
                        <button type="submit" class="absolute left-3 text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                        
                        @if(request('search'))
                            <a href="{{ route('katalog.index') }}" class="absolute right-3 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($katalog->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($katalog as $item)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="relative w-full aspect-square bg-gray-50 flex items-center justify-center overflow-hidden p-6 border-b border-gray-50">
                                @if($item->stok_tersedia > 0)
                                    <span class="absolute top-3 left-3 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-md">Tersedia</span>
                                @else
                                    <span class="absolute top-3 left-3 bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-md">Habis</span>
                                @endif

                                @if($item->foto_barang)
                                    <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-contain">
                                @else
                                    <span class="text-gray-400 text-sm">Tanpa Gambar</span>
                                @endif
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">{{ $item->kategori->nama_kategori }}</span>
                                <h3 class="text-base font-bold text-gray-900 line-clamp-2 mb-4">{{ $item->nama_barang }}</h3>
                                
                                <div class="mt-auto border-t border-gray-100 pt-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-0.5">Sewa Harian</p>
                                        <p class="text-lg font-extrabold text-blue-700">
                                            Rp{{ number_format($item->harga_sewa_per_hari, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    @if($item->stok_tersedia > 0)
                                        <a href="{{ route('katalog.show', $item->id) }}" class="bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                                            Sewa
                                        </a>
                                    @else
                                        <button disabled class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg text-sm font-bold cursor-not-allowed">
                                            Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $katalog->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-white border border-gray-100 rounded-3xl shadow-sm">
                    <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Pencarian Tidak Ditemukan</h3>
                    <p class="text-gray-500 text-sm mb-6">Maaf, kami tidak bisa menemukan barang dengan kata kunci "{{ request('search') }}".</p>
                    <a href="{{ route('katalog.index') }}" class="inline-block bg-blue-50 text-blue-600 font-bold px-6 py-2.5 rounded-xl hover:bg-blue-100 transition">Kembali ke Semua Katalog</a>
                </div>
            @endif

        </div>
    </div>

</body>
</html>