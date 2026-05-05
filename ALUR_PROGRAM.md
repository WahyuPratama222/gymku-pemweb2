# 📋 Dokumentasi Alur Program — GymKu

> Aplikasi manajemen gym berbasis web menggunakan **Laravel 11** dengan dua peran pengguna: **Admin** dan **Member**.

---

## 🗂️ Daftar Isi

1. [Struktur Aplikasi](#struktur-aplikasi)
2. [Database & Model](#database--model)
3. [Middleware & Keamanan](#middleware--keamanan)
4. [Alur Autentikasi](#alur-autentikasi)
5. [Alur Admin](#alur-admin)
6. [Alur Member](#alur-member)
7. [Diagram Alur Lengkap](#diagram-alur-lengkap)

---

## 🏗️ Struktur Aplikasi

```
gymku-pemweb2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── MemberController.php
│   │   │   │   ├── PackageController.php
│   │   │   │   └── PaymentController.php
│   │   │   └── Member/
│   │   │       ├── DashboardController.php
│   │   │       ├── PackageController.php
│   │   │       ├── PaymentController.php
│   │   │       └── ProgressController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       └── MemberMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Package.php
│       ├── Registration.php
│       ├── Payment.php
│       └── Progress.php
├── resources/views/
│   ├── home.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── member.blade.php
│   │   ├── packages.blade.php
│   │   └── payments.blade.php
│   └── member/
│       ├── dashboard.blade.php
│       ├── packages.blade.php
│       ├── checkout.blade.php
│       ├── payment-success.blade.php
│       ├── payments.blade.php
│       └── progress.blade.php
└── routes/
    └── web.php
```

---

## 🗄️ Database & Model

### Tabel & Relasi

```
users ──────────────────────────────────────────────────┐
  id_user (PK)                                           │
  name, email, gender, password                          │
  role: enum('Member', 'Admin')                          │
                                                         │
         hasMany ↓                  hasMany ↓            │
                                                         │
registrations ◄──── id_user ────────────────────────────┘
  id_registration (PK)
  id_user (FK) → users
  id_package (FK) → packages
  start_date, expiry_date
  status: enum('Pending', 'Active', 'Expired', 'Cancelled')

         hasMany ↓                  belongsTo ↑

payments ◄──── id_registration
  id_payment (PK)
  id_registration (FK) → registrations
  payment_method: enum('Transfer Bank', 'Tunai', 'QRIS', 'E-Wallet')
  payment_status: enum('Belum Lunas', 'Lunas')
  amount, payment_date

packages
  id_package (PK)
  name, price, day_duration
  is_premium: boolean
  status: enum('Active', 'Inactive')

progress ◄──── id_user
  id_progress (PK)
  id_user (FK) → users
  record_date, weight, height
  body_fat (nullable), muscle_mass (nullable)
```

### Model — Fitur Penting

| Model | Fitur Khusus |
|-------|-------------|
| `User` | Method `isAdmin()` dan `isMember()` untuk cek role |
| `Registration` | Accessor `days_remaining` — otomatis hitung sisa hari |
| `Payment` | Scope `paid()` (Lunas) dan `pending()` (Belum Lunas) |
| `Package` | Scope `active()` untuk filter paket aktif |

---

## 🔐 Middleware & Keamanan

### Dua Middleware Custom

**`AdminMiddleware`**
```
Request masuk → Cek auth()->check() && role === 'Admin'
  ✅ Lolos → lanjut ke controller
  ❌ Gagal → redirect ke / (home) dengan pesan error
```

**`MemberMiddleware`**
```
Request masuk → Cek auth()->check() && role === 'Member'
  ✅ Lolos → lanjut ke controller
  ❌ Gagal → redirect ke / (home) dengan pesan error
```

### Kelompok Route

| Kelompok | Prefix | Middleware | Akses |
|----------|--------|------------|-------|
| Public | `/` | — | Semua orang |
| Auth | `/login`, `/register` | `guest` | Tamu saja (belum login) |
| Admin | `/admin/*` | `auth` + `admin` | Admin saja |
| Member | `/member/*` | `auth` + `member` | Member saja |

---

## 🔑 Alur Autentikasi

### Halaman: `/` (Home)
- **View:** `home.blade.php`
- **Logic:** Halaman publik, tidak ada middleware
- Menampilkan landing page dengan tombol Login/Register

---

### Halaman: `/login`
- **Controller:** `Auth\LoginController`
- **View:** `auth/login.blade.php`

**Alur GET (tampilkan form):**
```
User buka /login
  → Cek Auth::check()
    ✅ Sudah login → redirect ke dashboard sesuai role
    ❌ Belum login → tampilkan form login
```

**Alur POST (proses login):**
```
User submit form
  → Validasi: email (required|email), password (required|min:8)
    ❌ Gagal validasi → kembali ke form + tampilkan error
  → Auth::attempt(['email', 'password'])
    ❌ Gagal → kembali dengan error "Email atau password salah"
    ✅ Berhasil:
        → session()->regenerate()
        → Cek role user:
            role === 'Admin' → redirect /admin/dashboard
            role === 'Member' → redirect /member/dashboard
```

---

### Halaman: `/register`
- **Controller:** `Auth\RegisterController`
- **View:** `auth/register.blade.php`

**Alur POST (proses registrasi):**
```
User submit form
  → Validasi:
      name     : required|string|max:100
      email    : required|email|unique:users|max:100
      gender   : required|in:Laki-Laki,Wanita
      password : required|min:8|confirmed
    ❌ Gagal → kembali ke form + error
  → User::create() dengan password di-hash (Hash::make)
  → Role default otomatis = 'Member'
  → redirect /login + pesan sukses "Akun berhasil dibuat!"
```

---

## 👑 Alur Admin

### Halaman: `/admin/dashboard`
- **Controller:** `Admin\DashboardController@index`
- **View:** `admin/dashboard.blade.php`

**Data yang dikirim ke view:**

```
summary = {
  total_members        : COUNT users WHERE role='Member'
  active_memberships   : COUNT registrations WHERE status='Active'
  expired_memberships  : COUNT registrations WHERE status='Expired'
  active_packages      : COUNT packages WHERE status='Active'
  income_today         : SUM payments.amount WHERE status='Lunas' AND DATE=today
  income_this_month    : SUM payments.amount WHERE status='Lunas' AND bulan ini
}

pendingPayments = 5 payment terakhir dengan status 'Belum Lunas'
  → eager load: registration → user, registration → package

chartData = {
  revenue     : pendapatan 7 hari terakhir (per hari)
  memberGrowth: jumlah member baru 6 bulan terakhir (per bulan)
}
```

---

### Halaman: `/admin/members`
- **Controller:** `Admin\MemberController`
- **View:** `admin/member.blade.php`

**Alur GET (tampilkan daftar member):**
```
Ambil semua User WHERE role='Member'
  → eager load registrations (urut terbaru) + package
  → map() tiap user → ambil registrasi terbaru (->first())
  → Tampilkan: nama, email, gender, tanggal daftar,
               status membership, nama paket, tanggal expired
```

**Alur POST (tambah member baru oleh admin):**
```
Admin isi form tambah member
  → Validasi: name, email (unique), gender, password (confirmed)
    ❌ Gagal → kembali + error
  → User::create() dengan role='Member' + password di-hash
  → redirect /admin/members + sukses
```

---

### Halaman: `/admin/packages`
- **Controller:** `Admin\PackageController`
- **View:** `admin/packages.blade.php`

**Alur GET:** Ambil semua paket diurutkan `day_duration ASC`

**Alur POST (tambah paket):**
```
Validasi: name, price (numeric|min:0), day_duration (integer|min:1),
          is_premium (boolean), status (Active|Inactive)
  ❌ Gagal → balik + error
  ✅ Package::create() → redirect + sukses
```

**Alur PUT (edit paket):**
```
Package::findOrFail($id)
  → Validasi sama seperti tambah
  → $package->update([...])
  → redirect + sukses
```

**Alur DELETE (hapus paket):**
```
Package::findOrFail($id)
  → Cek apakah ada Registration yang pakai paket ini:
      Registration::where('id_package', $id)->count()
    ❌ Ada yang pakai → redirect + error "Paket masih digunakan member"
    ✅ Tidak ada → $package->delete() → redirect + sukses
```

---

### Halaman: `/admin/payments`
- **Controller:** `Admin\PaymentController`
- **View:** `admin/payments.blade.php`

**Alur GET:**
```
Ambil semua Payment + eager load registration → user, package
  → urut payment_date DESC
  → map() → bentuk object untuk view

chartData = {
  monthlyRevenue : pendapatan 12 bulan terakhir (per bulan)
  paymentMethods : breakdown metode pembayaran (Transfer/Tunai/QRIS/E-Wallet)
}
```

**Alur POST `/admin/payments/{id}/confirm` (konfirmasi pembayaran):**
```
DB::beginTransaction()
  → Payment::findOrFail($id) + load registration
  → payment->update(['payment_status' => 'Lunas'])
  → payment->registration->update(['status' => 'Active'])
DB::commit()
  ✅ Sukses → redirect + "Membership diaktifkan"
  ❌ Exception → DB::rollBack() → redirect + error
```

> ⚠️ Konfirmasi pembayaran menggunakan **database transaction** untuk menjamin konsistensi: status payment dan registration diupdate secara atomik (dua-duanya berhasil atau dua-duanya dibatalkan).

---

## 👤 Alur Member

### Halaman: `/member/dashboard`
- **Controller:** `Member\DashboardController@index`
- **View:** `member/dashboard.blade.php`

**Data yang dikirim:**
```
activeMembership = Registration WHERE id_user=user.id AND status='Active'
                   (dengan eager load package, urut terbaru)

daysRemaining    = activeMembership->days_remaining
                   (Accessor di model: hitung selisih hari sampai expiry_date)

recentPayments   = 5 payment terbaru milik user ini
```

---

### Halaman: `/member/packages`
- **Controller:** `Member\PackageController@index`
- **View:** `member/packages.blade.php`

**Alur:**
```
Ambil semua Package WHERE status='Active'
  → withCount registrations (exclude Cancelled)
  → urut price ASC

Tentukan mostPopularNonPremiumId:
  → Package aktif, non-premium, dengan registrations_count terbanyak
  → Digunakan view untuk memberi badge "Terpopuler"
```

---

### Halaman: `/member/checkout` (GET)
- **Controller:** `Member\PackageController@checkout`
- **View:** `member/checkout.blade.php`

**Alur:**
```
Ambil ?id=<package_id> dan ?extra_days=<N> dari query string

Package::where('id_package', $id)->where('status', 'Active')->firstOrFail()

Hitung:
  pricePerDay = ceil(price / day_duration)
  totalDays   = day_duration + extra_days
  totalPrice  = price + (extra_days × pricePerDay)

Cek membership aktif:
  Registration WHERE id_user=user.id AND status='Active'
    ❌ Sudah punya aktif → redirect /member/packages + error
    ✅ Tidak ada → tampilkan halaman checkout
```

---

### Halaman: `/member/checkout` (POST — Proses Pembelian)
- **Controller:** `Member\PackageController@processCheckout`

**Alur lengkap:**
```
Validasi input:
  id_package    : required|exists:packages
  extra_days    : required|integer|min:0
  start_date    : required|date|after_or_equal:today
  payment_method: required|in:Transfer Bank,Tunai,QRIS,E-Wallet
  ❌ Gagal → back() + error

Hitung ulang di server:
  pricePerDay = ceil(price / day_duration)
  totalDays   = day_duration + extra_days
  totalPrice  = price + (extra_days × pricePerDay)
  expiryDate  = startDate + totalDays hari

DB::beginTransaction()
  → Registration::create([
        id_user, id_package,
        start_date, expiry_date,
        status = 'Pending'       ← belum aktif sampai pembayaran dikonfirmasi admin
    ])
  → Payment::create([
        id_registration,
        payment_method,
        payment_status = 'Belum Lunas',
        amount = totalPrice
    ])
DB::commit()
  ✅ redirect /member/payment-success/{id_registration}
  ❌ rollBack() → back() + error sistem
```

---

### Halaman: `/member/payment-success/{id}`
- **Controller:** `Member\PackageController@paymentSuccess`
- **View:** `member/payment-success.blade.php`

**Alur:**
```
Registration::with(['package', 'payment'])
  WHERE id_registration=$id AND id_user=user.id
  → firstOrFail() (404 jika bukan punya user ini)

Generate instruksi pembayaran berdasarkan payment_method:
  'Transfer Bank' → instruksi transfer BCA
  'Tunai'         → instruksi datang ke resepsionis
  'QRIS'          → instruksi scan QR
  'E-Wallet'      → instruksi OVO/LinkAja/ShopeePay
```

---

### Halaman: `/member/payments`
- **Controller:** `Member\PaymentController@index`
- **View:** `member/payments.blade.php`

**Alur:**
```
Ambil semua Payment milik user ini:
  Payment::whereHas('registration', fn → where('id_user', user.id))
  → dengan eager load registration.package
  → urut payment_date DESC
```

---

### Halaman: `/member/progress`
- **Controller:** `Member\ProgressController`
- **View:** `member/progress.blade.php`

**Alur GET (tampilkan progress):**
```
Ambil query string: ?showAll=1 dan ?preset=week|month|quarter|year|all

Progress::where('id_user', user.id)->orderBy('record_date', 'desc')->get()

latest   = data progress terbaru (->first())
baseline = data pembanding berdasarkan preset:
  'week'    → cari data ≤ (latest_date - 1 minggu)
  'month'   → cari data ≤ (latest_date - 1 bulan)
  'quarter' → cari data ≤ (latest_date - 3 bulan)
  'year'    → cari data ≤ (latest_date - 1 tahun)
  'all'     → data paling lama (->last())

View menampilkan grafik perkembangan dan perbandingan latest vs baseline
```

**Alur POST (tambah record):**
```
Validasi:
  record_date  : required|date
  weight       : required|numeric|min:0|max:500
  height       : required|numeric|min:0|max:300
  body_fat     : nullable|numeric|min:0|max:100
  muscle_mass  : nullable|numeric|min:0|max:500

Progress::create([id_user, record_date, weight, height, body_fat, muscle_mass])
→ redirect /member/progress + sukses
```

**Alur DELETE:**
```
Progress::where('id_progress', $id)->where('id_user', user.id)->firstOrFail()
  → Pastikan hanya bisa hapus data miliknya sendiri (keamanan)
  → $progress->delete()
  → redirect + sukses
```

---

## 📊 Diagram Alur Lengkap

```
                    ┌─────────────────┐
                    │   Buka Website  │
                    │    GET /        │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │   home.blade    │
                    │ (Landing Page)  │
                    └────────┬────────┘
                             │
              ┌──────────────┼──────────────┐
              │                             │
    ┌─────────▼──────┐           ┌──────────▼──────┐
    │  GET /login    │           │  GET /register  │
    │ (middleware:   │           │ (middleware:     │
    │   guest)       │           │   guest)        │
    └────────┬───────┘           └──────────┬──────┘
             │                              │
    POST /login                    POST /register
             │                              │
     Auth::attempt()               User::create()
             │                     role = 'Member'
             │                              │
             └──────────┬───────────────────┘
                        │
                 Cek role user
                        │
          ┌─────────────┴─────────────┐
          │                           │
    role='Admin'                role='Member'
          │                           │
   /admin/dashboard            /member/dashboard
          │                           │
    ┌─────┴──────┐           ┌────────┴────────┐
    │            │           │                 │
 /members    /packages    /packages        /progress
 /payments               /checkout
                          /payments
```

### Alur Pembelian Paket (Detail)

```
Member pilih paket
        │
        ▼
GET /member/checkout?id={pkg_id}&extra_days={N}
  → Cek membership aktif?
        │
   Ada aktif ──────────────────────► Redirect + error
        │
   Tidak ada
        │
        ▼
Tampilkan form checkout
(pilih tanggal mulai + metode bayar)
        │
        ▼
POST /member/checkout
  → Validasi input
  → Hitung harga + tanggal expired
  → DB Transaction:
      CREATE registration (status=Pending)
      CREATE payment (status=Belum Lunas)
  → Commit
        │
        ▼
GET /member/payment-success/{id}
(tampilkan instruksi bayar sesuai metode)
        │
        ▼
Admin cek daftar pembayaran
POST /admin/payments/{id}/confirm
  → DB Transaction:
      payment.status  = 'Lunas'
      registration.status = 'Active'
  → Commit
        │
        ▼
Membership member AKTIF ✅
```

---

## 📝 Catatan Penting

| Hal | Keterangan |
|-----|------------|
| **Hashing password** | Menggunakan `Hash::make()` (Bcrypt, 12 rounds) |
| **Transaksi DB** | Konfirmasi pembayaran & checkout pakai `DB::beginTransaction()` |
| **Keamanan delete** | Progress hanya bisa dihapus oleh pemiliknya (`where id_user = user.id`) |
| **Status Membership** | `Pending` → setelah daftar, `Active` → setelah admin konfirmasi |
| **Paket tidak bisa dihapus** | Jika sudah dipakai di registrasi manapun |
| **Middleware guest** | Halaman login/register otomatis redirect jika sudah login |
| **Extra days** | Member bisa tambah hari ke paket dengan harga proporsional (harga/hari) |
