<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Perangkat - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased overflow-x-hidden">

    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 top-0 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('katalog.index') }}" class="text-2xl font-extrabold text-blue-700 tracking-tight">Rental.ly</a>
                </div>
                
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('katalog.index') }}" class="text-gray-500 hover:text-gray-900 font-medium transition">Jelajah</a>
                    <a href="{{ route('kategori.index') }}" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Kategori</a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 font-medium transition">Katalog</a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 font-medium transition">Bantuan</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-700 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-700 font-medium hidden sm:block">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-700 text-white px-5 py-2.5 rounded-full hover:bg-blue-800 font-semibold transition shadow-md shadow-blue-700/20">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-32 pb-24 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">Katalog Premium</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Eksplorasi Perangkat Berdasarkan Kategori</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Sewa perangkat teknologi terbaru dari merek global terkemuka untuk kebutuhan profesional, kreatif, hingga gaming harian Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($kategori as $kat)
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                        @php
                            $iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path></svg>';
                            $namaKatLokal = strtolower($kat->nama_kategori);
                            if (str_contains($namaKatLokal, 'laptop') || str_contains($namaKatLokal, 'komputer')) {
                                $iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
                            } elseif (str_contains($namaKatLokal, 'kamera') || str_contains($namaKatLokal, 'camera')) {
                                $iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
                            } elseif (str_contains($namaKatLokal, 'gaming') || str_contains($namaKatLokal, 'playstation')) {
                                $iconSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            }
                        @endphp
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <div class="w-6 h-6">{!! $iconSvg !!}</div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 relative z-10">{{ $kat->nama_kategori }}</h3>
                        <p class="text-sm text-gray-500 mb-8 line-clamp-2 relative z-10">{{ $kat->deskripsi ?? 'Jelajahi koleksi terbaik kami untuk kategori ini.' }}</p>
                        <a href="#" class="inline-flex items-center text-sm font-bold text-blue-600 group-hover:text-blue-700 relative z-10">
                            Lihat Koleksi <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <div class="absolute -bottom-6 -right-6 w-40 h-40 text-gray-50 opacity-[0.03] group-hover:opacity-[0.05] group-hover:scale-110 transition-all duration-500 pointer-events-none transform -rotate-12">
                            {!! $iconSvg !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</body>
</html>