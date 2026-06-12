# REGRESSION TEST REPORT
## Perancangan Sistem Pendataan Transaksi Berbasis Web untuk Efisiensi Manajemen Pelanggan dan Keuangan di RFD Laundry

---

**Jenis Pengujian  :** Regression Testing  
**Dasar Pengujian  :** Bug Report (Black Box Testing)  
**Tanggal Dibuat   :** 07 Juni 2026  
**Tujuan           :** Memverifikasi bahwa defect yang ditemukan pada sesi Black Box Testing telah diperbaiki dan tidak muncul kembali setelah perubahan kode  

---

## Referensi Defect yang Menjadi Dasar Regression Test

| Defect ID | Fitur | Severity | Priority | Tester Asal |
|-----------|-------|----------|----------|-------------|
| DF-TRX-01 | Mengelola Data Transaksi | High | High | MLN |
| DF-KASIR-01 | Mengelola Data Kasir | Medium | Medium | BAY |
| DF-KSR-02 | Mengelola Data Kasir | High | Medium | FAI |
| DF-KSR-01 | Mengelola Data Kasir | Medium | Low | FAI |
| DF-CUST-01 | Mengelola Data Pelanggan | High | Medium | DVD |

---

## Tabel Regression Test Case

### RT-001 — Validasi Jumlah Bayar Minimum saat Update Transaksi
**Referensi Defect :** DF-TRX-01  
**Fitur            :** Mengelola Data Transaksi  
**Deskripsi Defect :** Sistem masih bisa menyimpan data transaksi meskipun jumlah bayar di bawah 50% dari total tagihan  
**Tujuan RT        :** Memastikan sistem menolak penyimpanan jika jumlah bayar di bawah batas minimum (50%)

| No | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-------------------|----------|-----------------------|--------------|--------|
| 1 | Login sebagai admin/kasir | username: `1`, password: `12345678` | Berhasil login, diarahkan ke dashboard | | |
| 2 | Buka halaman Kelola Transaksi `/transaksi` | — | Halaman daftar transaksi ditampilkan | | |
| 3 | Pilih salah satu transaksi, klik tombol **"Update"** | Transaksi dengan total tagihan ≥ Rp 50.000 | Modal atau halaman edit transaksi terbuka | | |
| 4 | Isi kolom **"Jumlah Bayar"** dengan nominal di bawah 50% total tagihan | Contoh: total Rp 50.000 → isi Rp 10.000 (20%) | Sistem menampilkan pesan validasi error dan **menolak** penyimpanan | | |
| 5 | Klik tombol **"Simpan Perubahan"** | Jumlah bayar: Rp 10.000 | Sistem **tidak menyimpan** data; muncul notifikasi: *"Jumlah bayar tidak boleh kurang dari 50% total tagihan"* | | |
| 6 | Ulangi langkah 4–5 dengan nominal tepat 50% | Contoh: total Rp 50.000 → isi Rp 25.000 (50%) | Sistem **berhasil menyimpan** (batas minimum terpenuhi) | | |
| 7 | Ulangi langkah 4–5 dengan nominal di atas 50% | Contoh: total Rp 50.000 → isi Rp 30.000 (60%) | Sistem **berhasil menyimpan** | | |

**Kriteria Lulus:** Sistem menolak penyimpanan pada langkah 5 dan menerima penyimpanan pada langkah 6–7.

---

### RT-002 — Update Akun Kasir dengan Data Valid
**Referensi Defect :** DF-KASIR-01  
**Fitur            :** Mengelola Data Kasir  
**Deskripsi Defect :** Sistem gagal melakukan update akun kasir meskipun form diisi dengan data valid  
**Tujuan RT        :** Memastikan fungsi update kasir berjalan normal setelah perbaikan

| No | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-------------------|----------|-----------------------|--------------|--------|
| 1 | Login sebagai admin | username: `1`, password: `12345678` | Berhasil login sebagai admin | | |
| 2 | Buka halaman **Kelola Data Kasir** | — | Daftar kasir ditampilkan | | |
| 3 | Pilih salah satu kasir, klik tombol **"Update Kasir"** | — | Form edit kasir terbuka dengan data kasir yang dipilih | | |
| 4 | Ubah field **Nama** dengan nama baru yang valid | `Nama Baru Kasir` | Field nama berhasil diubah | | |
| 5 | Ubah field **Email** dengan email valid yang belum terdaftar | `kasir.baru@rfdlaundry.com` | Field email berhasil diubah | | |
| 6 | Klik tombol **"Simpan Perubahan"** | — | Data kasir **berhasil diperbarui**; muncul notifikasi sukses; data baru tampil di tabel | | |
| 7 | Verifikasi data di tabel | — | Tabel menampilkan nama dan email yang baru diubah tanpa perlu refresh manual | | |

