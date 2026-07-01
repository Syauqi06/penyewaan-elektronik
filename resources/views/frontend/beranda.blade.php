<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental.ly - Sewa Gadget Impianmu Hari Ini</title>
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
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('beranda') }}" class="text-blue-700 font-semibold border-b-2 border-blue-700 pb-1">Beranda</a>
                    <a href="{{ route('katalog.index') }}" class="text-gray-500 hover:text-gray-900 font-medium transition">Katalog Produk</a>
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

    <div class="pt-32 pb-20 bg-gradient-to-b from-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold mb-6 uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span> Platform Penyewaan No. 1
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Sewa Gadget <span class="text-blue-700">Impianmu</span> Hari Ini
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Akses teknologi terbaru tanpa harus membelinya. Nikmati fleksibilitas penyewaan kamera, laptop, hingga perlengkapan gaming premium untuk kebutuhan harian, mingguan, atau bulanan.
                    </p>
                    <div class="flex mt-8">
                        <a href="#produk-pilihan" class="bg-blue-700 text-white w-full sm:w-80 px-8 py-4 rounded-xl text-lg font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-700/30 flex items-center justify-center gap-3">
                            Mulai Menjelajah 
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="relative lg:ml-auto">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?q=80&w=1000&auto=format&fit=crop" alt="Hero Setup" class="w-full max-w-lg object-cover aspect-[4/3]">
                        <div class="absolute inset-0 bg-gradient-to-tr from-gray-900/40 to-transparent"></div>
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-white/90 backdrop-blur-sm p-4 rounded-2xl shadow-xl border border-gray-100 flex items-center gap-4">
                        <div class="bg-green-100 p-3 rounded-full text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Pembayaran Aman</h4>
                            <p class="text-sm text-gray-500">
                                Pembayaran dilakukan sesuai biaya sewa yang dipilih <span class="text-xs italic text-gray-400 ml-1">*s&k berlaku</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="produk-pilihan" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Produk Pilihan</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Temukan perangkat terbaik untuk menunjang aktivitas harian dan hobi Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($katalog as $item)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="relative w-full aspect-square bg-gray-50 flex items-center justify-center overflow-hidden p-6">
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
                                    <p class="text-xs text-gray-400 mb-0.5">Mulai dari</p>
                                    <p class="text-lg font-extrabold text-blue-700">
                                        Rp{{ number_format($item->harga_sewa_per_hari, 0, ',', '.') }}<span class="text-xs font-medium text-gray-400">/hr</span>
                                    </p>
                                </div>
                                @if($item->stok_tersedia > 0)
                                    <a href="{{ route('katalog.show', $item->id) }}" class="bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white p-2.5 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                @else
                                    <button disabled class="bg-gray-100 text-gray-400 p-2.5 rounded-lg cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-800 transition">
                    Lihat Semua Produk <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Ulasan Pelanggan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-50 p-8 rounded-2xl relative">
                    <svg class="absolute top-6 right-6 w-10 h-10 text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-xl">A</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Andi Pratama</h4>
                            <p class="text-xs text-gray-500">Fotografer Freelance</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">"Sangat merekomendasikan Rental.ly! Proses sewa kamera Sony A7III sangat cepat, barangnya dalam kondisi sempurna, dan proses pembayarannya juga sangat transparan."</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl relative">
                    <svg class="absolute top-6 right-6 w-10 h-10 text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-700 font-bold text-xl">S</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Siti Rahayu</h4>
                            <p class="text-xs text-gray-500">Mahasiswa Desain</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">"Waktu laptop saya rusak saat deadline tugas akhir, Rental.ly jadi penyelamat. Sewa MacBook Pro di sini proses verifikasinya gampang dan harga sangat masuk akal."</p>
                </div>
                <div class="bg-slate-50 p-8 rounded-2xl relative">
                    <svg class="absolute top-6 right-6 w-10 h-10 text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-orange-700 font-bold text-xl">B</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Budi Santoso</h4>
                            <p class="text-xs text-gray-500">Event Organizer</p>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">"Platform rental paling rapi yang pernah saya temui. Tampilan UI-nya bersih, cek ketersediaan stok real-time, dan proses pembayaran yang dijalankan sangat lancar."</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-1">
                    <h3 class="text-2xl font-extrabold text-white mb-4">Rental.ly</h3>
                    <p class="text-sm">Pasar Penyewaan Elektronik Premium untuk para profesional dan antusias teknologi.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">Karier</a></li>
                        <li><a href="#" class="hover:text-white transition">Program Afiliasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Hubungi Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition">Ketentuan Layanan</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Jl. Apa bae No. 12, Bekasi</li>
                        <li>support@rentally.com</li>
                        <li>+62 21 555 1234</li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <p>&copy; 2026 Rental.ly. Semua Hak Cipta Dilindungi.</p>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-white transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                    <a href="#" class="hover:text-white transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>