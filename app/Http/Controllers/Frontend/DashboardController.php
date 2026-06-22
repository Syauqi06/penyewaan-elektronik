<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AlamatUser;
use App\Models\Peminjaman;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $alamats = $user->alamat_users; 
        $verifikasi = $user->verifikasi_identitas;

        $peminjamans = \App\Models\Peminjaman::with(['detail_peminjaman.unit_barang.katalog_barang', 'pembayaran'])
                        ->where('user_id', $user->id)
                        ->latest('tanggal_pesan')
                        ->get();

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        foreach ($peminjamans as $pinjam) {
            // Kita cuma ngecek pesanan yang statusnya masih 'pending'
            if ($pinjam->status_peminjaman == 'pending') {
                $tagihanAwal = $pinjam->pembayaran->where('jenis_pembayaran', 'tagihan_awal')->first();

                // Pastikan ada Order ID (kode_transaksi_gateway)
                if ($tagihanAwal && $tagihanAwal->kode_transaksi_gateway) {
                    try {
                        // Laravel NANYA LANGSUNG ke server Midtrans
                        $midtransStatus = (object) \Midtrans\Transaction::status($tagihanAwal->kode_transaksi_gateway);

                        // Kalau jawaban Midtrans adalah LUNAS (Settlement/Capture)
                        if ($midtransStatus->transaction_status == 'settlement' || $midtransStatus->transaction_status == 'capture') {
                            
                            // 1. Update tabel pembayaran di database kita
                            $tagihanAwal->update([
                                'status_pembayaran' => 'settlement',
                                'metode_pembayaran' => $midtransStatus->payment_type ?? 'transfer',
                                'tanggal_bayar' => \Carbon\Carbon::now()
                            ]);

                            // 2. Update status peminjaman di database kita jadi disetujui
                            $pinjam->update([
                                'status_peminjaman' => 'disetujui'
                            ]);

                            // 3. Ubah variabel sementara biar view HTML-nya langsung berubah tanpa reload 2x
                            $pinjam->status_peminjaman = 'disetujui';
                        }
                    } catch (\Exception $e) {
                        // Abaikan kalau API Midtrans lagi error atau order belum masuk ke server mereka
                    }
                }
            }
        }

        return view('frontend.dashboard', compact('user', 'alamats', 'verifikasi', 'peminjamans'));
    }

    // Menyimpan alamat baru
    public function storeAlamat(Request $request)
    {
        $request->validate([
            'label_alamat' => 'required|string|max:255',
            'provinsi' => 'required|string',
            'kota_kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kode_pos' => 'required|numeric',
            'detail_alamat' => 'required|string',
        ]);

        $alamatLengkap = 'Kel. ' . $request->kelurahan . ', ' . $request->detail_alamat;

        AlamatUser::create([
            'user_id' => Auth::id(),
            'label_alamat' => $request->label_alamat,
            'provinsi' => $request->provinsi,
            'kota_kabupaten' => $request->kota_kabupaten,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'detail_alamat' => $alamatLengkap,
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan!');
    }

    public function uploadKtp()
    {
        $user = Auth::user();
        $verifikasi = $user->verifikasi_identitas;

        // Jika sudah disetujui atau pending, jangan izinkan upload ulang
        if ($verifikasi && in_array($verifikasi->status, ['pending', 'disetujui'])) {
            return redirect()->route('dashboard')->with('success', 'KTP Anda sedang diproses atau sudah disetujui.');
        }

        return view('frontend.upload-ktp', compact('user'));
    }

    // Menyimpan file KTP ke storage
    public function storeKtp(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // Simpan gambar ke folder storage/app/public/ktp
        $path = $request->file('foto_ktp')->store('ktp', 'public');

        // Simpan atau update ke database
        \App\Models\VerifikasiIdentitas::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'foto_ktp' => $path,
                'status' => 'pending',
                'catatan' => null 
            ]
        );

        return redirect()->route('dashboard')->with('success', 'KTP berhasil diunggah! Mohon tunggu verifikasi dari Admin.');
    }

    public function showPesanan(int $id)
    {
        $pesanan = Peminjaman::with(['detail_peminjaman.unit_barang.katalog_barang', 'pembayaran', 'alamat_user'])
                    ->where('user_id', Auth::id())
                    ->findOrFail($id);

        return view('frontend.detail-pesanan', compact('pesanan'));
    }
}
