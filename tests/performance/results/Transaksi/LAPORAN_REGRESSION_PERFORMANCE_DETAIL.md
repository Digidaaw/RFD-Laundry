# LAPORAN REGRESSION TESTING — PERFORMANCE TEST
## Perancangan Sistem Pendataan Transaksi Berbasis Web
## RFD Laundry Management System

---

**Jenis Pengujian  :** Regression Testing — Performance Test  
**Tool             :** Apache JMeter 5.6.3  
**Lingkungan       :** `http://localhost:8000` (php artisan serve)  
**Tanggal Uji      :** 07 Juni 2026  
**Tester           :** MAU  
**Fitur            :** Mengelola Data Transaksi  

---

## TC-TRX-PT-01 — Response Time ≤ 4 Detik saat 50 User Akses Simultan

| | |
|--|--|
| **Test Case ID** | TC-TRX-PT-01 |
| **Deskripsi** | Response time ≤ 4 detik saat 50 user akses simultan |
| **Jumlah User** | 50 |
| **Precondition** | Server Laravel berjalan di `localhost:8000`, akun admin tersedia |
| **Target** | Semua langkah response time < 4.000ms, Error Rate = 0% |

### Detail Langkah Pengujian

| No | Langkah | Target (ms) | Avg Aktual (ms) | Min (ms) | Max (ms) | Error % | Throughput | Status |
|----|---------|-------------|-----------------|----------|----------|---------|------------|--------|
| 1 | GET /login — Buka halaman login | < 4.000 | **282** | 14 | 1.744 | 0,00% | 3,30/det | ✅ **LULUS** |
| 2 | POST /login — Proses autentikasi kasir | < 4.000 | **431** | 86 | 1.924 | 0,00% | 3,29/det | ✅ **LULUS** |
| 3 | GET /transaksi — Buka halaman kelola transaksi | < 4.000 | **168** | 26 | 1.743 | 0,00% | 3,28/det | ✅ **LULUS** |
| 4 | GET /transaksi?search — Filter/pencarian transaksi | < 4.000 | *tidak diukur* | — | — | — | — | — |

### Hasil Keseluruhan TC-TRX-PT-01

| Metrik | Nilai |
|--------|-------|
| Total Sampel | 300 |
| Rata-rata Response Time | **294ms** |
| Response Time Tercepat | 14ms |
| Response Time Terlambat | 1.924ms |
| Error Rate | **0,00%** |
| Throughput | 9,76 request/detik |
| Standar Deviasi | 432,45ms |

| | Jumlah | Keterangan |
|--|--------|------------|
| ✅ **LULUS** | **3** | Semua langkah di bawah threshold 4.000ms |
| ❌ **TIDAK LULUS** | **0** | — |

> **Kesimpulan:** Sistem **LULUS** TC-TRX-PT-01. Dengan 50 user simultan, seluruh response time jauh di bawah batas 4.000ms (max hanya 1.924ms). Error rate 0%. Sistem sangat responsif pada beban ini.

---

## TC-TRX-PT-02 — Maksimum User Sebelum Aplikasi Crash

| | |
|--|--|
| **Test Case ID** | TC-TRX-PT-02 |
| **Deskripsi** | Maksimum user sebelum aplikasi crash |
| **Jumlah User** | 50 / 100 / 250 (bertahap) |
| **Precondition** | Server Laravel berjalan, setiap gelombang dijalankan setelah restart server |
| **Target** | Menemukan breaking point sistem |

### Detail Langkah Pengujian — Gelombang 1 (50 User)

| No | Langkah | Target | Avg Aktual (ms) | Min (ms) | Max (ms) | Error % | Status |
|----|---------|--------|-----------------|----------|----------|---------|--------|
| 1 | GET /login (50 user) | Tidak crash | **282** | 14 | 1.744 | 0,00% | ✅ **LULUS** |
| 2 | POST /login (50 user) | Tidak crash | **431** | 86 | 1.924 | 0,00% | ✅ **LULUS** |
| 3 | GET /transaksi (50 user) | Tidak crash | **168** | 26 | 1.743 | 0,00% | ✅ **LULUS** |

