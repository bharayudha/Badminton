# Pengaturan Partner Badminton

Aplikasi web untuk membantu pengelola badminton mengelola pemain dan menyiapkan sesi pencarian partner atau pembagian tim.

## Persyaratan

- PHP 8.3 atau lebih baru dengan ekstensi `pdo_mysql`
- Composer
- MySQL 8 atau MariaDB yang kompatibel
- Node.js dan npm

## Menjalankan secara lokal

1. Salin `.env.example` ke `.env`, lalu isi koneksi database MySQL:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=badminton
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Buat database `badminton` di MySQL, kemudian jalankan:

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   npm install
   npm run build
   php artisan serve
   ```

3. Buka alamat yang ditampilkan oleh `php artisan serve`.
4. Untuk membuat akun contoh, atur `SEED_ADMIN_PASSWORD` pada `.env`, lalu jalankan `php artisan db:seed`. Akun admin menggunakan `SEED_ADMIN_EMAIL`; empat akun pemain contoh memakai kata sandi `password` dan hanya untuk pengembangan lokal.

Untuk pemeriksaan otomatis, jalankan `php artisan test`. Konfigurasi tes menggunakan SQLite in-memory sehingga tidak memerlukan database MySQL.

## Fitur saat ini

- Login, registrasi pemain, logout, dan pembatasan akses role.
- Dashboard ringkas dan aktivitas sesi terbaru.
- CRUD pemain untuk admin, pencarian nama, filter level, dan pagination.
- Profil pemain mandiri serta manajemen role pengguna oleh admin.
- Pembuatan sesi partner atau pembagian dua tim ganda, hasil, dan riwayat.
- Skor prioritas terpisah dari aturan pengelompokan; bobot level/pengalaman ditentukan pada setiap sesi dan harus berjumlah 100%.
- Aturan pengelompokan awal ditampilkan secara eksplisit; hasil bukan klaim pasangan optimal.

## Skema awal

- `users` menyimpan akun dan role (`admin` atau `pemain`).
- `players` menyimpan profil badminton, level pilihan pemain, dan nomor telepon.
- `player_experiences` menyimpan durasi, frekuensi bermain, serta pengalaman latihan/klub dan kompetisi.
- `pairing_sessions` dan `pairing_results` menyimpan sesi dan hasil yang dapat dikembangkan untuk Weighted Scoring + Sorting.

Bobot kriteria dan aturan pencocokan belum ditetapkan. Aplikasi tidak menilai level pemain secara otomatis.
