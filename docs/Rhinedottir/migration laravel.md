# Catatan: Menjalankan Migration Spesifik di Laravel

Jika Anda hanya ingin menjalankan satu file migration tertentu tanpa mengeksekusi seluruh file migration yang ada, Anda bisa menggunakan opsi `--path`.

## 🚀 Perintah Dasar

Jalankan perintah berikut di terminal:

```bash
php artisan migrate --path=/database/migrations/2026_09_06_103224_add_trainers_to_msds_documents_table.php
```

## 📌 Hal yang Perlu Diperhatikan

1. **Sesuaikan Path:** Pastikan *path* (jalur) sesuai dengan lokasi file di proyek Anda. Secara default, file berada di folder `database/migrations/`.
2. **Berlaku untuk Rollback:** Opsi `--path` ini juga dapat digunakan jika Anda ingin membatalkan (*rollback*) migration khusus untuk file tersebut:
   
   ```bash
   php artisan migrate:rollback --path=/database/migrations/2026_09_06_103224_add_trainers_to_msds_documents_table.php
   ```