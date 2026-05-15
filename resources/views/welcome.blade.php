<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SewaElektronik - Cepat & Mudah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-2xl text-indigo-600">SewaElektronik.</span>
                </div>
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2">Log in</a>
                        <a href="{{ route('register') }}" class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-indigo-700 transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="relative bg-indigo-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center">
                <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Sewa Gadget Impian</span>
                    <span class="block text-indigo-600">Tanpa Harus Membeli</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Laptop, kamera, drone, hingga proyektor. Semua tersedia untuk mendukung produktivitas dan hobi Anda. Harga terjangkau, proses cepat, dan terpercaya.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Katalog Tersedia</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse ($barangs as $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col">
                    <div class="h-48 bg-gray-200 overflow-hidden">
                        @if($barang->foto_barang)
                            <img src="{{ Storage::url($barang->foto_barang) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full w-full text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-50 mb-2">
                                {{ $barang->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $barang->nama_barang }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $barang->deskripsi }}</p>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-lg font-extrabold text-indigo-600">Rp {{ number_format($barang->harga_sewa_perhari, 0, ',', '.') }}<span class="text-sm font-normal text-gray-500">/hari</span></span>
                            <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Sewa
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada barang yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>