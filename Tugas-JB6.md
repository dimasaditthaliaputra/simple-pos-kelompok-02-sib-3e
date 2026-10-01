# Tugas Jobsheet 6 — Simple POS (Kelompok 02 SIB-3E)

> **Pertanyaan:**  
> Jelaskan dengan kata-katamu sendiri (ditulis di README atau dokumen terpisah): kalau field `total` tetap dikirim dari form dan divalidasi dengan aturan `numeric`, apakah itu cukup mencegah manipulasi total? Kenapa atau kenapa tidak?

---

## 1. Dimas Adit Thalia Putra

**Jawaban:**

**Tidak cukup.** 

Validasi `numeric` pada server hanya memastikan bahwa data yang diterima berformat angka, tetapi tidak memverifikasi kebenaran atau kesesuaian nilai tersebut dengan rincian transaksi sebenarnya.

Data yang dikirimkan dari sisi klien (*client-side*) sepenuhnya berada di bawah kendali pengguna. Siapa pun dapat memanipulasi nilai `total` melalui *Developer Tools* (*Inspect Element*) ataupun menembak endpoint secara langsung menggunakan tools seperti Postman atau cURL. Sebagai contoh, transaksi yang semestinya bernilai Rp500.000 bisa diubah nilainya menjadi Rp5.000 sebelum request dikirim. Karena angka 5.000 tetap berupa angka, aturan validasi `numeric` akan meloloskannya.

Oleh karena itu, sesuai prinsip keamanan web (*never trust user input*), nilai `total` tidak boleh dipercayakan dari input form klien. Server harus selalu menghitung ulang total secara mandiri di sisi *back-end* berdasarkan harga resmi barang yang tersimpan di database dikalikan dengan kuantitas (*qty*) yang dibeli.

---

## 2. Fajar Kurnia Putra

**Jawaban:**

Jika total dikirim langsung dari input klien dan sekadar divalidasi dengan aturan `numeric`, validasi tersebut hanya akan memeriksa apakah input tersebut berupa angka. Pengguna dapat memanipulasi request melalui Inspect Element dengan mengubah nilai total menjadi angka yang jauh lebih kecil dari harga aslinya. Validasi `numeric` akan meloloskan input ini karena masih berupa angka. 

Itulah sebabnya, total harus selalu dihitung dan dijumlahkan ulang di server berdasarkan harga barang yang tersimpan di database, bukan memercayai perhitungan dari sisi klien.

---

## 3. Maulida Aprina

**Jawaban:**

**Tidak cukup untuk mencegah manipulasi.**

Aturan validasi `numeric` hanya membatasi tipe data agar berupa angka, bukan memeriksa integritas atau keabsahan logika bisnisnya (*data type validation* vs *data integrity validation*). Validasi ini tidak memiliki kemampuan untuk mendeteksi apakah nominal angka tersebut sesuai dengan kalkulasi barang yang sebenarnya.

Karena data yang berasal dari *form request* bersifat *untrusted*, penyerang atau pengguna dapat dengan mudah merekayasa data tersebut sebelum masuk ke server (misalnya melalui manipulasi DOM, HTTP proxy, atau modifikasi payload). Jika total tagihan diubah menjadi Rp1 atau nilai kecil lainnya, server akan tetap menganggap data tersebut valid karena memenuhi kriteria `numeric`.

Solusi yang tepat dan aman adalah sistem cukup menerima `product_id` dan `qty` dari form, sedangkan kalkulasi `subtotal` dan `total` wajib diproses seluruhnya di sisi server (*server-side calculation*) menggunakan harga acuan yang valid dari database.

---

## 4. Najwah Syihab

**Jawaban:**

Jika total dikirim langsung dari input klien dan sekadar divalidasi dengan aturan `numeric`, validasi tersebut hanya akan memeriksa apakah input tersebut berupa angka. Pengguna dapat memanipulasi request melalui Inspect Element dengan mengubah nilai total menjadi angka yang jauh lebih kecil dari harga aslinya. Validasi `numeric` akan meloloskan input ini karena masih berupa angka. 

Itulah sebabnya, total harus selalu dihitung dan dijumlahkan ulang di server berdasarkan harga barang yang tersimpan di database, bukan memercayai perhitungan dari sisi klien.