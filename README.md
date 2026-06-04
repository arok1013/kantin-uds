# Kantin UDS (Pujasera UDS)

Aplikasi web e-commerce sederhana berbasis PHP untuk memesan makanan dan minuman dari berbagai warung di Kantin/Pujasera Universitas Darul 'Ulum Surakarta (UDS). Aplikasi ini mendukung registrasi pengguna, pemilihan menu dari berbagai kedai/warung, keranjang belanja, hingga proses checkout pembayaran menggunakan QRIS.

## Fitur Utama

- **Autentikasi Pengguna**: Fitur Login dan Register untuk pengguna baru agar dapat melakukan transaksi.
- **Daftar Warung & Menu**: Menjelajahi menu dari berbagai warung yang tersedia:
  - **Warung Jawa**: Spesialis masakan Jawa otentik.
  - **Kedai Mama Zavan**: Menu khas Sunda modern.
  - **Warung Bu Endang**: Penyetan dan aneka jus segar.
- **Keranjang Belanja**: Menambah, mengurangi, dan menghapus pesanan secara interaktif sebelum pembayaran.
- **Metode Pembayaran QRIS**: Proses checkout dengan simulasi pembayaran digital QRIS (Saweria).
- **Desain Responsif**: Antarmuka modern dan responsif menggunakan CSS murni (Vanilla CSS) yang nyaman diakses lewat perangkat mobile maupun desktop.

## Struktur Project

```text
kantin-uds/
├── img/                  # Folder aset gambar menu dan warung
├── cart.php              # Halaman detail keranjang belanja
├── cart_handler.php      # Handler backend untuk penambahan/pengurangan item keranjang
├── checkout.php          # Halaman proses checkout dan metode pembayaran QRIS
├── empty_cart.php        # Halaman jika keranjang kosong
├── index.php             # Halaman utama (landing page)
├── kedai-mama-zavan.php  # Menu Kedai Mama Zavan
├── login.php             # Halaman login pengguna
├── logout.php            # Halaman proses logout
├── order_success.php     # Halaman setelah pesanan berhasil diselesaikan
├── register.php          # Halaman pendaftaran akun baru
├── style.css             # Styling utama aplikasi (Vanilla CSS)
├── warung-bu-endang.php  # Menu Warung Bu Endang
└── warung-jawa.php       # Menu Warung Jawa
```

## Teknologi yang Digunakan

- **Frontend**: HTML5, Vanilla CSS, Font Awesome 6.4.0 (Icons)
- **Backend**: PHP (Native Session)
- **Database**: PHP Session-based storage (untuk keranjang) & custom login handling

## Cara Menjalankan Project Secara Lokal

1. **Prasyarat**:
   Pastikan Anda sudah mengunduh dan menginstal server lokal seperti **XAMPP**, **Laragon**, atau menggunakan built-in PHP server.

2. **Kloning Repositori**:
   ```bash
   git clone https://github.com/arok1013/kantin-uds.git
   ```

3. **Pindahkan File**:
   Pindahkan folder proyek ini ke direktori root server lokal Anda (misal `htdocs` untuk XAMPP atau `www` untuk Laragon).

4. **Jalankan Aplikasi**:
   Buka browser Anda dan akses:
   ```text
   http://localhost/kantin-uds
   ```
   Atau jalankan server internal PHP dari terminal di dalam direktori proyek:
   ```bash
   php -S localhost:8000
   ```
   Lalu buka [http://localhost:8000](http://localhost:8000) di browser Anda.

## Kontributor

- **Derik** - Developer Utama
