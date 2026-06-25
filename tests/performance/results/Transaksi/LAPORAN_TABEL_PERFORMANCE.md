# Laporan Performance Testing — Fitur Kelola Transaksi
## Sistem Informasi RFD Laundry Management System

---

**Tanggal Pengujian :** 07 Juni 2026  
**Tool Pengujian   :** Apache JMeter 5.6.3  
**Lingkungan       :** Development Server (`php artisan serve`, `http://localhost:8000`)  
**Database         :** MySQL (SQLite via Laravel)  
**Target Halaman   :** `/transaksi` (Kelola Transaksi)  
**Kredensial Uji   :** username: `1`, password: `12345678`

---

## 1. Ringkasan Skenario Pengujian

| Kode | Nama Skenario | Jumlah User | Ramp-Up | Loop | Tujuan |
|------|--------------|-------------|---------|------|--------|
| TC01 | Load Test — Normal Harian | 15 user | 60 detik | 3x | Simulasi aktivitas harian kasir (jam buka) |
| TC02-A | Stress Test — Gelombang 1 | 50 user | 30 detik | 2x | Uji batas awal sistem di atas beban normal |
| TC02-B | Stress Test — Gelombang 2 | 100 user | 30 detik | 2x | Identifikasi titik degradasi performa |
| TC02-C | Spike Test | 250 user | 30 detik | 1x | Uji lonjakan beban secara tiba-tiba |
| TC03 | Database Test | 50 user | 60 detik | 1x | Uji performa operasi baca/tulis database |

---

## 2. Hasil Pengujian Per Test Case

### TC01 — Load Test: 15 User (Kondisi Normal Harian)

> **Skenario:** Simulasi 15 kasir login dan mengakses halaman kelola transaksi di jam buka laundry.

| No | Label | Sampel | Avg (ms) | Min (ms) | Max (ms) | Std. Dev. | Error % | Throughput | Status |
|----|-------|--------|----------|----------|----------|-----------|---------|------------|--------|
| 1 | GET /login | 45 | 140 | 15 | 1.492 | 354,45 | **0,00%** | 1,39/det | ✅ LULUS |
| 2 | POST /login | 45 | 238 | 89 | 1.645 | 367,59 | **0,00%** | 1,39/det | ✅ LULUS |
| 3 | GET /transaksi | 45 | 177 | 29 | 1.163 | 322,68 | **0,00%** | 1,46/det | ✅ LULUS |
| 4 | GET /transaksi?search | 45 | 159 | 27 | 1.410 | 365,91 | **0,00%** | 1,46/det | ✅ LULUS |
| | **TOTAL** | **180** | **178** | **15** | **1.645** | **355,01** | **0,00%** | **5,19/det** | **✅ LULUS** |

**Kesimpulan TC01:** Sistem berjalan sangat baik pada beban normal. Seluruh request berhasil dengan rata-rata response time 178ms, jauh di bawah threshold 2.000ms. Error rate 0%.

---

### TC02-A — Stress Test Gelombang 1: 50 User

> **Skenario:** Menguji batas sistem dengan beban 3× lipat di atas kondisi normal.

| No | Label | Sampel | Avg (ms) | Min (ms) | Max (ms) | Std. Dev. | Error % | Throughput | Status |
|----|-------|--------|----------|----------|----------|-----------|---------|------------|--------|
| 1 | GET /login | 100 | 282 | 14 | 1.744 | 430,84 | **0,00%** | 3,30/det | ✅ LULUS |
| 2 | POST /login | 100 | 431 | 86 | 1.924 | 507,77 | **0,00%** | 3,29/det | ✅ LULUS |
| 3 | GET /transaksi | 100 | 168 | 26 | 1.743 | 287,57 | **0,00%** | 3,28/det | ✅ LULUS |
| | **TOTAL** | **300** | **294** | **14** | **1.924** | **432,45** | **0,00%** | **9,76/det** | **✅ LULUS** |

**Kesimpulan TC02-A:** Sistem masih mampu menangani 50 user bersamaan tanpa error. Response time meningkat namun masih di bawah threshold 2.000ms. Sistem stabil.

---

### TC02-B — Stress Test Gelombang 2: 100 User

