# Simple POS - Kelompok 02 (SIB 3E)

Aplikasi Point of Sale (POS) berbasis web menggunakan Laravel 11 dengan sistem otorisasi dan kontrol akses berbasis peran (*Role-Based Access Control*).

---

## Akun Uji Coba (Demo Accounts)

Data akun berikut telah disediakan melalui database seeder (`DemoUserSeeder`):

| Peran (Role) | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@pos.test` | `password` | Mengakses Kasir, Transaksi, serta mengelola Produk & Kategori |
| **Kasir** | `kasir@pos.test` | `password` | Mengakses Kasir & melihat riwayat Transaksi (terbatas dari menu Admin) |
| **Guest (Tamu)** | *(Tanpa Login)* | - | Mengakses halaman Login (`/login`) & Halaman Informasi (`/info`) |

---

## Skenario Uji Manual Berdasarkan Peran

Berikut adalah skenario pengujian manual untuk setiap peran yang ada pada aplikasi:

### 1. Skenario Uji: Peran Admin (`role: admin`)
* **Tujuan**: Memastikan pengguna dengan peran Admin memiliki akses penuh ke seluruh fitur aplikasi, termasuk manajemen master data (Kategori & Produk).
* **Akun Uji**: `admin@pos.test` / `password`

**Langkah-langkah:**
1. Buka browser dan akses halaman login di `http://localhost:8000/login`.
2. Masukkan email `admin@pos.test` dan password `password`, kemudian klik tombol **Login**.
3. Periksa bilah navigasi (navbar) di bagian atas layar:
   - Periksa identitas pengguna yang ditampilkan pada sudut kanan navbar.
   - Periksa ketersediaan tautan menu navigasi.
4. Klik menu **Produk** (`/products`) atau buka halaman tambah produk di `/products/create`.
5. Klik menu **Kategori** (`/categories`) atau coba tambahkan kategori baru.
6. Klik menu **Kasir** (`/pos`) dan lakukan simulasi transaksi.
7. Klik tombol **Keluar** untuk mengakhiri sesi.

**Hasil yang Diharapkan:**
- Pengguna berhasil login dan diarahkan ke halaman kasir POS (`/pos`).
- Bilah navigasi menampilkan teks identitas `Admin Kafe (admin)`.
- Semua menu navigasi ditampilkan: **Kasir**, **Transaksi**, **Produk**, dan **Kategori**.
- Halaman `/products` dan `/categories` dapat diakses dengan status HTTP 200 (tanpa error 403 Forbidden).
- Admin dapat melakukan operasi pengelolaan produk dan kategori secara normal.
- Sesi berhasil diakhiri setelah menekan tombol Keluar dan pengguna kembali ke halaman login.

---

### 2. Skenario Uji: Peran Kasir (`role: kasir`)
* **Tujuan**: Memastikan pengguna dengan peran Kasir dapat mengakses transaksi kasir, namun **dibatasi dan ditolak** ketika mencoba mengakses halaman manajemen admin (Produk & Kategori).
* **Akun Uji**: `kasir@pos.test` / `password`

**Langkah-langkah:**
1. Buka browser dan akses halaman login di `http://localhost:8000/login`.
2. Masukkan email `kasir@pos.test` dan password `password`, kemudian klik tombol **Login**.
3. Periksa bilah navigasi (navbar) di bagian atas layar:
   - Periksa identitas pengguna yang ditampilkan.
   - Periksa ketersediaan tautan menu navigasi.
4. Lakukan pembuatan transaksi pada halaman **Kasir** (`/pos`) dan periksa riwayat pada halaman **Transaksi** (`/transactions`).
5. Coba akses rute manajemen produk secara langsung melalui address bar browser: ketik `http://localhost:8000/products` lalu tekan Enter.
6. Coba akses rute manajemen kategori secara langsung melalui address bar browser: ketik `http://localhost:8000/categories` lalu tekan Enter.
7. Klik tombol **Keluar** untuk mengakhiri sesi.

**Hasil yang Diharapkan:**
- Pengguna berhasil login dan diarahkan ke halaman kasir POS (`/pos`).
- Bilah navigasi menampilkan teks identitas `Kasir Kafe (kasir)`.
- Bilah navigasi **hanya** menampilkan menu **Kasir** dan **Transaksi**. Menu **Produk** dan **Kategori** tidak muncul (disembunyikan).
- Halaman `/pos` dan `/transactions` dapat diakses dan digunakan secara normal untuk transaksi.
- Saat mengakses `http://localhost:8000/products` atau `http://localhost:8000/categories`, sistem menolak akses dengan menampilkan error **HTTP 403 Forbidden** bertuliskan:  
  `"Halaman ini hanya untuk peran admin."`
- Rute admin berhasil dilindungi oleh middleware `role:admin`.

---

### 3. Skenario Uji: Peran Guest / Tamu (`guest`)
* **Tujuan**: Memastikan pengguna yang belum login tidak dapat mengakses halaman transaksi atau manajemen, serta hanya diizinkan mengakses halaman publik/tamu.
* **Akun Uji**: Pengguna belum terautentikasi (gunakan tab *Incognito/Private Browsing*).

**Langkah-langkah:**
1. Buka tab baru dalam mode *Incognito* / *Private Browsing* (kondisi belum login).
2. Coba akses halaman kasir `http://localhost:8000/pos` atau riwayat transaksi `http://localhost:8000/transactions`.
3. Coba akses halaman manajemen produk `http://localhost:8000/products` atau kategori `http://localhost:8000/categories`.
4. Buka halaman informasi tamu di `http://localhost:8000/info`.
5. Buka halaman login di `http://localhost:8000/login`.

