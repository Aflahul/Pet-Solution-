# 🐾 Aplikasi Kasir – Pet Solution Makassar

Aplikasi kasir ini berbasis web yang dikembangkan khusus untuk kebutuhan **UMKM Pet Solution (Petshop) di Makassar**.  
Sistem ini dirancang dengan fokus pada **kecepatan transaksi**, **pencarian produk instan**, dan **laporan harian cepat** sehingga mendukung operasional kasir.

---

## 🎯 Tujuan Utama

- Mempercepat proses transaksi dari **scan barcode → cetak struk** dengan latensi minimal.  
- Menyediakan **backoffice CRUD** untuk produk, kategori, stok, dan pengguna.  
- Menyediakan **laporan harian/bulanan** yang cepat tanpa beban agregasi berat real-time.  
- Tetap sederhana, offline-tolerant, dan bisa berjalan di lingkungan lokal (PC kasir + printer thermal).

---

## 🚀 Fitur Utama

### 🔐 Admin / Owner
- Kelola produk, kategori, dan stok.  
- Kelola pengguna (role: Owner, Kasir).  
- Lihat riwayat transaksi & cetak ulang struk.  
- Akses laporan omzet harian, bulanan, top produk, dan stok menipis.

### 🧾 Kasir
- Halaman POS cepat (Inertia + Vue 3) dengan **scan barcode instan**.  
- Keranjang dengan diskon, qty, pajak.  
- Checkout dengan cetak struk thermal (58/80mm).  
- Hotkeys untuk mempercepat input (Enter, +/–, Del, F2, F4).  

### 📊 Laporan
- Ringkasan omzet harian (≤ 200 ms query).  
- Rekap transaksi & jumlah item.  
- Arsip laporan dalam format PDF / ekspor CSV.  

---

## 🛠️ Teknologi

- **Backend**: [Laravel 10/11](https://laravel.com/) + MySQL (InnoDB)  
- **Frontend POS**: Inertia + Vue 3 + Tailwind (PWA + IndexedDB + Service Worker)  
- **Frontend Backoffice**: Blade + Tailwind (Livewire opsional)  
- **Cetak**: window.print() + CSS, opsi QZ Tray/ESC-POS untuk kecepatan maksimum  
- **Tools**: PHP 8.2+, XAMPP, Visual Studio Code  

---

## 📸 Screenshots

### Halaman POS
![POS](docs/screenshots/pos.png)

### Manajemen Produk
![Produk](docs/screenshots/produk.png)

### Riwayat Transaksi
![Transaksi](docs/screenshots/transaksi.png)

### Laporan Harian
![Laporan](docs/screenshots/laporan.png)


---

## 📌 Roadmap Pengembangan

- **V1.0**: POS cepat + backoffice CRUD + laporan ringkas.  
- **V1.1**: Integrasi cetak QZ Tray/ESC-POS default, manajemen shift kasir.  
- **V1.2**: PWA offline penuh, multi-device kasir.  
- **V2.0**: Multi-outlet, hosting terpusat, integrasi pembayaran non-tunai/QRIS.  

---


## 📜 Lisensi

Proyek ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).
