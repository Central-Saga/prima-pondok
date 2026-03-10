# 📅 Fitur Modal Kalender Reservasi

## 🎯 Deskripsi

Modal kalender interaktif yang memungkinkan user memilih tanggal check-in dan check-out dengan visualisasi ketersediaan kamar secara real-time.

## ✨ Fitur Utama

### 1. **Visualisasi Ketersediaan Real-time**

- Tanggal yang sudah dipesan otomatis ditandai sebagai "NOT AVAILABLE"
- Tanggal yang sudah lewat (past dates) dinonaktifkan
- Tanggal yang dipilih di-highlight dengan warna hijau
- Range check-in sampai check-out ditampilkan dengan background hijau muda

### 2. **Navigasi Kalender**

- Tombol Previous/Next untuk navigasi antar bulan
- Header menampilkan bulan dan tahun saat ini
- Grid 7x7 (Mon-Sun) dengan layout responsif

### 3. **Sistem Pemilihan Tanggal**

- Klik pertama: Pilih tanggal check-in
- Klik kedua: Pilih tanggal check-out (harus setelah check-in)
- Validasi otomatis: tidak bisa pilih tanggal yang sudah terisi
- Auto-close modal setelah kedua tanggal dipilih

### 4. **Informasi Harga Real-time**

- Setelah memilih check-in dan check-out, muncul box info:
  - Tanggal yang dipilih (formatted)
  - Total harga (IDR)
  - Jumlah malam
- Background biru muda dengan border

### 5. **Legend/Keterangan**

Badge warna untuk memudahkan user:

- 🟢 **Hijau (Emerald)**: Tanggal terpilih
- ⚪ **Abu-abu**: Tanggal sudah terisi
- ⬜ **Putih**: Tanggal tersedia

## 🖥️ Cara Penggunaan

### Untuk User/Wisatawan:

1. **Buka Halaman Detail Kamar**

   - Klik kamar yang diinginkan dari daftar

2. **Klik Tombol "Pilih Tanggal dengan Kalender"**

   - Tombol bergaris putus-putus berwarna biru
   - Icon kalender di samping teks

3. **Modal Kalender Akan Muncul**

   - Overlay gelap di background
   - Kalender di tengah layar

4. **Pilih Tanggal Check-in**

   - Klik tanggal yang tersedia (putih)
   - Tanggal akan berubah hijau dengan label "Masuk"

5. **Pilih Tanggal Check-out**

   - Klik tanggal setelah check-in
   - Range akan ter-highlight hijau muda
   - Tanggal checkout dengan label "Keluar"

6. **Modal Otomatis Tutup**
   - Setelah kedua tanggal dipilih
   - Tanggal ter-isi di form booking
   - Total harga otomatis terupdate

### Navigasi Bulan:

- **← Previous**: Mundur 1 bulan
- **Next →**: Maju 1 bulan

## 🎨 Desain UI

### Warna & Style:

```
- Background Modal: White (#ffffff)
- Backdrop: Black 50% opacity
- Selected Date: Emerald-500 (#10b981)
- Range Dates: Emerald-100 (#d1fae5)
- Booked Dates: Slate-100 (#f1f5f9)
- Available Dates: White with hover effect
- Price Box: Sky-50 background (#f0f9ff)
```

### Responsive:

- Mobile: Padding lebih kecil, grid tetap 7 kolom
- Tablet/Desktop: Max-width 2xl (672px)
- Modal centered dengan overflow scroll

## 🔧 Logika Backend

### Cek Ketersediaan:

```php
// Query pemesanan yang overlap dengan bulan yang ditampilkan
$bookings = Pemesanan::where('kamar_id', $kamar->id)
    ->whereIn('status', ['pending', 'confirmed'])
    ->where('tanggal_checkin', '<=', $endOfMonth)
    ->where('tanggal_checkout', '>=', $startOfMonth)
    ->get();

// Generate array tanggal yang terisi
foreach ($bookings as $booking) {
    $current = $booking->tanggal_checkin;
    while ($current < $booking->tanggal_checkout) {
        $bookedDates[] = $current->day;
        $current->addDay();
    }
}
```

### Validasi Tanggal:

- ✅ Tidak bisa pilih tanggal yang sudah terisi
- ✅ Tidak bisa pilih tanggal yang sudah lewat
- ✅ Check-out harus setelah check-in
- ✅ Sinkronisasi dengan form manual

## 📱 Fitur Tambahan

### 1. **Fallback Input Manual**

User tetap bisa input tanggal manual jika lebih nyaman:

```
[Pilih Tanggal dengan Kalender] <- Button modal
        atau pilih manual        <- Label
[Check-in Date] [________________]
[Check-out Date] [_______________]
```

### 2. **Real-time Calculation**

- Setiap kali tanggal dipilih, otomatis hitung:
  - Jumlah hari menginap
  - Total biaya (harga × hari)
  - Validasi overlap dengan booking lain

### 3. **Multi-language Support**

- Bahasa Indonesia & English
- Label bulan, hari, dan pesan ter-translate
- Sesuai locale yang dipilih user

## 🚀 Performance

### Optimisasi:

1. **Lazy Loading**: Modal hanya render saat dibuka
2. **Wire Key**: Livewire key untuk prevent conflict
3. **Debounce**: Input manual pakai debounce 1000ms
4. **Query Efficiency**: Hanya query booking di bulan yang ditampilkan

### Caching:

- Booked dates di-cache per bulan
- Re-load saat ganti bulan

## 🔒 Keamanan

- Server-side validation untuk semua tanggal
- Disabled state untuk tanggal tidak valid
- CSRF protection (Livewire)
- XSS prevention (Blade escaping)

## 📊 Status Indicators

### Label Tanggal:

| Status    | Label             | Warna Background | Text Color  |
| --------- | ----------------- | ---------------- | ----------- |
| Check-in  | "Masuk"/"In"      | Emerald-500      | White       |
| Check-out | "Keluar"/"Out"    | Emerald-500      | White       |
| In Range  | -                 | Emerald-100      | Emerald-800 |
| Booked    | "Terisi"/"Booked" | Slate-100        | Slate-400   |
| Available | -                 | White            | Slate-700   |
| Past      | - (strikethrough) | Slate-50         | Slate-300   |

## 🛠️ Teknologi

- **Livewire 3.x**: Reactive components
- **Alpine.js**: Client-side interactions (via Livewire)
- **Tailwind CSS 4**: Styling & responsive
- **Carbon**: Date manipulation
- **Blade**: Templating

## 📝 Event Flow

```
1. User klik "Pilih Tanggal dengan Kalender"
   ↓
2. openCalendar() dipanggil
   ↓
3. loadBookedDates() query database
   ↓
4. Modal di-render dengan data bulan ini
   ↓
5. User klik tanggal check-in
   ↓
6. selectDate(day) update $tanggal_checkin
   ↓
7. User klik tanggal check-out
   ↓
8. selectDate(day) update $tanggal_checkout
   ↓
9. closeCalendar() otomatis
   ↓
10. recalc() update harga dan validasi
```

## 🎁 Keuntungan

### Untuk User:

✅ Visual yang jelas untuk tanggal tersedia/tidak  
✅ Mudah pilih range tanggal  
✅ Langsung lihat harga total  
✅ Tidak perlu coba-coba tanggal

### Untuk Admin:

✅ Mengurangi double booking  
✅ User experience lebih baik  
✅ Less customer support

### Untuk Developer:

✅ Reusable component  
✅ Clean separation of concerns  
✅ Easy to maintain

---

**Dibuat**: 29 Januari 2026  
**Laravel Version**: 12.0  
**Livewire Version**: 3.x
