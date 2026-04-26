# TODO: Perbaikan Dashboard

## Langkah-langkah:
- [x] 1. Fix `dashboard.blade.php` — Ubah `@push('styles')` jadi inline `<style>`
- [x] 2. Fix `DashboardController.php` — Tambah filter `organization_guid` pada query `$zeroStockReagen` dan `$stocks`
- [x] 3. Fix `DashboardController.php` — Hapus unused `$labels` dan `$data` dari compact()
- [x] 4. Verifikasi perubahan
