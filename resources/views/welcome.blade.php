<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rental.Ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="antialiased bg-white text-gray-800 font-sans">

    <div class="relative bg-gray-900 h-[80vh] flex flex-col overflow-hidden">
        
        <div id="slider-container" class="absolute inset-0 z-0">
            <div class="slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100">
                <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1920&q=80" alt="Hero 1" class="w-full h-full object-cover opacity-40">
            </div>
            <div class="slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="https://images.unsplash.com/photo-1511381939415-e440c05231e2?auto=format&fit=crop&w=1920&q=80" alt="Hero 2" class="w-full h-full object-cover opacity-40">
            </div>
            <div class="slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="https://images.unsplash.com/photo-1527443154391-42721dc225c5?auto=format&fit=crop&w=1920&q=80" alt="Hero 3" class="w-full h-full object-cover opacity-40">
            </div>
        </div>

        <nav class="relative z-10 w-full text-white py-6 px-8 flex justify-between items-center">
            <div class="text-2xl font-bold tracking-wider">Rental.Ly</div>
            <div class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="#" class="hover:text-gray-300">Home</a>
                <a href="#" class="hover:text-gray-300">Order</a>
                <a href="#" class="hover:text-gray-300">About</a>
                <a href="#" class="hover:text-gray-300">Contact</a>
            </div>
            <div class="space-x-4 text-sm font-medium">
                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-300">Log In</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-300">Sign Up</a>
                @endauth
            </div>
        </nav>

        <div class="relative z-10 flex-grow flex flex-col justify-center items-center text-center px-4">
            <p class="text-white text-lg md:text-xl tracking-widest mb-2">WELCOME TO</p>
            <h1 class="text-white text-5xl md:text-7xl font-bold tracking-tight">Rental.Ly</h1>
            
            <div class="flex space-x-3 mt-8">
                <button onclick="changeSlide(0)" class="dot w-2 h-2 md:w-3 md:h-3 rounded-full bg-white transition-opacity duration-300 opacity-100"></button>
                <button onclick="changeSlide(1)" class="dot w-2 h-2 md:w-3 md:h-3 rounded-full bg-white transition-opacity duration-300 opacity-50 hover:opacity-75"></button>
                <button onclick="changeSlide(2)" class="dot w-2 h-2 md:w-3 md:h-3 rounded-full bg-white transition-opacity duration-300 opacity-50 hover:opacity-75"></button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Lokasi anda sekarang?</h3>
        <div class="relative w-full md:w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-gray-400"></i>
            </div>
            <input type="text" class="bg-gray-100 border-none text-gray-900 text-sm rounded-lg focus:ring-indigo-500 block w-full pl-10 p-2.5" placeholder="Jakarta...">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @php
            $groupedBarangs = $barangs->groupBy(function($item) {
                return $item->kategori->nama_kategori ?? 'Umum';
            });
        @endphp

        @foreach($groupedBarangs as $kategori => $items)
            <div class="mb-12">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Pilihan {{ $kategori }}</h3>
                
                <div class="flex overflow-x-auto space-x-6 pb-4 snap-x">
                    @foreach($items as $barang)
                        <div class="min-w-[260px] max-w-[260px] bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow snap-start flex flex-col">
                            <div class="h-40 w-full p-4 flex justify-center items-center">
                                @if($barang->foto_barang)
                                    <img src="{{ Storage::url($barang->foto_barang) }}" alt="{{ $barang->nama_barang }}" class="object-contain h-full">
                                @else
                                    <i class="fa-solid fa-image text-4xl text-gray-300"></i>
                                @endif
                            </div>
                            
                            <div class="p-4 flex-grow flex flex-col justify-between border-t border-gray-50">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-1">{{ $barang->nama_barang }}</h4>
                                    <div class="flex text-yellow-400 text-xs mt-1 mb-2">
                                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm font-bold text-gray-900">Rp{{ number_format($barang->harga_sewa_perhari, 0, ',', '.') }}<span class="text-xs font-normal text-gray-500">/hari</span></span>
                                </div>

                                <form action="{{ route('checkout.store', $barang->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="submit" class="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded hover:bg-gray-700 transition">
                                        Order Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="w-full bg-gray-100 py-16">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center gap-8">
            <div class="md:w-1/2">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Rental.Ly</h2>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    Layanan aplikasi ini dibuat karena kebutuhan konsumen di berbagai daerah akan keperluan alat elektronik tanpa harus membeli. Di mana banyak orang membutuhkan sarana penunjang digital dalam waktu singkat dan proses penyewaan yang terpercaya.
                </p>
                <a href="#" class="inline-block bg-gray-900 text-white font-semibold py-3 px-8 rounded hover:bg-gray-700 transition">
                    Order Now
                </a>
            </div>
            <div class="md:w-1/2">
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80" alt="About" class="rounded-lg shadow-lg">
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-20 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-12">Mengapa Rental.Ly?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm border border-gray-100">
                <i class="fa-solid fa-shield-halved text-5xl text-gray-800 mb-4"></i>
                <h3 class="font-bold text-gray-800 mb-2">Terpercaya</h3>
                <p class="text-xs text-gray-500">Tidak perlu diragukan lagi kepastian layanan kami, sistem keamanan yang terjamin untuk setiap transaksi.</p>
            </div>
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm border border-gray-100">
                <i class="fa-solid fa-bolt text-5xl text-yellow-500 mb-4"></i>
                <h3 class="font-bold text-gray-800 mb-2">Pelayanan Cepat</h3>
                <p class="text-xs text-gray-500">Pengiriman dan proses serah terima sangat cepat, admin responsif dan siap membantu kebutuhan Anda.</p>
            </div>
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm border border-gray-100">
                <i class="fa-solid fa-thumbs-up text-5xl text-blue-600 mb-4"></i>
                <h3 class="font-bold text-gray-800 mb-2">Mudah & Terbaik</h3>
                <p class="text-xs text-gray-500">Banyak user telah memberikan review positif atas layanan kemudahan platform kami.</p>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t-4 border-gray-700">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 mb-12 text-sm">
            <div>
                <h4 class="font-bold text-lg mb-4">Rental.Ly</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Order</a></li>
                    <li><a href="#" class="hover:text-white">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-white">Kontak Media</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4">Ikuti Kami</h4>
                <ul class="space-y-3 text-gray-400">
                    <li><a href="#" class="hover:text-white flex items-center"><i class="fa-brands fa-instagram w-6"></i> Instagram</a></li>
                    <li><a href="#" class="hover:text-white flex items-center"><i class="fa-brands fa-whatsapp w-6"></i> Whatsapp</a></li>
                    <li><a href="#" class="hover:text-white flex items-center"><i class="fa-brands fa-facebook w-6"></i> Facebook</a></li>
                    <li><a href="#" class="hover:text-white flex items-center"><i class="fa-brands fa-x-twitter w-6"></i> Twitter</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4">Metode Pembayaran</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-white text-blue-800 px-2 py-1 rounded text-xs font-bold">BCA</span>
                    <span class="bg-white text-blue-600 px-2 py-1 rounded text-xs font-bold">MANDIRI</span>
                    <span class="bg-white text-orange-500 px-2 py-1 rounded text-xs font-bold">BNI</span>
                    <span class="bg-white text-green-500 px-2 py-1 rounded text-xs font-bold">GOPAY</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4">Metode Pengiriman</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-bold border border-white">J&T Express</span>
                    <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold border border-white">JNE</span>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-500 text-xs border-t border-gray-800 pt-8">
            Copyright © Rental.Ly All Rights Reserved.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentSlide = 0;
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            let slideInterval;

            // Fungsi untuk mengganti slide
            window.changeSlide = function(index) {
                // Hapus state aktif dari slide dan dot saat ini
                slides[currentSlide].classList.remove('opacity-100');
                slides[currentSlide].classList.add('opacity-0');
                dots[currentSlide].classList.remove('opacity-100');
                dots[currentSlide].classList.add('opacity-50');

                // Update index
                currentSlide = index;

                // Tambahkan state aktif ke slide dan dot baru
                slides[currentSlide].classList.remove('opacity-0');
                slides[currentSlide].classList.add('opacity-100');
                dots[currentSlide].classList.remove('opacity-50');
                dots[currentSlide].classList.add('opacity-100');

                // Reset timer otomatis setiap kali tombol diklik manual
                resetInterval();
            };

            // Fungsi untuk auto-slide setiap 5 detik
            function startInterval() {
                slideInterval = setInterval(() => {
                    let nextSlide = (currentSlide + 1) % slides.length;
                    changeSlide(nextSlide);
                }, 5000); // 5000ms = 5 detik
            }

            function resetInterval() {
                clearInterval(slideInterval);
                startInterval();
            }

            // Mulai auto-slide saat halaman dimuat
            startInterval();
        });
    </script>
</body>
</html>