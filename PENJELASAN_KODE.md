# Penjelasan Kode GymKu

Dokumen ini menjelaskan alur, fungsi, dan logika mendalam dari setiap halaman (view) serta backend (controller) dalam proyek **GymKu** — sistem manajemen gym berbasis web menggunakan Laravel 11.

---

## 1. Halaman Publik (Tanpa Login)

### `home.blade.php`
- **Fungsi**: Halaman utama (Landing Page).
- **Logika**: Hanya merender view statis yang berisi informasi umum tentang gym. Dapat diakses oleh siapa saja tanpa perlu login.

### `auth/login.blade.php`
- **Fungsi**: Masuk ke sistem.
- **Logika**:
  - Menggunakan `Auth\LoginController@login`.
  - **Middleware Guest**: Jika sudah login, otomatis diarahkan ke dashboard sesuai role — tidak bisa mengakses halaman ini lagi.
  - **Validasi**: Email harus valid, password minimal 8 karakter.
  - **Auth::attempt()**: Laravel mencocokkan email + password dengan database secara otomatis.
  - **Session**: Melakukan `session()->regenerate()` setelah login untuk mencegah session fixation attack.
  - **Role-Based Redirect**: Setelah login berhasil, sistem mengecek `user->role`:
    - `'Admin'` → diarahkan ke `/admin/dashboard`
    - `'Member'` → diarahkan ke `/member/dashboard`

### `auth/register.blade.php`
- **Fungsi**: Pendaftaran akun member baru.
- **Logika**:
  - Menggunakan `Auth\RegisterController@register`.
  - **Middleware Guest**: Sama seperti login, user yang sudah masuk tidak bisa mengakses halaman ini.
  - **Validasi**: Nama maksimal 100 karakter, email harus unik di tabel `users`, jenis kelamin hanya `Laki-Laki` atau `Wanita`, password minimal 8 karakter dan harus dikonfirmasi (matching).
  - **Security**: Password di-hash menggunakan `Hash::make` (Bcrypt 12 rounds) sebelum disimpan ke database.
  - **Role Default**: Akun yang dibuat lewat halaman ini otomatis mendapat role `'Member'`. Admin tidak bisa mendaftar sendiri.
  - Setelah berhasil daftar, user diarahkan ke halaman **login** (bukan auto-login).

---

## 2. Halaman Member

### `member/dashboard.blade.php`
- **Fungsi**: Dashboard utama member setelah login.
- **Logika**:
  - Menggunakan `Member\DashboardController@index`.
  - Mengambil **membership aktif** milik user: `Registration WHERE id_user = user.id AND status = 'Active'` (urut terbaru).
  - Menghitung **sisa hari** membership menggunakan accessor `days_remaining` di model `Registration` — selisih antara `expiry_date` dan hari ini menggunakan Carbon.
  - Menampilkan **5 pembayaran terbaru** milik user beserta nama paket yang dibeli.

### `member/packages.blade.php`
- **Fungsi**: Katalog paket gym yang tersedia untuk dibeli.
- **Logika**:
  - Menggunakan `Member\PackageController@index`.
  - Hanya menampilkan paket dengan `status = 'Active'` menggunakan scope `Package::active()`.
  - Setiap paket dihitung jumlah pendaftarannya (`withCount registrations`, exclude status `Cancelled`) untuk menentukan **paket terpopuler**.
  - Paket non-premium dengan registrasi terbanyak mendapat badge **"Terpopuler"** di tampilan.
  - Paket diurutkan dari harga termurah.

