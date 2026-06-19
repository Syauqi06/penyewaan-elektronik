<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Penyewaan Elektronik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">SewaElektronik</h1>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard Saya</a>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium mr-4">Log in</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Daftar</a>
            @endauth
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Katalog Barang Sewa</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($katalog as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300">
                    
                    <div class="relative w-full aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if($item->foto_barang)
                            <img src="{{ asset('storage/' . $item->foto_barang) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-contain p-4">
                        @else
                            <span class="text-gray-400 text-sm">Tanpa Gambar</span>
                        @endif
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">{{ $item->kategori->nama_kategori }}</span>
                        <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2">{{ $item->nama_barang }}</h3>
                        
                        <div class="mt-auto">
                            <p class="text-lg font-bold text-orange-500">
                                Rp {{ number_format($item->harga_sewa_per_hari, 0, ',', '.') }}<span class="text-xs text-gray-400 font-normal">/hari</span>
                            </p>
                            
                            <div class="mt-3 flex items-center justify-between">
                                @if($item->stok_tersedia > 0)
                                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-md">Stok: {{ $item->stok_tersedia }}</span>
                                        <a href="{{ route('booking.create', $item->id) }}" class="text-xs font-semibold bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 transition text-center">Sewa</a>                                
                                @else
                                    <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-md">Habis</span>
                                        <a href="{{ route('booking.create', $item->id) }}" class="text-xs font-semibold bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 transition text-center">Sewa</a>                                
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>