### Detail Langkah Pengujian — Gelombang 2 (100 User)

| No | Langkah | Target | Avg Aktual (ms) | Min (ms) | Max (ms) | Error % | Status |
|----|---------|--------|-----------------|----------|----------|---------|--------|
| 4 | GET /login (100 user) | Tidak crash | **2.653** | 16 | 7.133 | 0,00% | ⚠️ **LULUS*** |
| 5 | POST /login (100 user) | Tidak crash | **5.672** | 100 | 11.650 | 0,00% | ⚠️ **LULUS*** |
| 6 | GET /transaksi (100 user) | Tidak crash | **2.761** | 28 | 7.134 | 0,00% | ⚠️ **LULUS*** |

> *⚠️ Lulus dari sisi tidak crash (Error 0%), namun response time sudah jauh melampaui 4.000ms. Terjadi degradasi performa signifikan.

### Detail Langkah Pengujian — Gelombang 3 (250 User / Spike)

| No | Langkah | Target | Avg Aktual (ms) | Min (ms) | Max (ms) | Error % | Status |
|----|---------|--------|-----------------|----------|----------|---------|--------|
| 7 | GET /login (250 user) | Tidak crash | **9.230** | 0 | 19.608 | **4,40%** | ❌ **TIDAK LULUS** |
| 8 | POST /login (250 user) | Tidak crash | **20.200** | 0 | 34.239 | **12,00%** | ❌ **TIDAK LULUS** |
| 9 | GET /transaksi (250 user) | Tidak crash | **7.016** | 0 | 21.034 | **18,00%** | ❌ **TIDAK LULUS** |

> Min = 0ms menunjukkan koneksi langsung ditolak akibat TCP TIME_WAIT exhaustion (kehabisan port).

### Hasil Keseluruhan TC-TRX-PT-02

| Gelombang | User | Total Sampel | Avg (ms) | Max (ms) | Error % | Hasil |
|-----------|------|-------------|----------|----------|---------|-------|
| Gelombang 1 | 50 | 300 | 294 | 1.924 | **0,00%** | ✅ Stabil |
| Gelombang 2 | 100 | 600 | 3.695 | 11.650 | **0,00%** | ⚠️ Degradasi |
| Gelombang 3 | 250 | 750 | 12.149 | 34.239 | **11,47%** | ❌ Breaking Point |

| | Jumlah | Langkah |
|--|--------|---------|
| ✅ **LULUS** | **6** | Langkah 1–6 (50 & 100 user tidak crash) |
| ❌ **TIDAK LULUS** | **3** | Langkah 7–9 (250 user: error + timeout ekstrem) |

> **Kesimpulan:** Breaking point sistem ditemukan pada **~250 user simultan**. Sistem masih bertahan (tidak crash total) pada 100 user tetapi dengan degradasi performa berat. Batas aman operasional: **≤ 50 user simultan**.

---

## TC-TRX-PT-03 — Waktu Eksekusi Database saat 50 User Baca/Tulis Simultan

| | |
|--|--|
| **Test Case ID** | TC-TRX-PT-03 |
| **Deskripsi** | Waktu eksekusi DB saat 50 user baca/tulis simultan |
| **Jumlah User** | 50 |
| **Precondition** | Server berjalan, database berisi data transaksi, akun valid tersedia |
| **Target** | Semua operasi DB selesai < 5.000ms, Error Rate = 0% |

### Detail Langkah Pengujian