### `member/checkout.blade.php`
- **Fungsi**: Form pembelian/pendaftaran paket.
- **Logika**:
  - Menggunakan `Member\PackageController@checkout` (GET).
  - **Cek Status**: Jika paket bukan `'Active'`, sistem akan lempar 404 (`firstOrFail()`).
  - **Cek Membership Aktif (Logika Krusial)**: Sistem mengecek apakah user sudah memiliki `Registration` dengan `status = 'Active'`. Jika ya, form tidak bisa diakses dan user diarahkan kembali ke halaman paket dengan pesan error.
  - **Extra Days**: Member bisa menambahkan hari ekstra ke durasi paket. Harga tambahan dihitung proporsional: `ceil(price / day_duration) × extra_days`.
  - **Kalkulasi di Server**: Semua perhitungan harga (totalDays, totalPrice) dilakukan ulang di server saat POST — tidak bergantung pada data dari form untuk mencegah manipulasi harga.
  - **Validasi Tanggal**: `start_date` harus hari ini atau setelahnya (`after_or_equal:today`).
  - **Metode Bayar**: Pilihan `Transfer Bank`, `Tunai`, `QRIS`, atau `E-Wallet`.

### `member/payment-success.blade.php`
- **Fungsi**: Halaman konfirmasi setelah checkout berhasil.
- **Logika**:
  - Menggunakan `Member\PackageController@paymentSuccess`.
  - Menampilkan ringkasan transaksi: nama paket, metode bayar, jumlah bayar, status.
  - **Keamanan**: Data diambil dengan filter `WHERE id_user = user.id` — user tidak bisa mengakses halaman sukses milik orang lain (akan 404).
  - **Instruksi Pembayaran Dinamis**: Berdasarkan `payment_method`, sistem generate instruksi yang berbeda:
    - `Transfer Bank` → info rekening BCA tujuan transfer
    - `Tunai` → instruksi datang ke resepsionis + Order ID
    - `QRIS` → instruksi scan QR di resepsionis
    - `E-Wallet` → instruksi kirim ke nomor gym via OVO/LinkAja/ShopeePay

### `member/payments.blade.php`
- **Fungsi**: Riwayat pembayaran member.
- **Logika**:
  - Menggunakan `Member\PaymentController@index`.
  - Mengambil semua `Payment` yang berelasi ke `Registration` milik user (`whereHas`).
  - Menampilkan status pembayaran: `Belum Lunas` (menunggu konfirmasi admin) atau `Lunas` (sudah dikonfirmasi).

### `member/progress.blade.php`
- **Fungsi**: Tracking perkembangan fisik member (berat, tinggi, lemak, massa otot).
- **Logika**:
  - Menggunakan `Member\ProgressController`.
  - **Data Terbaru (latest)**: Record progress dengan `record_date` paling baru.
  - **Data Pembanding (baseline)**: Ditentukan oleh query string `?preset=`:
    - `week` → data terdekat ≤ (tanggal terbaru − 1 minggu)
    - `month` → data terdekat ≤ (tanggal terbaru − 1 bulan)
    - `quarter` → data terdekat ≤ (tanggal terbaru − 3 bulan)
    - `year` → data terdekat ≤ (tanggal terbaru − 1 tahun)
    - `all` → data paling lama (sebagai baseline awal)
  - View menampilkan grafik perubahan dan perbandingan `latest` vs `baseline`.
  - **Keamanan Hapus**: Saat delete, query difilter `WHERE id_progress = $id AND id_user = user.id` — member tidak bisa menghapus data progress orang lain.

---

## 3. Halaman Admin

### `admin/dashboard.blade.php`
- **Fungsi**: Dashboard utama admin dengan statistik global.
- **Logika**:
  - Menggunakan `Admin\DashboardController@index`.
  - Menghitung statistik:
    - `total_members` → COUNT users WHERE role = 'Member'
    - `active_memberships` → COUNT registrations WHERE status = 'Active'
    - `expired_memberships` → COUNT registrations WHERE status = 'Expired'
    - `active_packages` → COUNT packages WHERE status = 'Active'
    - `income_today` → SUM payment.amount WHERE status = 'Lunas' AND tanggal = hari ini
    - `income_this_month` → SUM payment.amount WHERE status = 'Lunas' AND bulan ini
  - Menampilkan **5 pembayaran pending terbaru** (belum dikonfirmasi) beserta nama member dan paketnya.
  - **Grafik Pendapatan**: Data 7 hari terakhir (per hari).
  - **Grafik Pertumbuhan Member**: Data 6 bulan terakhir (per bulan).