**Kriteria Lulus:** Data kasir berhasil diperbarui di database dan tercermin di tabel pada langkah 6–7.

---

### RT-003 — Nonaktifkan/Hapus Akun Kasir
**Referensi Defect :** DF-KSR-02  
**Fitur            :** Mengelola Data Kasir  
**Deskripsi Defect :** Saat melakukan update kasir, sistem hanya me-reload halaman tanpa menyimpan data; fitur delete seharusnya diubah menjadi fitur nonaktifkan status kasir  
**Tujuan RT        :** Memastikan (1) update kasir tersimpan tanpa perlu ubah password, dan (2) fitur delete/nonaktifkan berfungsi benar

| No | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-------------------|----------|-----------------------|--------------|--------|
| 1 | Login sebagai admin | username: `1`, password: `12345678` | Berhasil login | | |
| 2 | Buka halaman **Kelola Data Kasir** | — | Daftar kasir ditampilkan | | |
| 3 | Pilih kasir, klik tombol **"Update"**, ubah hanya field **Nama** (tanpa mengubah password) | Nama baru: `Kasir Test Regression` | Form terisi dengan data baru | | |
| 4 | Klik **"Simpan Perubahan"** tanpa mengisi field password | Password dikosongkan | Sistem **berhasil menyimpan** perubahan nama **tanpa** memaksa perubahan password | | |
| 5 | Verifikasi perubahan tersimpan di tabel | — | Nama baru tampil di tabel | | |
| 6 | Pilih kasir yang akan dinonaktifkan, klik tombol **"Nonaktifkan"** / **"Delete"** | — | Muncul dialog konfirmasi nonaktifkan/hapus | | |
| 7 | Klik **"Ya, Hapus"** / **"Konfirmasi"** pada dialog | — | Status kasir berubah menjadi nonaktif ATAU kasir terhapus dari daftar; **data tersimpan permanen** di database | | |
| 8 | Refresh halaman dan cek tabel kasir | — | Status perubahan tetap tersimpan (tidak kembali ke semula) | | |

**Kriteria Lulus:** Langkah 4 berhasil menyimpan tanpa password; langkah 7–8 memastikan nonaktifkan/hapus berfungsi permanen.

---

### RT-004 — Interaksi Tombol Tambah Data Kasir
**Referensi Defect :** DF-KSR-01  
**Fitur            :** Mengelola Data Kasir  
**Deskripsi Defect :** Saat otomasi dengan Katalon, ditemukan `ElementNotInteractableException` — elemen/tombol tidak bisa diklik (tidak interactable)  
**Tujuan RT        :** Memastikan tombol tambah data kasir selalu dalam kondisi dapat diklik (interactable) baik secara manual maupun otomasi

| No | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-------------------|----------|-----------------------|--------------|--------|
| 1 | Login sebagai admin | username: `1`, password: `12345678` | Berhasil login | | |
| 2 | Buka halaman **Kelola Data Kasir** | — | Halaman termuat sempurna | | |
| 3 | Verifikasi tombol **"Tambah Kasir"** terlihat di halaman | — | Tombol terlihat dan tidak tertutup elemen lain | | |
| 4 | Klik tombol **"Tambah Kasir"** | — | Modal atau halaman tambah kasir terbuka dengan normal | | |
| 5 | Isi form tambah kasir dengan data lengkap dan valid | Nama: `Kasir Baru`, Username: `kasir_reg_01`, Password: `Test1234!` | Semua field dapat diisi tanpa hambatan | | |
| 6 | Klik tombol **"Simpan"** | — | Data kasir baru **berhasil tersimpan**; muncul di tabel | | |
| 7 | Ulangi langkah 3–4 segera setelah penyimpanan berhasil | — | Tombol **"Tambah Kasir"** masih dapat diklik (tidak disable/hidden setelah aksi sebelumnya) | | |

