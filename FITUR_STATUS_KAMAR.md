# Fitur Status Ketersediaan Kamar

## 📋 Deskripsi

Sistem booking kamar sekarang dilengkapi dengan fitur status ketersediaan yang jelas dan informatif. Setiap kamar memiliki status yang menunjukkan apakah kamar dapat dipesan atau tidak.

## 🎯 Status Kamar

### 1. **Tersedia (Available)**

- **Warna Badge**: Hijau (Emerald)
- **Kondisi**: Kamar dapat dipesan oleh wisatawan
- **Aksi**: Tombol "Pesan Sekarang" aktif
- **Status di Database**: `available`

### 2. **Maintenance**

- **Warna Badge**: Kuning/Amber
- **Kondisi**: Kamar sedang dalam perbaikan/perawatan
- **Aksi**: Tombol booking dinonaktifkan
- **Alert**: Muncul pesan "Kamar sedang maintenance dan belum bisa dipesan"
- **Status di Database**: `maintenance`

### 3. **Tidak Tersedia (Unavailable)**

- **Warna Badge**: Merah (Rose)
- **Kondisi**: Kamar tidak tersedia untuk pemesanan (alasan lain selain maintenance)
- **Aksi**: Tombol booking dinonaktifkan
- **Alert**: Muncul pesan "Kamar sedang tidak tersedia dan belum bisa dipesan"
- **Status di Database**: `unavailable`

## 🖥️ Implementasi di UI

### Halaman Daftar Kamar (`/kamar`)

- Badge status ditampilkan di setiap card kamar
- Warna badge berbeda sesuai status
- User dapat langsung melihat status kamar tanpa perlu membuka detail

### Halaman Detail Kamar (`/kamar/{id}`)

- Badge status ditampilkan di 2 lokasi:

  1. **Di judul kamar** - Badge besar dengan warna yang sesuai
  2. **Di card booking** - Badge kecil di pojok kanan atas card

- **Alert Box Informatif** (jika kamar tidak tersedia):

  - Icon sesuai status (wrench untuk maintenance, warning untuk unavailable)
  - Judul yang jelas
  - Pesan error yang informatif
  - Saran untuk menghubungi admin via WhatsApp

- **Form Booking**:
  - Input tanggal check-in/check-out dinonaktifkan jika kamar tidak tersedia
  - Tombol "Pesan Sekarang" disabled dengan opacity 60%
  - Tombol WhatsApp tetap aktif untuk konsultasi

### Admin Panel

- Badge status di tabel daftar kamar
- Admin dapat mengubah status kamar melalui form edit
- Status tersinkronisasi langsung dengan halaman publik

## 🔧 Penggunaan Helper Methods (Model Kamar)

```php
// Cek apakah kamar bisa dipesan
$kamar->isBookable(); // true jika status = 'available'

// Cek apakah kamar sedang maintenance
$kamar->isMaintenance(); // true jika status = 'maintenance'

// Cek apakah kamar tidak tersedia
$kamar->isUnavailable(); // true jika status = 'unavailable'
```

## 🌐 Multi-bahasa (i18n)

Status diterjemahkan otomatis sesuai bahasa yang dipilih:

### Bahasa Indonesia

- Tersedia
- Maintenance
- Tidak Tersedia

### English

- Available
- Maintenance
- Unavailable

## 📝 Cara Mengubah Status Kamar

### Via Admin Panel:

1. Login sebagai admin
2. Buka menu **Kamar**
3. Klik **Edit** pada kamar yang ingin diubah
4. Pilih status di dropdown:
   - Tersedia (available)
   - Maintenance
   - Tidak Tersedia (unavailable)
5. Simpan perubahan

### Via Database:

```sql
-- Set kamar menjadi maintenance
UPDATE kamar SET status = 'maintenance' WHERE id = 1;

-- Set kamar menjadi tersedia
UPDATE kamar SET status = 'available' WHERE id = 1;

-- Set kamar menjadi tidak tersedia
UPDATE kamar SET status = 'unavailable' WHERE id = 1;
```

## ✅ Validasi Booking

Sistem akan otomatis:

1. ✅ Mengecek status kamar saat user memilih tanggal
2. ✅ Menampilkan pesan error jika kamar tidak tersedia
3. ✅ Menonaktifkan form booking jika status bukan 'available'
4. ✅ Mencegah user membuat pemesanan untuk kamar yang tidak tersedia
5. ✅ Menampilkan pesan informatif dengan saran kontak admin

## 🎨 Komponen Reusable

**Component**: `x-status-badge`

- Otomatis mendeteksi status dan menampilkan warna yang sesuai
- Mendukung translasi otomatis
- Digunakan di seluruh aplikasi (public & admin)

**Usage**:

```blade
<x-status-badge :status="$kamar->status" />
```

## 🚀 Keuntungan Fitur Ini

1. **User Experience**: User langsung tahu kamar mana yang bisa dipesan
2. **Transparansi**: Informasi status yang jelas dan mudah dipahami
3. **Efisiensi**: Mengurangi pemesanan yang tidak perlu untuk kamar yang sedang tidak tersedia
4. **Fleksibilitas**: Admin dapat dengan mudah mengubah status kamar sesuai kebutuhan
5. **Komunikasi**: User tetap bisa bertanya via WhatsApp meski kamar tidak tersedia

## 🔒 Keamanan

- Validasi dilakukan di backend (server-side)
- Disabled attribute pada form mencegah submit manual
- Observer pattern memastikan konsistensi data
- Status kamar dicek ulang saat proses booking

---

**Dibuat**: 29 Januari 2026  
**Laravel Version**: 12.0  
**Livewire Version**: 3.x
