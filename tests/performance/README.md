# 🚀 JMeter Performance Testing - Fitur Transaksi RFD Laundry

## 📋 Gambaran Umum

File test plan JMeter ini dirancang khusus untuk melakukan **performance testing** pada seluruh endpoint fitur **Transaksi** di aplikasi RFD Laundry Management System.

---

## 📁 Struktur File

```
tests/performance/
├── RFD_Transaksi_Performance_Test.jmx   ← File utama JMeter Test Plan
├── results/                              ← Folder output hasil test
│   ├── summary_report.csv               ← Dihasilkan otomatis saat test
│   └── aggregate_report.csv             ← Dihasilkan otomatis saat test
└── README.md                            ← Panduan ini
```

---

## ✅ Prasyarat

| Kebutuhan | Detail |
|-----------|--------|
| **Apache JMeter** | Versi 5.6+ ([Download](https://jmeter.apache.org/download_jmeter.cgi)) |
| **Java JDK/JRE** | Versi 8 atau 11+ |
| **Aplikasi Laravel** | Harus berjalan (`php artisan serve`) |
| **Database** | MySQL aktif dengan data pelanggan & layanan |

---

## ⚙️ Langkah Setup Sebelum Menjalankan

### 1. Pastikan Aplikasi Berjalan
```powershell
# Di direktori project
php artisan serve
# Aplikasi berjalan di: http://localhost:8000
```

### 2. Sesuaikan Variabel di JMeter
Buka file `.jmx` di JMeter GUI, lalu pergi ke:
**Test Plan → User Defined Variables**

| Variabel | Default | Keterangan |
|----------|---------|------------|
| `BASE_URL` | `localhost` | Host aplikasi |
| `BASE_PORT` | `8000` | Port php artisan serve |
| `LOGIN_EMAIL` | `admin@rfdlaundry.com` | **Ganti** dengan email akun yang valid |
| `LOGIN_PASSWORD` | `password` | **Ganti** dengan password yang benar |
| `ID_PELANGGAN` | `1` | **Ganti** dengan ID pelanggan di database |
| `ID_LAYANAN` | `1` | **Ganti** dengan ID layanan yang memiliki unit_satuan kg & pcs |

### 3. Cek Data di Database
```sql
-- Ambil ID pelanggan yang tersedia
SELECT id, name FROM pelanggans LIMIT 5;

-- Ambil ID layanan beserta unit satuan
SELECT l.id, l.name, lu.unit_satuan, lu.harga
FROM layanans l
JOIN layanan_units lu ON lu.layanan_id = l.id
LIMIT 10;
```

---

## 🧪 Skenario Test yang Diuji

| # | Langkah | Endpoint | Metode | Batas Waktu |
|---|---------|----------|--------|-------------|
| 1 | Ambil CSRF Token | `GET /login` | GET | 3 detik |
| 2 | Submit Login | `POST /login` | POST | 3 detik |
| 3 | Lihat Daftar Transaksi | `GET /transaksi` | GET | 3 detik |
| 4 | Pencarian Transaksi | `GET /transaksi?search=IJ` | GET | 3 detik |
| 5 | Filter Status DP | `GET /transaksi?type=dp` | GET | 3 detik |
| 6 | Buat Transaksi Single | `POST /transaksi` | POST | 5 detik |
| 7 | Buat Transaksi Multi | `POST /transaksi` | POST | 5 detik |
| 8 | Update Transaksi | `PUT /transaksi/{id}` | POST+_method | 5 detik |
| 9 | Bayar Piutang | `PATCH /transaksi/{id}/bayar` | POST+_method | 5 detik |
| 10 | ~~Hapus Transaksi~~ | `DELETE /transaksi/{id}` | POST+_method | 5 detik |
| 11 | Cetak Struk PDF | `GET /transaksi/{id}/cetak-struk` | GET | 10 detik |

> ⚠️ **Langkah 10 (Delete) dinonaktifkan by default** untuk menghindari penghapusan data produksi. Aktifkan hanya di environment testing dengan data dummy.

---

## ▶️ Cara Menjalankan Test

### Opsi A: Via GUI JMeter (Direkomendasikan untuk pertama kali)

1. Buka **Apache JMeter**
2. Klik **File → Open** → pilih `RFD_Transaksi_Performance_Test.jmx`
3. Sesuaikan variabel (lihat langkah setup di atas)
4. Klik tombol **▶ Run** (atau tekan Ctrl+R)
5. Pantau hasil di **View Results Tree**, **Summary Report**, dan **Aggregate Report**

### Opsi B: Via Command Line (Non-GUI / CI-CD)

```powershell
# Navigasi ke direktori JMeter bin
cd "C:\path\to\apache-jmeter\bin"

# Jalankan test (ganti path sesuai lokasi file)
.\jmeter.bat -n -t "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Performance_Test.jmx" `
  -l "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\results\test_result.jtl" `
  -e -o "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\results\html_report"

# Untuk override variabel dari command line:
.\jmeter.bat -n -t "[path]\RFD_Transaksi_Performance_Test.jmx" `
  -JLOGIN_EMAIL="kasir@rfdlaundry.com" `
  -JLOGIN_PASSWORD="passwordkasir" `
  -JID_PELANGGAN="3" `
  -JID_LAYANAN="2" `
  -l "[path]\results\test_result.jtl" `
  -e -o "[path]\results\html_report"
```

---

## 📊 Konfigurasi Thread Group

### Load Test Normal (Default)
| Parameter | Nilai |
|-----------|-------|
| Jumlah Virtual User | **50 user** |
| Ramp-Up Period | **30 detik** |
| Loop Count | **5 iterasi** |
| Total Request | ±2.750 requests |

### Stress Test (Thread Group 2 - Disabled)
| Parameter | Nilai |
|-----------|-------|
| Jumlah Virtual User | **100 user** |
| Ramp-Up Period | **60 detik** |
| Loop Count | **10 iterasi** |
| Total Request | ±3.000 requests |

> Aktifkan Thread Group 2 dengan **klik kanan → Enable** untuk menjalankan stress test.

---

## 📈 Membaca Hasil Test

### Metrik Utama di Summary Report

| Kolom | Arti |
|-------|------|
| **# Samples** | Total jumlah request yang dikirim |
| **Average** | Rata-rata response time (ms) |
| **Min / Max** | Response time terkecil / terbesar |
| **90th pct** | 90% request selesai dalam waktu ini |
| **Error %** | Persentase request yang gagal |
| **Throughput** | Jumlah request per detik |

### Tolok Ukur Performa (Benchmark)

| Kondisi | Target |
|---------|--------|
| ✅ Response time rata-rata | < 2000ms |
| ✅ Response time 90th percentile | < 3000ms |
| ✅ Error rate | < 1% |
| ✅ Throughput | > 10 req/detik |
| ⚠️ Cetak PDF (DomPDF) | < 10000ms |

---

## 🔧 Troubleshooting

### ❌ Error: `csrf_token = TOKEN_NOT_FOUND`
- Pastikan aplikasi Laravel sedang berjalan
- Cek URL dan port di User Defined Variables
- Pastikan halaman `/login` dapat diakses di browser

### ❌ Error: Login gagal (redirect ke `/login` lagi)
- Cek `LOGIN_EMAIL` dan `LOGIN_PASSWORD` sudah benar
- Pastikan HTTP Cookie Manager aktif di Test Plan

### ❌ Error: 422 Unprocessable Entity saat POST /transaksi
- `ID_PELANGGAN` tidak ada di database → ganti dengan ID yang valid
- `ID_LAYANAN` tidak ada di database → ganti dengan ID yang valid
- Unit satuan `kg` atau `pcs` tidak terdaftar di `layanan_units` untuk layanan tersebut

### ❌ Error: 404 saat UPDATE / BAYAR
- ID transaksi yang diekstrak mungkin tidak valid
- Tambahkan **Debug Sampler** untuk memeriksa nilai variabel `${transaksi_id}`

### ❌ Thread Group 2 tidak muncul
- Thread Group Stress Test sengaja **disabled** secara default
- Klik kanan → Enable untuk mengaktifkannya

---

## 📝 Catatan Penting

1. **Jangan jalankan test ini di environment produksi** tanpa backup data terlebih dahulu
2. **Data transaksi baru akan dibuat** di database selama test berlangsung
3. Hapus data test setelah selesai: `DELETE FROM transaksis WHERE deskripsi LIKE '%JMeter%'`
4. Untuk hasil yang akurat, jalankan test di **mesin yang berbeda** dengan server Laravel