> **Skenario:** Menguji performa sistem pada beban 100 user bersamaan untuk mengidentifikasi titik degradasi.

| No | Label | Sampel | Avg (ms) | Min (ms) | Max (ms) | Std. Dev. | Error % | Throughput | Status |
|----|-------|--------|----------|----------|----------|-----------|---------|------------|--------|
| 1 | GET /login | 200 | 2.653 | 16 | 7.133 | 1.502,75 | **0,00%** | 4,30/det | ⚠️ LAMBAT |
| 2 | POST /login | 200 | 5.672 | 100 | 11.650 | 2.817,22 | **0,00%** | 4,01/det | ⚠️ LAMBAT |
| 3 | GET /transaksi | 200 | 2.761 | 28 | 7.134 | 1.642,86 | **0,00%** | 4,00/det | ⚠️ LAMBAT |
| | **TOTAL** | **600** | **3.695** | **16** | **11.650** | **2.500,60** | **0,00%** | **11,95/det** | **⚠️ DEGRADASI** |

**Kesimpulan TC02-B:** Error rate 0% (tidak ada kegagalan), namun response time meningkat signifikan — rata-rata 3,7 detik dan POST /login mencapai max 11,6 detik. Ini menunjukkan degradasi performa yang jelas. Sistem masih merespons tetapi sudah jauh melewati threshold kenyamanan pengguna (2.000ms).

---

### TC02-C — Spike Test: 250 User (Lonjakan Tiba-tiba)

> **Skenario:** Mensimulasikan lonjakan beban ekstrem secara mendadak untuk melihat ketahanan sistem.

| No | Label | Sampel | Avg (ms) | Min (ms) | Max (ms) | Std. Dev. | Error % | Throughput | Status |
|----|-------|--------|----------|----------|----------|-----------|---------|------------|--------|
| 1 | GET /login | 250 | 9.230 | 0 | 19.608 | 5.821,90 | **4,40%** | 5,15/det | ❌ GAGAL |
| 2 | POST /login | 250 | 20.200 | 0 | 34.239 | 10.757,48 | **12,00%** | 3,79/det | ❌ GAGAL |
| 3 | GET /transaksi | 250 | 7.016 | 0 | 21.034 | 5.815,86 | **18,00%** | 3,51/det | ❌ GAGAL |
| | **TOTAL** | **750** | **12.149** | **0** | **34.239** | **9.714,65** | **11,47%** | **10,24/det** | **❌ GAGAL** |

**Kesimpulan TC02-C:** Sistem tidak mampu menangani lonjakan 250 user. Error rate mencapai 11,47% dengan Min=0ms (koneksi langsung ditolak akibat TCP TIME_WAIT exhaustion). POST /login memerlukan rata-rata 20 detik. Ini adalah **breaking point** sistem pada lingkungan development server.

---

### TC03 — Database Test: 50 User (Operasi Baca/Tulis)

> **Skenario:** Mengukur waktu eksekusi query database (SELECT, LIKE, JOIN, INSERT) saat 50 user mengakses bersamaan.

| No | Label | Sampel | Avg (ms) | Min (ms) | Max (ms) | Std. Dev. | Error % | Throughput | Status |
|----|-------|--------|----------|----------|----------|-----------|---------|------------|--------|
| 1 | GET /login | 50 | 871 | 17 | 2.670 | 888,13 | **0,00%** | 1,57/det | ✅ LULUS |
| 2 | POST /login | 50 | 1.056 | 101 | 4.482 | 1.185,87 | **0,00%** | 1,56/det | ✅ LULUS |
| 3 | DB READ: GET /transaksi *(SELECT + JOIN + Paginate)* | 50 | 285 | 27 | 2.652 | 597,87 | **0,00%** | 1,56/det | ✅ LULUS |
| 4 | DB READ 2: GET /transaksi?search *(WHERE LIKE + JOIN)* | 50 | 477 | 25 | 2.664 | 856,97 | **0,00%** | 1,56/det | ✅ LULUS |
| 5 | DB WRITE: POST /transaksi *(INSERT Transaction)* | 50 | 895 | 49 | 4.398 | 1.225,27 | **0,00%** | 1,56/det | ✅ LULUS |
| | **TOTAL** | **250** | **717** | **17** | **4.482** | **1.020,10** | **0,00%** | **7,47/det** | **✅ LULUS** |

