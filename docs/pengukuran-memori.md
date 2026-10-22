# Pengukuran Memori: get() vs lazyById()

Dataset: sekitar 52.500 row pada tabel `transactions`.

| Cara baca data | Peak memory (MB) |
|---|---|
| `Transaction::lazyById()` | 69.1 MB |
| `Transaction::get()` | > 128 MB (Crash / Memory Exhausted) |

Kesimpulan kelompok: 
Penggunaan metode `Transaction::get()` memuat seluruh 52.500 baris data ke dalam memori sekaligus dan menginstansiasi setiap baris menjadi objek model Eloquent. Hal ini menyebabkan konsumsi memori melonjak drastis hingga melampaui batas default memory limit PHP (128 MB) dan menimbulkan fatal error (*Allowed memory size exhausted*).

Sebaliknya, `Transaction::lazyById()` memanfaatkan lazy collection (generator/chunking berbasis ID) sehingga data diambil dan diproses secara bertahap dalam potongan kecil tanpa menumpuk seluruh objek di memori. Hasilnya, memori puncak dapat ditekan secara stabil di angka **69.1 MB** dan pemrosesan 52.500 baris data selesai dengan aman dan efisien. Oleh karena itu, untuk dataset berskala besar, penggunaan `lazyById()` atau chunking sangat disarankan dibandingkan `get()`.
