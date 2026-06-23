<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Batas Waktu Rental.ly</title>
</head>
<body style="background-color: #f8fafc; font-family: sans-serif; padding: 30px; color: #334155;">

    <div style="max-w: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        
        <div style="background-color: #1e3a8a; padding: 30px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0; font-size: 24px;">Masa Sewa Hampir Habis! ⏱️</h2>
            <p style="margin: 5px 0 0 0; color: #93c5fd; font-size: 14px;">Order ID: #RENT-{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div style="padding: 30px; line-height: 1.6;">
            <p>Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
            <p>Kami ingin mengingatkan bahwa masa sewa barang Anda di <strong>Rental.ly</strong> akan segera berakhir pada:</p>
            
            <div style="background-color: #fef08a; border: 1px solid #fef08a; padding: 15px; border-radius: 12px; text-align: center; margin: 20px 0;">
                <span style="font-size: 18px; font-weight: bold; color: #854d0e;">
                    📆 {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}
                </span>
            </div>

            <p><strong>Daftar Barang Disewa:</strong></p>
            <ul style="padding-left: 20px; margin-top: 5px;">
                @foreach($peminjaman->detail_peminjaman as $detail)
                    <li>{{ $detail->unit_barang->katalog_barang->nama_barang ?? 'Barang' }}</li>
                @endforeach
            </ul>

            <p style="color: #64748b; font-size: 14px; margin-top: 25px;">
                <em>*Catatan: Keterlambatan pengembalian akan dikenakan denda otomatis sebesar Rp 50.000 per hari sesuai dengan ketentuan layanan kami.</em>
            </p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="http://127.0.0.1:8000/dashboard" style="background-color: #2563eb; color: #ffffff; padding: 12px 30px; border-radius: 10px; text-decoration: none; font-weight: bold; display: inline-block;">Cek Dashboard Saya</a>
            </div>
        </div>

        <div style="background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
            &copy; 2026 Rental.ly - Platform Penyewaan Elektronik Impian.
        </div>
    </div>

</body>
</html>