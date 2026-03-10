<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KAMAR DATA ===\n";
$kamars = App\Models\Kamar::where('nama_kamar', 'LIKE', 'Single Room%')->get();
foreach ($kamars as $kamar) {
    echo "{$kamar->id}. {$kamar->nama_kamar} - Harga: Rp " . number_format($kamar->harga, 0, ',', '.') . "\n";
}

echo "\n=== PEMESANAN DATA untuk Single Room ===\n";
$bookings = App\Models\Pemesanan::whereHas('kamar', function($q) {
    $q->where('nama_kamar', 'LIKE', 'Single Room%');
})->with('kamar')->get();

if ($bookings->count() == 0) {
    echo "Tidak ada pemesanan untuk Single Room\n";
} else {
    foreach ($bookings as $booking) {
        echo "\nID: {$booking->id}\n";
        echo "Kamar: {$booking->kamar->nama_kamar}\n";
        echo "Check-in: {$booking->tanggal_checkin}\n";
        echo "Check-out: {$booking->tanggal_checkout}\n";
        echo "Status: {$booking->status}\n";
        echo "---\n";
    }
}

echo "\n=== PEMESANAN untuk Single Room 3 (ID 3) ===\n";
$room3 = App\Models\Pemesanan::where('kamar_id', 3)->get();
if ($room3->count() == 0) {
    echo "Tidak ada pemesanan untuk kamar ID 3\n";
} else {
    foreach ($room3 as $b) {
        echo "Check-in: {$b->tanggal_checkin}, Check-out: {$b->tanggal_checkout}, Status: {$b->status}\n";
    }
}

echo "\n=== PEMESANAN untuk Single Room 4 (ID 4) ===\n";
$room4 = App\Models\Pemesanan::where('kamar_id', 4)->get();
if ($room4->count() == 0) {
    echo "Tidak ada pemesanan untuk kamar ID 4\n";
} else {
    foreach ($room4 as $b) {
        echo "Check-in: {$b->tanggal_checkin}, Check-out: {$b->tanggal_checkout}, Status: {$b->status}\n";
    }
}
