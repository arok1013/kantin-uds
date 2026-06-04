# Kantin UDS (Pujasera UDS)

Aplikasi web e-commerce sederhana berbasis **HTML, CSS, dan JavaScript** untuk memesan makanan dan minuman dari berbagai warung di Kantin/Pujasera Universitas Darul 'Ulum Surakarta (UDS). 

Proyek ini telah dikonversi dari versi PHP menjadi **aplikasi web statis murni** agar dapat dideploy langsung pada **GitHub Pages** (tanpa memerlukan server backend PHP/MySQL). Semua fungsionalitas login, registrasi, pengelolaan keranjang belanja, hingga simulasi pemesanan dan checkout QRIS kini berjalan sepenuhnya di sisi klien menggunakan browser **localStorage**.

## Link Live Demo (GitHub Pages)

Akses aplikasi web ini secara langsung di:
👉 **[arok1013.github.io/kantin-uds](https://arok1013.github.io/kantin-uds/)**

## Fitur Utama

- **Autentikasi Pengguna (Client-Side)**: Fitur Login dan Register yang terhubung ke penyimpanan lokal (`localStorage`). Dilengkapi dengan validasi input (kecocokan password, panjang minimal 6 karakter, dll.) serta akun admin bawaan (`username: admin`, `password: admin123`).
- **Daftar Warung & Menu**: Menjelajahi menu dengan harga dan gambar dari berbagai warung:
  - **Warung Jawa**: Rujak Cingur, Lalapan Ayam Goreng, Penyetan Komplit.
  - **Kedai Mama Zavan**: Seblak, Baso Aci, Kentang Krispy, Tahu Crispy, Enoki Crispy, Tempura Bumbu Tabur/Spicy.
  - **Warung Bu Endang**: Gado-gado, Soto Ayam.
- **Keranjang Belanja Interaktif**: Memungkinkan penambahan menu secara real-time, perbaruan kuantitas (1-10 porsi), penghapusan item individual, pengosongan keranjang, serta validasi agar seluruh item dalam keranjang berasal dari warung yang sama (Single-Restaurant Checkout).
- **Proses Checkout & QRIS Dinamis**: Halaman checkout dengan input nama pemesan, nomor meja, ringkasan belanja, dan visual QRIS dinamis yang berganti otomatis menyesuaikan warung (Saweria 1 untuk Warung Jawa, Saweria 2 untuk Kedai Mama Zavan dan Warung Bu Endang).
- **Struk Pesanan & Konfirmasi WhatsApp**: Membuat ID pesanan unik (`ORD-...`) dan ringkasan pembayaran secara instan, serta menyediakan tombol kirim konfirmasi pemesanan otomatis beserta template teks detail pesanan langsung ke nomor WhatsApp pengelola warung yang bersangkutan.
- **Desain Responsif & Estetis**: Antarmuka modern dan responsif menggunakan Vanilla CSS dengan performa cepat, micro-animation pada tombol, transisi halus, serta kompatibilitas penuh dari layar mobile hingga desktop.

## Struktur Project

```text
kantin-uds/
├── img/                       # Folder aset gambar menu dan warung (QRIS Saweria)
├── cart.html                  # Halaman detail keranjang belanja
├── checkout.html              # Halaman proses checkout dan metode pembayaran QRIS
├── index.html                 # Halaman utama (landing page / pujasera)
├── kedai-mama-zavan.html      # Menu makanan Kedai Mama Zavan
├── login.html                 # Halaman masuk pengguna
├── order_success.html         # Halaman struk pesanan & konfirmasi WhatsApp
├── register.html              # Halaman pendaftaran akun baru
├── style.css                  # Styling utama aplikasi (Vanilla CSS)
├── warung-bu-endang.html      # Menu makanan Warung Bu Endang
└── warung-jawa.html           # Menu makanan Warung Jawa
```

## Teknologi yang Digunakan

- **Frontend**: HTML5, Vanilla CSS, Font Awesome 6.4.0 (Icons)
- **Logika & Data**: JavaScript (ES6+), Web Storage API (`localStorage`)

## Cara Menjalankan Project Secara Lokal

### Cara Cepat (Direct Open)
Karena aplikasi ini adalah web statis murni, Anda cukup:
1. Unduh atau klon repositori ini ke komputer Anda.
2. Klik ganda (double click) file `index.html` untuk langsung membukanya di browser Google Chrome, Firefox, Safari, atau Edge.

### Menggunakan Web Server Lokal (Direkomendasikan)
Jika ingin menjalankan menggunakan web server lokal ringan (seperti Python atau Node.js):
- **Python 3**:
  ```bash
  python -m http.server 8000
  ```
- **Node.js (serve)**:
  ```bash
  npx serve
  ```
Lalu buka [http://localhost:8000](http://localhost:8000) atau port yang disediakan di browser Anda.

## Kontributor

- **Derik** - Developer Utama