### `admin/member.blade.php`
- **Fungsi**: Manajemen daftar seluruh member.
- **Logika**:
  - Menggunakan `Admin\MemberController`.
  - Menampilkan semua user dengan role `'Member'` beserta informasi membership terakhir mereka (nama paket, tanggal expired, status).
  - Data registrasi diambil dengan eager loading (`with`) dan diambil yang paling baru (`->first()`).
  - **Tambah Member**: Admin bisa menambah member secara langsung tanpa harus ke halaman register. Validasi sama dengan register publik (email unik, password confirmed), role otomatis `'Member'`.

### `admin/packages.blade.php`
- **Fungsi**: Manajemen paket gym (CRUD).
- **Logika**:
  - Menggunakan `Admin\PackageController`.
  - **Tambah & Edit**: Validasi nama, harga (tidak boleh negatif), durasi (minimal 1 hari), kategori premium, dan status.
  - **Hapus (Logika Pengaman)**: Sebelum menghapus, sistem mengecek apakah paket sedang digunakan di tabel `registrations`. Jika ada, penghapusan **ditolak** dengan pesan error — untuk menjaga integritas data historis member.
  - Paket diurutkan berdasarkan durasi (`day_duration ASC`).

### `admin/payments.blade.php`
- **Fungsi**: Manajemen dan konfirmasi semua pembayaran.
- **Logika**:
  - Menggunakan `Admin\PaymentController`.
  - Menampilkan semua transaksi dengan data member dan paket terkait (eager load).
  - **Konfirmasi Pembayaran (Logika Krusial)**: Saat admin klik konfirmasi (`POST /admin/payments/{id}/confirm`), sistem menggunakan **database transaction** (`DB::beginTransaction`):
    1. Update `payment.payment_status` → `'Lunas'`
    2. Update `registration.status` → `'Active'`
    3. `DB::commit()` — kedua perubahan terjadi sekaligus
    - Jika salah satu gagal → `DB::rollBack()` — tidak ada data setengah-setengah
  - **Grafik**: Pendapatan 12 bulan terakhir dan breakdown per metode pembayaran.

---

## 4. Logika Backend (Controllers)

| Controller | Fungsi Utama |
|---|---|
| `Auth\LoginController` | Autentikasi login, redirect berdasarkan role, logout |
| `Auth\RegisterController` | Registrasi member baru, hash password |
| `Admin\DashboardController` | Statistik global gym, data grafik |
| `Admin\MemberController` | CRUD member oleh admin |
| `Admin\PackageController` | CRUD paket, validasi sebelum hapus |
| `Admin\PaymentController` | List pembayaran, konfirmasi dengan DB transaction |
| `Member\DashboardController` | Data dashboard member (membership aktif, sisa hari) |
| `Member\PackageController` | Katalog paket, proses checkout, instruksi bayar |
| `Member\PaymentController` | Riwayat pembayaran member |
| `Member\ProgressController` | CRUD tracking fisik, logika perbandingan preset |

---

## 5. Middleware

### `AdminMiddleware.php`
- **Fungsi**: Memproteksi semua route `/admin/*`.
- **Logika**: Mengecek `auth()->check() && auth()->user()->role === 'Admin'`. Jika bukan admin, diarahkan ke halaman `/` dengan pesan error.

### `MemberMiddleware.php`
- **Fungsi**: Memproteksi semua route `/member/*`.
- **Logika**: Mengecek `auth()->check() && auth()->user()->role === 'Member'`. Jika bukan member, diarahkan ke halaman `/` dengan pesan error.

---

## 6. Model & Relasi Database

### Relasi Antar Tabel
- **User** `hasMany` **Registration** (satu user bisa daftar berkali-kali)
- **User** `hasMany` **Progress** (satu user bisa punya banyak record fisik)
- **Package** `hasMany` **Registration** (satu paket bisa dibeli banyak member)
- **Registration** `belongsTo` **User** & **Package**
- **Registration** `hasMany` **Payment** (satu registrasi bisa punya banyak pembayaran)