**Kriteria Lulus:** Tombol selalu interactable di langkah 4 dan 7; tidak ada exception elemen tidak dapat diklik.

---

### RT-005 — Modal Edit Pelanggan Tidak Berubah Menjadi Modal Tambah Pelanggan
**Referensi Defect :** DF-CUST-01  
**Fitur            :** Mengelola Data Pelanggan  
**Deskripsi Defect :** Saat update nomor telepon pelanggan dengan nomor yang sudah terdaftar, modal berubah dari `edit-customer` menjadi `add-customer` (modal tambah pelanggan baru)  
**Tujuan RT        :** Memastikan modal tetap dalam mode **edit** meskipun terjadi konflik validasi nomor telepon

| No | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|----|-------------------|----------|-----------------------|--------------|--------|
| 1 | Login sebagai admin/kasir | username: `1`, password: `12345678` | Berhasil login | | |
| 2 | Buka halaman **Kelola Data Pelanggan** | — | Daftar pelanggan ditampilkan | | |
| 3 | Pilih pelanggan A, klik tombol **"Update"** | — | Modal **edit pelanggan** terbuka dengan data pelanggan A | | |
| 4 | Verifikasi judul/title modal | — | Modal menampilkan judul **"Edit Pelanggan"** (bukan "Tambah Pelanggan") | | |
| 5 | Ubah field **Nomor Telepon** dengan nomor yang sudah dimiliki pelanggan B | Nomor telepon yang sudah terdaftar di sistem | Field nomor telepon berisi nomor duplikat | | |
| 6 | Klik tombol **"Simpan Perubahan"** | — | Sistem **menolak penyimpanan** dan menampilkan pesan error: *"Nomor telepon sudah terdaftar"* | | |
| 7 | Verifikasi modal setelah klik simpan | — | Modal **tetap terbuka sebagai modal edit** (bukan berubah menjadi modal tambah pelanggan) | | |
| 8 | Verifikasi data pelanggan A di tabel | — | Data pelanggan A **tidak berubah** (nomor telepon lama tetap tersimpan) | | |
| 9 | Ubah nomor telepon ke nomor yang belum terdaftar, lalu klik simpan | Nomor unik baru | Data **berhasil diperbarui**; modal tertutup; data baru tampil di tabel | | |

**Kriteria Lulus:** Modal tetap sebagai `edit-customer` pada langkah 7; tidak terjadi perubahan ke `add-customer` dalam kondisi apapun.

---

## Rekap Tabel Regression Test

| RT ID | Referensi Defect | Fitur | Prioritas Uji | Jumlah Langkah | Kriteria Pass |
|-------|-----------------|-------|--------------|----------------|---------------|
| RT-001 | DF-TRX-01 | Mengelola Data Transaksi | 🔴 Tinggi | 7 langkah | Validasi min 50% berjalan |
| RT-002 | DF-KASIR-01 | Mengelola Data Kasir | 🟡 Sedang | 7 langkah | Update kasir berhasil disimpan |
| RT-003 | DF-KSR-02 | Mengelola Data Kasir | 🔴 Tinggi | 8 langkah | Simpan tanpa password + nonaktifkan permanen |
| RT-004 | DF-KSR-01 | Mengelola Data Kasir | 🟢 Rendah | 7 langkah | Tombol selalu interactable |
| RT-005 | DF-CUST-01 | Mengelola Data Pelanggan | 🔴 Tinggi | 9 langkah | Modal tidak berubah dari edit ke add |

---

## Kapan Regression Test Ini Dijalankan?

| Kondisi | RT yang Dijalankan |
|---------|-------------------|
| Ada perubahan kode di modul Transaksi | RT-001 (wajib) |
| Ada perubahan kode di modul Kasir | RT-002, RT-003, RT-004 (wajib) |
| Ada perubahan kode di modul Pelanggan | RT-005 (wajib) |
| Ada perubahan global (dependency, framework update) | **Semua RT** dijalankan |
| Sebelum setiap rilis/deployment | **Semua RT** dijalankan |

---

*Dokumen ini dibuat berdasarkan Bug Report hasil Black Box Testing.*  
*Defect asal: DF-TRX-01, DF-KASIR-01, DF-KSR-02, DF-KSR-01, DF-CUST-01*