| No | Langkah | Jenis Operasi DB | Target (ms) | Avg Aktual (ms) | Min (ms) | Max (ms) | Error % | Status |
|----|---------|-----------------|-------------|-----------------|----------|----------|---------|--------|
| 1 | GET /login — Buka halaman login | AUTH (Session) | < 5.000 | **871** | 17 | 2.670 | 0,00% | ✅ **LULUS** |
| 2 | POST /login — Autentikasi ke database | SELECT (users WHERE username) | < 5.000 | **1.056** | 101 | 4.482 | 0,00% | ✅ **LULUS** |
| 3 | GET /transaksi — Tampil daftar transaksi | **READ:** SELECT + JOIN + PAGINATE | < 5.000 | **285** | 27 | 2.652 | 0,00% | ✅ **LULUS** |
| 4 | GET /transaksi?search — Pencarian dengan filter | **READ:** WHERE LIKE + JOIN | < 5.000 | **477** | 25 | 2.664 | 0,00% | ✅ **LULUS** |
| 5 | POST /transaksi — Simpan transaksi baru | **WRITE:** INSERT Transaction | < 5.000 | **895** | 49 | 4.398 | 0,00% | ✅ **LULUS** |

### Perbandingan Kecepatan Operasi Database

| Jenis Operasi | Query | Avg (ms) | Keterangan |
|--------------|-------|----------|------------|
| READ — Select List | SELECT * + JOIN + LIMIT | **285ms** | 🚀 Tercepat |
| READ — Search/Filter | SELECT WHERE LIKE + JOIN | **477ms** | ✅ Cepat |
| WRITE — Insert | INSERT INTO transaksi | **895ms** | ✅ Normal untuk write |
| AUTH — Login | SELECT WHERE username | **1.056ms** | ✅ Wajar (bcrypt verify) |

### Hasil Keseluruhan TC-TRX-PT-03

| Metrik | Nilai |
|--------|-------|
| Total Sampel | 250 |
| Rata-rata Keseluruhan | **717ms** |
| Response Time Tercepat | 17ms |
| Response Time Terlambat | 4.482ms |
| Error Rate | **0,00%** |
| Throughput | 7,47 request/detik |
| Standar Deviasi | 1.020,10ms |

| | Jumlah | Keterangan |
|--|--------|------------|
| ✅ **LULUS** | **5** | Semua operasi DB di bawah threshold 5.000ms |
| ❌ **TIDAK LULUS** | **0** | — |

> **Kesimpulan:** Sistem **LULUS** TC-TRX-PT-03. Semua operasi database (baca dan tulis) berjalan normal dengan error rate 0%. Query READ (285ms) lebih cepat dari WRITE (895ms), hal ini wajar karena INSERT melibatkan validasi dan penulisan ke disk. Performa database dinilai baik untuk skala usaha rumahan.

---

## Rekap Akhir Seluruh Performance Test

| Test Case ID | Deskripsi | Jumlah User | Total Langkah | Lulus | Tidak Lulus | Status Akhir |
|-------------|-----------|-------------|--------------|-------|-------------|--------------|
| TC-TRX-PT-01 | Response time ≤ 4 detik saat 50 user akses simultan | 50 | 3 | **3** | **0** | ✅ **LULUS** |
| TC-TRX-PT-02 | Maksimum user sebelum aplikasi crash | 50 / 100 / 250 | 9 | **6** | **3** | ⚠️ **SEBAGIAN LULUS** |
| TC-TRX-PT-03 | Waktu eksekusi DB saat 50 user baca/tulis simultan | 50 | 5 | **5** | **0** | ✅ **LULUS** |
| | | | **TOTAL** | **14** | **3** | |

### Temuan Utama

| No | Temuan | Detail | Rekomendasi |
|----|--------|--------|-------------|
| 1 | ✅ Sistem aman untuk operasional harian | 0% error pada beban ≤ 50 user | Tidak perlu tindakan untuk usaha rumahan (maks 15 user) |
| 2 | ⚠️ Degradasi performa di 100 user | Avg response time naik ke 3.695ms (dari 294ms) | Monitor jika user bertambah di masa depan |
| 3 | ❌ Breaking point di 250 user | Error 11,47%, TCP TIME_WAIT exhaustion | Migrasi ke Nginx + PHP-FPM jika skala membesar |
| 4 | ✅ Performa database sangat baik | READ 285ms, WRITE 895ms, 0% error | Pertahankan struktur query saat ini |

---

*Data diambil dari hasil pengujian JMeter: TC01 summary.csv, TC02-A summary.csv, TC02-B summary.csv, TC02-C summary.csv, TC03 summary.csv*
