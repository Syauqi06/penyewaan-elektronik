<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Sewa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center">
        <h2 class="text-2xl font-bold mb-4">Selesaikan Pembayaran</h2>
        <p class="mb-6 text-gray-600">Total Tagihan: <span class="font-bold text-indigo-600 text-xl">Rp {{ number_format($peminjaman->total_biaya_sewa, 0, ',', '.') }}</span></p>
        
        <button id="pay-button" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
            Bayar Sekarang
        </button>
    </div>

    <script>
        const payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', async function () {
            // Disable button saat loading
            payButton.innerHTML = 'Memproses...';
            payButton.disabled = true;

            try {
                // Ambil token dari backend kita (Controller)
                const response = await fetch('{{ route('checkout.token', $peminjaman->id) }}');
                const data = await response.json();

                if (data.snap_token) {
                    // Panggil pop-up Midtrans
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            alert("Pembayaran Berhasil!"); 
                            window.location.href = '/dashboard';
                        },
                        onPending: function(result){
                            alert("Menunggu Pembayaran Anda!");
                            window.location.href = '/dashboard';
                        },
                        onError: function(result){
                            alert("Pembayaran Gagal!");
                        },
                        onClose: function(){
                            payButton.innerHTML = 'Bayar Sekarang';
                            payButton.disabled = false;
                        }
                    });
                } else {
                    alert('Gagal mengambil token pembayaran.');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan sistem.');
            }
        });
    </script>
</body>
</html>