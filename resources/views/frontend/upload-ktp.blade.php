<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload KTP - Rental.ly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-gray-800">

    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b border-gray-100">
        <a href="{{ route('katalog.index') }}" class="text-xl font-bold text-blue-700">Rental.ly</a>
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600">&larr; Kembali ke Dashboard</a>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Identitas</h2>
            <p class="text-gray-500 mb-8">Untuk menjaga keamanan perangkat premium kami, mohon unggah foto KTP asli Anda. Data Anda kami jamin kerahasiaannya.</p>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ktp.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Foto KTP (.jpg, .png)</label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-500 transition cursor-pointer" onclick="document.getElementById('file-upload').click()">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative font-medium text-blue-600 hover:text-blue-500">
                                    <span>Pilih File</span>
                                    <input id="file-upload" name="foto_ktp" type="file" class="sr-only" required accept="image/png, image/jpeg, image/jpg" onchange="document.getElementById('file-name').innerText = this.files[0].name">
                                </span>
                            </div>
                            <p class="text-xs text-gray-500" id="file-name">Maksimal ukuran file 2MB</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md shadow-blue-600/20">
                    Kirim untuk Verifikasi
                </button>
            </form>
        </div>
    </div>

</body>
</html>