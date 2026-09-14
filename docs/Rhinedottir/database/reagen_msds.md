# Dokumentasi Struktur Tabel `reagen_msds`

Dokumen ini berisi penjelasan struktur tabel **`reagen_msds`** dari database `reagen-app2`.

---

## 📋 Table Structure: `reagen_msds`

[[Reagens]]

| NAMA KOLOM | TYPE | KETERANGAN |
| :--- | :--- | :--- |
| `id` | `bigint(20) UNSIGNED` | Primary Key, Auto Increment. Identifikasi unik untuk setiap record dokumen MSDS. |
| `reagen_guid` | `char(36)` | Foreign Key terhubung ke `reagens(guid)` dengan aksi `ON DELETE CASCADE`. GUID referensi reagen terkait. |
| `file_name` | `varchar(255)` | Nama file dokumen MSDS yang diunggah. |
| `file_path` | `varchar(255)` | Lokasi penyimpanan/path file MSDS pada sistem/storage. |
| `version` | `varchar(255)` | Versi dokumen MSDS (dapat berisi NULL). |
| `revision_date` | `date` | Tanggal revisi/pembaruan dokumen MSDS (dapat berisi NULL). |
| `notes` | `text` | Catatan atau keterangan tambahan mengenai dokumen MSDS (dapat berisi NULL). |
| `is_latest` | `tinyint(1)` | Penanda status apakah file ini merupakan revisi/versi terbaru (Default: `0`). |
| `organization_guid` | `char(36)` | GUID identifikasi organisasi pemilik data MSDS. |
| `uploaded_by` | `bigint(20) UNSIGNED` | ID pengguna yang mengunggah dokumen MSDS (dapat berisi NULL). |
| `created_at` | `timestamp` | Waktu/tanggal data pertama kali dibuat (dapat berisi NULL). |
| `updated_at` | `timestamp` | Waktu/tanggal data terakhir kali diperbarui (dapat berisi NULL). |

---

## 🔑 Indeks & Relasi Database

### 1. Primary Key
- **Kolom**: `id`

### 2. Foreign Key & Constraints
- **Constraint**: `reagen_msds_reagen_guid_foreign`
  - **Kolom Local**: `reagen_guid`
  - **Tabel Referensi**: `reagens` (`guid`)
  - **Aksi**: `ON DELETE CASCADE` (jika data pada tabel `reagens` dihapus, data MSDS terkait juga otomatis terhapus).

### 3. Index (Kunci Pencarian)
- **Index Name**: `reagen_msds_reagen_guid_is_latest_index`
  - **Kolom**: `reagen_guid`, `is_latest`
  - **Tujuan**: Mengoptimalkan pencarian dokumen MSDS terbaru berdasarkan `reagen_guid`.