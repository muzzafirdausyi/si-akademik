# SI Akademik — Acara 5–9 (BKPM Workshop SI Web Server)

Aplikasi PHP MVC sederhana (tanpa framework) yang membangun bertahap sesuai
BKPM: routing manual, middleware auth, database + PDO CRUD, dan refactor OOP
dengan dependency injection.

## Cara Menjalankan (XAMPP / Laragon)

1. Salin folder `si-akademik` ini ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Buat database:
   - Buka phpMyAdmin → Import → pilih file `database/si_akademik.sql`.
   - Ini akan membuat database `si_akademik`, 3 tabel (prodi, mahasiswa,
     matakuliah), dan data awal.
3. Cek `config/database.php` — sesuaikan `username`/`password` MySQL jika perlu
   (default XAMPP: user `root`, password kosong).
4. **Jadikan folder `public/` sebagai document root**, salah satu caranya:
   - Laragon: klik kanan project → `Set as web root` mengarah ke `public/`, atau
   - Virtual host Apache dengan `DocumentRoot .../si-akademik/public`.
   - Alternatif cepat untuk testing: jalankan PHP built-in server:
     ```
     cd si-akademik/public
     php -S localhost:8000
     ```
     lalu buka `http://localhost:8000`.
5. Login dengan akun demo (hardcode, lihat `AuthController`):
   - Username: `admin`
   - Password: `admin123`

Jika project diletakkan di subfolder (misalnya `http://localhost/si-akademik/public`
tanpa virtual host), isi `base_path` di `config/app.php` menjadi
`'/si-akademik/public'`.

## Pemetaan ke Setiap Acara

### Acara 5 — Routing, .htaccess, Request & Response
- `public/.htaccess` — redirect semua request non-file/folder ke `index.php`.
- `public/index.php` — front controller: parse URI & method, lalu dispatch.
- `app/Core/Router.php` — array routing + dukungan parameter `{id}`.
- `routes/web.php` — daftar route GET/POST.
- **Tugas mandiri**: dukungan parameter URL (`/mahasiswa/{id}/edit`) sudah
  diimplementasikan lewat regex sederhana di `Router::match()`.

### Acara 6 — Middleware, Auth Sederhana, Struktur Folder Lengkap
- `app/Core/Middleware/AuthMiddleware.php` — cek session sebelum lanjut ke
  Controller.
- `app/Controllers/AuthController.php` — `loginForm`, `login`, `logout`
  (kredensial masih hardcode sesuai instruksi Langkah #4).
- `routes/web.php` — `$router->middleware([...], [AuthMiddleware::class])`
  melindungi `/dashboard`, `/mahasiswa`, `/prodi`, `/matakuliah`.
- **Tugas mandiri**: flash message "Selamat datang, Admin" setelah login dan
  "Anda telah logout" setelah logout — lihat `Controller::flash()` dan
  `Views/home/dashboard.php`.

### Acara 7 — Database, CREATE TABLE, dan Model Dasar
- `database/si_akademik.sql` — 3 tabel + data awal (`prodi`, `mahasiswa`,
  `matakuliah`), lengkap dengan `FOREIGN KEY`.
- `config/database.php` — kredensial koneksi.
- **Tugas mandiri**: kolom `status ENUM('aktif','cuti','lulus')` pada tabel
  `mahasiswa` sudah ditambahkan dengan default `'aktif'`.

### Acara 8 — PDO, Prepared Statement, Relasi, CRUD Lengkap
- `app/Core/Database.php` — singleton PDO dengan `ERRMODE_EXCEPTION` dan
  `EMULATE_PREPARES => false`.
- `app/Repositories/*.php` — semua query pakai prepared statement (`:named`
  parameter), termasuk `JOIN` mahasiswa↔prodi dan matakuliah↔prodi.
- `app/Controllers/*Controller.php` — CRUD penuh (index, create, store, edit,
  update, destroy) untuk Mahasiswa, Prodi, dan Matakuliah.
- **Tugas mandiri**: fitur pencarian di `/mahasiswa?q=...` menggunakan `LIKE`
  dengan prepared statement — lihat `MahasiswaRepository::search()`.

### Acara 9 — OOP Lanjutan: Object Composition, Dependency Injection, Getter/Setter
- `app/Models/Mahasiswa.php` (juga Prodi & Matakuliah) — semua atribut
  `private`, diakses lewat getter, dan setter melakukan validasi (NIM harus
  angka, nama tidak boleh kosong, email valid, dll — melempar
  `InvalidArgumentException` jika salah).
- `app/Repositories/MahasiswaRepository.php` — menerima `PDO` lewat
  **constructor injection**, tidak membuat koneksi sendiri.
- `app/Controllers/MahasiswaController.php` — menerima/membentuk
  `MahasiswaRepository` di constructor (Controller → Repository → Database:
  rantai dependency yang jelas dan testable).

## Struktur Folder

Mengikuti struktur lengkap di Acara 6:

```
si-akademik/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Views/
│   └── Core/
│       └── Middleware/
├── config/
├── database/
├── public/
├── routes/
└── storage/logs/
```

## Catatan untuk Laporan

Setiap Acara di BKPM meminta laporan PDF terpisah (format:
`ACARA-X_GOL_NIM_NAMA.pdf`) berisi Cover, Pendahuluan, Hasil Praktik,
Kesimpulan, dan Daftar Pustaka. Gunakan source code di sini sebagai bahan
"Hasil Praktik" — screenshot halaman yang berjalan (login, dashboard, CRUD
mahasiswa/prodi/matakuliah) untuk melengkapi laporan tiap acara.