**Kesimpulan TC03:** Semua operasi database berjalan dengan error rate 0%. Query READ (285ms avg) lebih cepat dari WRITE (895ms avg), hal ini wajar karena INSERT melibatkan validasi, trigger, dan penulisan ke disk. Performa database dinilai baik untuk skala usaha rumahan.

---

## 3. Tabel Perbandingan Seluruh Test Case

| Test Case | Skenario | User | Total Sampel | Avg (ms) | Max (ms) | Error % | Throughput | Hasil |
|-----------|----------|------|-------------|----------|----------|---------|------------|-------|
| TC01 | Load Normal | 15 | 180 | 178 | 1.645 | **0,00%** | 5,19/det | ✅ **LULUS** |
| TC02-A | Stress Gel. 1 | 50 | 300 | 294 | 1.924 | **0,00%** | 9,76/det | ✅ **LULUS** |
| TC02-B | Stress Gel. 2 | 100 | 600 | 3.695 | 11.650 | **0,00%** | 11,95/det | ⚠️ **DEGRADASI** |
| TC02-C | Spike Test | 250 | 750 | 12.149 | 34.239 | **11,47%** | 10,24/det | ❌ **GAGAL** |
| TC03 | Database Test | 50 | 250 | 717 | 4.482 | **0,00%** | 7,47/det | ✅ **LULUS** |

---

## 4. Analisis & Temuan

### 4.1 Batas Kapasitas Sistem

```
Aman (Error 0%, Avg < 2000ms)  : ≤ 50 user bersamaan  (TC01, TC02-A, TC03)
Degradasi (Lambat, Error 0%)   : ~100 user bersamaan  (TC02-B)
Breaking Point (Error > 0%)    : ≥ 250 user bersamaan (TC02-C)
```

### 4.2 Bottleneck Utama

| Temuan | Penyebab | Dampak |
|--------|----------|--------|
| Response time melonjak di TC02-B | `php artisan serve` single-thread | POST /login max 11,6 detik |
| TCP TIME_WAIT exhaustion di TC02-C | 250 koneksi TCP tidak dapat didaur ulang cepat | Min=0ms (koneksi ditolak langsung) |
| Error 18% GET /transaksi di TC02-C | Cascade dari login gagal → sesi tidak valid | Redirect ke login (bukan HTTP 200) |

### 4.3 Performa Database (TC03)

| Operasi | Rata-rata | Keterangan |
|---------|-----------|------------|
| SELECT + JOIN + Paginate | 285ms | ✅ Sangat cepat |
| WHERE LIKE + JOIN (search) | 477ms | ✅ Cepat |
| INSERT (transaksi baru) | 895ms | ✅ Normal untuk write operation |

---

## 5. Kesimpulan

Sistem RFD Laundry **layak digunakan** untuk operasional usaha rumahan dengan jumlah staff admin/kasir maksimal **15 orang** (sesuai skenario nyata). Hasil TC01 membuktikan sistem dapat menangani seluruh aktivitas harian dengan sempurna (0% error, avg 178ms).

Degradasi performa hanya terjadi ketika beban jauh melebihi kebutuhan nyata bisnis (>50 user simultan), yang tidak akan terjadi dalam kondisi operasional sesungguhnya dari usaha laundry rumahan.

### Rekomendasi

| Prioritas | Rekomendasi |
|-----------|-------------|
| 🔴 Jika deploy ke production | Gunakan **Nginx + PHP-FPM** (bukan `php artisan serve`) |
| 🟡 Optimasi query | Tambahkan **database index** pada kolom `id_pelanggan`, `status`, `created_at` |
| 🟢 Sudah cukup | Untuk skala usaha rumahan (≤15 user), performa saat ini sudah **sangat memadai** |

---

*Dokumen ini dibuat otomatis dari data hasil pengujian JMeter.*  
*File sumber: `TC01 summary.csv`, `TC02-A summary.csv`, `TC02-B summary.csv`, `TC02-C summary.csv`, `TC03 summary.csv`*
