# Feature Test Layanan

PHPUnit feature test untuk modul Layanan (`LayananFeatureTest.php`).

## Menjalankan test

```bash
# Semua test layanan
php artisan test tests/Feature/Layanan/LayananFeatureTest.php

# Test update (TC 11-15)
php artisan test tests/Feature/Layanan/LayananFeatureTest.php --filter="testCanUpdate|testCannotUpdate"
```

## Daftar test case

| TC | Method | Deskripsi |
|----|--------|-----------|
| 1 | `testCanStoreLayananWithValidData` | Tambah layanan valid |
| 2 | `testCannotStoreLayananWithEmptyField` | Validasi field wajib kosong (store) |
| 3 | `testCannotStoreLayananWithInvalidPrice` | Validasi harga tidak valid (store) |
| 4 | `testCannotStoreLayananWithInvalidImageFormat` | Validasi format gambar (store) |
| 5 | `testCanDisplayAndCloseLayananForm` | Tampil/tutup form tambah |
| 6 | `testCanStoreLayananWithMultipleUnits` | Tambah layanan multi unit |
| 7 | `testCanCancelAddingLayananForm` | Batalkan penambahan |
| 8 | `testCanSearchExistingLayanan` | Pencarian layanan |
| 9 | `testCanSearchNonExistingLayanan` | Pencarian tidak ditemukan |
| 10 | `testCanSortLayananData` | Sort data layanan |
| 11 | `testCanUpdateLayananWithValidData` | Update layanan valid |
| 12 | `testCannotUpdateLayananWithEmptyField` | Validasi field wajib kosong (update) |
| 13 | `testCannotUpdateLayananWithInvalidPrice` | Validasi harga tidak valid (update) |
| 14 | `testCannotUpdateLayananWithInvalidImageFormat` | Validasi format gambar (update) |
| 15 | `testCanUpdateLayananWithAddingMoreUnits` | Update dengan menambah unit |
| 16 | `testCanCancelUpdatingLayananForm` | Batalkan perubahan saat update |
| 17 | `testGuestCannotAccessLayananPage` | Akses tanpa login → redirect login |

## Catatan

- Menggunakan `RefreshDatabase`
- Upload file menggunakan `UploadedFile::fake()` dan `Storage::fake()`
- Update dengan file upload memakai `POST` + `_method=PUT` (multipart/form-data)