**Hasil yang Diharapkan:**
- Setiap upaya mengakses halaman `/pos`, `/transactions`, `/products`, maupun `/categories` akan secara otomatis dialihkan (*redirect*) ke halaman `/login`.
- Halaman publik seperti `/info` dan `/login` dapat diakses dengan sukses tanpa hambatan autentikasi.

---

## Langkah 6: Pemilihan Format Ekspor untuk 100.000 Transaksi

### Pertanyaan:
> Untuk laporan 100.000 transaksi, format mana yang kalian pilih (CSV, Excel, atau PDF), dan kenapa?

### Jawaban:
Untuk menangani ekspor data laporan skala besar sebanyak **100.000 transaksi**, format yang kami pilih adalah **CSV (Comma-Separated Values)**.

### Alasan Pemilihan:

1. **Efisiensi Penggunaan Memori (Memory Streaming & Footprint O(1)):**
   * Ekspor CSV dapat memanfaatkan fitur streaming Laravel (`response()->streamDownload()`) dipadukan dengan generator Eloquent seperti `Transaction::lazyById()`.
   * Data dibaca dari database secara bertahap dalam potongan (*chunk*) kecil dan langsung ditulis baris per baris ke aliran output (`php://output`).
   * Server **tidak perlu** menampung 100.000 objek model sekaligus di memori RAM, sehingga konsumsi memori puncak (*peak memory*) tetap sangat rendah dan stabil, terhindar dari *fatal error* `Allowed memory size exhausted`.

2. **Performa Kecepatan Tinggi & Mencegah Request Timeout:**
   * CSV adalah format teks murni (*plain text*) tanpa *overhead* struktur XML, zip packaging, atau styling.
   * Proses unduh (*download*) ke peramban pengguna dapat dimulai seketika (*Time-to-First-Byte* sangat cepat) tanpa harus menunggu seluruh 100.000 data selesai dikalkulasi.

3. **Keterbatasan Format PDF (DOMPDF):**
   * DomPDF bekerja dengan membangun *DOM tree* dan menghitung tata letak (*layout*) dokumen HTML/CSS secara menyeluruh di memori.
   * Merender 100.000 baris transaksi (yang setara dengan ribuan halaman dokumen PDF) dipastikan akan menyebabkan server kehabisan memori atau mengalami *execution timeout*. Karena itulah pada implementasi PDF aplikasi ini, tabel rincian transaksi dibatasi secara sengaja (`limit(50)`) hanya untuk ringkasan.

4. **Keterbatasan Format Excel (.xlsx):**
   * Format Excel XLSX berbasis file XML terkompresi (ZIP). Library spreadsheet seperti PhpSpreadsheet / Laravel Excel harus memetakan setiap sel menjadi objek di memori.
   * Mengolah 100.000 baris x beberapa kolom membutuhkan alokasi memori yang sangat besar (ratusan MB hingga GB) dan beban CPU yang berat, sehingga kurang aman dilakukan secara *synchronous* tanpa antrean (*queue job*).

5. **Kemudahan Analisis Data:**
   * File CSV merupakan format universal standar industri yang tetap dapat dibuka langsung dengan Microsoft Excel / Google Sheets, serta sangat mudah diimpor ke sistem analisis data lain (*data warehouse*, database, Python/Pandas).

---

## Langkah 10: Identifikasi Proses Lain yang Layak Menggunakan Queued Job

### Proses: Export Laporan Penjualan ke PDF

Proses lain pada aplikasi Simple POS yang layak dipindahkan ke queued job adalah proses ekspor laporan penjualan ke format PDF.

### Alasan Pemilihan

1. **Mengurangi waktu tunggu pengguna.** Pembuatan laporan PDF membutuhkan proses pengambilan dan pengolahan data transaksi. Jika jumlah transaksi semakin banyak, proses tersebut dapat membutuhkan waktu lebih lama.

2. **Menjaga responsivitas aplikasi.** Dengan queued job, pembuatan file PDF dapat dilakukan di latar belakang (*background*), sehingga aplikasi tidak harus menunggu seluruh proses selesai dalam satu permintaan.

3. **Mendukung proses retry.** Jika pembuatan laporan gagal karena gangguan sementara, queued job dapat dikonfigurasi untuk mencoba menjalankan proses kembali sesuai jumlah percobaan dan jeda yang ditentukan.

4. **Mempermudah pengelolaan laporan berukuran besar.** Proses pembuatan laporan dapat dijalankan oleh worker secara terpisah dari permintaan pengguna, sehingga lebih mudah dipantau dan dikelola.

### Gambaran Implementasi

Ketika pengguna meminta ekspor laporan penjualan ke PDF, aplikasi memasukkan permintaan tersebut ke antrean (*queue*). Selanjutnya, worker mengambil job untuk mengolah data transaksi dan membuat file PDF. Setelah selesai, file dapat disediakan untuk diunduh oleh pengguna.

**Kesimpulan:** Ekspor laporan penjualan ke PDF layak dipertimbangkan untuk menggunakan queued job, terutama ketika jumlah transaksi semakin banyak dan waktu pemrosesan meningkat. Bagian ini merupakan usulan pengembangan dan bukan pernyataan bahwa proses ekspor PDF saat ini sudah menggunakan queue.