### Fitur Khusus Model

**`Registration.php`** — Accessor `days_remaining`:
```php
// Otomatis menghitung sisa hari dari expiry_date ke hari ini
// Menggunakan Carbon::diffInDays()
// Mengembalikan 0 jika membership tidak aktif atau sudah expired
$registration->days_remaining; // contoh: 14
```

**`Payment.php`** — Query Scopes:
```php
Payment::paid()    // WHERE payment_status = 'Lunas'
Payment::pending() // WHERE payment_status = 'Belum Lunas'
```

**`Package.php`** — Query Scope:
```php
Package::active() // WHERE status = 'Active'
```

---

## 7. File Penting Lainnya

- **`routes/web.php`**: Definisi semua URL, dikelompokkan berdasarkan middleware (`guest`, `auth + admin`, `auth + member`).
- **`app/Models/`**: Definisi relasi antar tabel menggunakan Eloquent ORM Laravel.
- **`.env`**: Konfigurasi koneksi database MySQL, nama aplikasi, dan pengaturan environment.
- **`database/migrations/`**: Struktur tabel database (users, packages, registrations, payments, progress).
- **`database/seeders/`**: Data awal untuk testing (UserSeeder, PackageSeeder, dll).

---

## 8. Status Data (Enum)

| Tabel | Kolom | Nilai yang Valid |
|---|---|---|
| `users` | `role` | `'Member'`, `'Admin'` |
| `users` | `gender` | `'Laki-Laki'`, `'Wanita'` |
| `packages` | `status` | `'Active'`, `'Inactive'` |
| `registrations` | `status` | `'Pending'`, `'Active'`, `'Expired'`, `'Cancelled'` |
| `payments` | `payment_status` | `'Belum Lunas'`, `'Lunas'` |
| `payments` | `payment_method` | `'Transfer Bank'`, `'Tunai'`, `'QRIS'`, `'E-Wallet'` |

---

## 9. Alur Pembelian Paket (End-to-End)

```
[1] Member buka /member/packages
      → Lihat daftar paket aktif + badge terpopuler

[2] Klik "Beli" → /member/checkout?id={pkg_id}&extra_days={N}
      → Sistem cek: apakah member sudah punya membership aktif?
        ❌ Sudah ada → redirect balik + error
        ✅ Belum ada → tampilkan form checkout

[3] Member isi form: tanggal mulai + metode bayar → Submit
      → Validasi semua input di server
      → Hitung ulang harga di server
      → DB Transaction:
          CREATE registration (status='Pending')
          CREATE payment (status='Belum Lunas')
      → Commit

[4] Redirect ke /member/payment-success/{id}
      → Tampilkan instruksi pembayaran sesuai metode

[5] Admin buka /admin/payments → lihat pembayaran 'Belum Lunas'

[6] Admin klik Konfirmasi → POST /admin/payments/{id}/confirm
      → DB Transaction:
          payment.status       = 'Lunas'
          registration.status  = 'Active'
      → Commit

[7] Membership member AKTIF ✅
      → Member bisa lihat sisa hari di /member/dashboard
```

---

## 10. Keamanan Aplikasi

| Mekanisme | Penjelasan |
|---|---|
| **Password Hashing** | `Hash::make()` — Bcrypt dengan 12 rounds |
| **Database Transaction** | Konfirmasi pembayaran atomik (gagal satu, semua dibatalkan) |
| **Ownership Check** | Delete progress difilter `AND id_user = user.id` |
| **Server-Side Calculation** | Harga checkout dihitung ulang di server, tidak dari form |
| **Session Regenerate** | Setelah login untuk cegah session fixation |
| **Middleware Role** | Admin & Member tidak bisa saling akses halaman satu sama lain |
| **Unique Email** | Validasi `unique:users,email` saat register & tambah member |
