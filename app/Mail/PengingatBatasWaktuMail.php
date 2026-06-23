<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Peminjaman;

class PengingatBatasWaktuMail extends Mailable
{
    use Queueable, SerializesModels;

    public Peminjaman $peminjaman;

    // Tangkap data peminjaman pas dipanggil
    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ PENGINGAT: Masa Sewa Barang Anda Hampir Habis! - Rental.ly',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pengingat', // Jalur file HTML email kita
        );
    }
}