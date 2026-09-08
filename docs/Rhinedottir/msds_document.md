# Struktur Tabel: `msds_documents` (Database: `rhinedottir`)

Berikut adalah struktur tabel berdasarkan file SQL yang diberikan:

[[Reagens]]

| NAMA KOLOM | TYPE | KETERANGAN |
| :--- | :--- | :--- |
| `id` | bigint unsigned | Primary Key, Auto Increment |
| `reagen_guid` | char(36) | Foreign Key (merujuk ke `reagens.guid`), Index, Tidak boleh kosong |
| `file_path` | varchar(255) | Path atau lokasi file disimpan, Tidak boleh kosong |
| `file_name` | varchar(255) | Nama file dokumen, Tidak boleh kosong |
| `file_size` | bigint unsigned | Ukuran file, Tidak boleh kosong |
| `file_type` | varchar(10) | Tipe atau ekstensi file, Tidak boleh kosong |
| `title` | varchar(255) | Judul dokumen, Boleh kosong (Nullable) |
| `trainers` | json | Data *trainers* dalam format JSON, Boleh kosong (Nullable) |
| `uploaded_by` | char(36) | ID pengguna yang mengunggah, Index, Boleh kosong (Nullable) |
| `created_at` | timestamp | Waktu pencatatan data dibuat, Boleh kosong (Nullable) |
| `updated_at` | timestamp | Waktu pencatatan data terakhir diubah, Boleh kosong (Nullable) |