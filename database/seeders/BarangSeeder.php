<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\KatalogBarang;
use App\Models\UnitBarang;
use Illuminate\Support\Str;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT KATEGORI
        $kategoriKamera = Kategori::firstOrCreate(
            ['nama_kategori' => 'Kamera'],
            ['deskripsi' => 'Kamera DSLR, Mirrorless, dan perlengkapan fotografi profesional.']
        );
        
        $kategoriLaptop = Kategori::firstOrCreate(
            ['nama_kategori' => 'Laptop'],
            ['deskripsi' => 'Laptop spesifikasi tinggi untuk editing, desain, dan pekerjaan berat.']
        );
        
        $kategoriGaming = Kategori::firstOrCreate(
            ['nama_kategori' => 'Gaming'],
            ['deskripsi' => 'Konsol game masa kini untuk hiburan maksimal di akhir pekan.']
        );

        // 2. DATA DUMMY BARANG
        $daftarBarang = [
            [
                'kategori_id' => $kategoriKamera->id,
                'nama_barang' => 'Sony Alpha A7 III Body Only',
                'deskripsi' => 'Kamera mirrorless full-frame dengan performa luar biasa. Cocok untuk video cinematic dan foto wedding.',
                'harga_asli' => 25000000,
                'harga_sewa_per_hari' => 250000,
                'jumlah_unit' => 3
            ],
            [
                'kategori_id' => $kategoriKamera->id,
                'nama_barang' => 'Canon EOS R5 Mirrorless',
                'deskripsi' => 'Kamera sultan dengan resolusi 45MP dan perekaman video 8K RAW. Idaman para profesional videografer.',
                'harga_asli' => 55000000,
                'harga_sewa_per_hari' => 600000,
                'jumlah_unit' => 2
            ],
            [
                'kategori_id' => $kategoriLaptop->id,
                'nama_barang' => 'MacBook Pro M2 14-inch 2023',
                'deskripsi' => 'Laptop Apple dengan chip M2 yang super ngebut. Sangat mulus untuk rendering video 4K dan programming.',
                'harga_asli' => 20000000,
                'harga_sewa_per_hari' => 300000,
                'jumlah_unit' => 4
            ],
            [
                'kategori_id' => $kategoriLaptop->id,
                'nama_barang' => 'ASUS ROG Strix G15',
                'deskripsi' => 'Laptop gaming monster dengan RTX 3070 Ti. Main game AAA rata kanan tanpa ngelag.',
                'harga_asli' => 24000000,
                'harga_sewa_per_hari' => 280000,
                'jumlah_unit' => 2
            ],
            [
                'kategori_id' => $kategoriGaming->id,
                'nama_barang' => 'Sony PlayStation 5 (Disc Edition)',
                'deskripsi' => 'Konsol gaming generasi terbaru. Lengkap dengan 2 DualSense Controller dan 3 game pilihan.',
                'harga_asli' => 8500000,
                'harga_sewa_per_hari' => 120000,
                'jumlah_unit' => 5
            ],
            [
                'kategori_id' => $kategoriGaming->id,
                'nama_barang' => 'Nintendo Switch OLED',
                'deskripsi' => 'Konsol hybrid dengan layar OLED yang memanjakan mata. Cocok buat mabar bareng temen pas nongkrong.',
                'harga_asli' => 5500000,
                'harga_sewa_per_hari' => 80000,
                'jumlah_unit' => 3
            ],
        ];

        // 3. EKSEKUSI PEMBUATAN BARANG & UNIT FISIKNYA
        foreach ($daftarBarang as $item) {
            
            $katalog = KatalogBarang::firstOrCreate(
                ['nama_barang' => $item['nama_barang']],
                [
                    'kategori_id' => $item['kategori_id'],
                    'deskripsi' => $item['deskripsi'],
                    'harga_asli' => $item['harga_asli'],
                    'harga_sewa_per_hari' => $item['harga_sewa_per_hari'],
                ]
            );

            $unitSaatIni = UnitBarang::where('katalog_barang_id', $katalog->id)->count();
            
            if ($unitSaatIni < $item['jumlah_unit']) {
                $kurangnya = $item['jumlah_unit'] - $unitSaatIni;
                
                for ($i = 1; $i <= $kurangnya; $i++) {
                    UnitBarang::create([
                        'katalog_barang_id' => $katalog->id,
                        // Sesuaikan dengan nama kolom di database lu: serial_number & kondisi_fisik
                        'serial_number' => strtoupper(Str::limit(str_replace(' ', '', $item['nama_barang']), 3, '')) . '-' . str_pad($i + $unitSaatIni, 3, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                        'kondisi_fisik' => 'Sempurna (Unit Baru)',
                        'status_ketersediaan' => 'tersedia'
                    ]);
                }
            }
        }
    }
}