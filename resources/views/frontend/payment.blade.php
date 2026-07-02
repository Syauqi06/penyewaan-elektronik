<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rental.ly</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        #loading-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        #loading-overlay.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e5e7eb;
            border-bottom-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 16px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body>

    <div id="loading-overlay">
        <div class="spinner"></div>
        <p class="loading-text">Menghubungkan ke pembayaran...</p>
    </div>

    <script>
        const loadingOverlay = document.getElementById('loading-overlay');

        function hideLoading() {
            loadingOverlay.classList.add('fade-out');
        }

        // Deteksi iframe Midtrans agar loading mulus
        const observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                for (const node of mutation.addedNodes) {
                    if (node.nodeType === 1 && (
                        node.id === 'snap-midtrans' ||
                        (node.className && String(node.className).includes('snap'))
                    )) {
                        hideLoading();
                        observer.disconnect();
                        return;
                    }
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
        const fallbackTimer = setTimeout(hideLoading, 3000);

        // CUKUP 1 KALI PEMANGGILAN SNAP PAY
        window.addEventListener('DOMContentLoaded', () => {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    // Karena tidak pakai ngrok, kita paksa frontend memanggil route backend
                    fetch(`/midtrans/check-status/${result.order_id}`)
                        .then(res => res.json())
                        .then(() => {
                            // Redirect setelah database berhasil diupdate oleh backend
                            window.location.href = "{{ route('pesanan.show', $peminjamanId) }}";
                        })
                        .catch(() => {
                            window.location.href = "{{ route('pesanan.show', $peminjamanId) }}";
                        });
                },
                onPending: function(result){
                    window.location.href = "{{ route('pesanan.show', $peminjamanId) }}";
                },
                onError: function(result){
                    alert("Pembayaran gagal, silakan coba lagi.");
                    window.location.href = "{{ route('pesanan.show', $peminjamanId) }}";
                },
                onClose: function(){
                    window.location.href = "{{ route('pesanan.show', $peminjamanId) }}";
                }
            });
        });
    </script>
</body>
</html>