[[organization]]

| **NAMA KOLOM**      | **TYPE**            | **KETERANGAN**                                                                               |
| ------------------- | ------------------- | -------------------------------------------------------------------------------------------- |
| `id`                | bigint(20) UNSIGNED | Primary key, Auto Increment, Tidak boleh NULL                                                |
| `organization_guid` | char(36)            | GUID/UUID Organisasi (utf8mb4_unicode_ci), Tidak boleh NULL                                  |
| `recommendations`   | longtext            | Data rekomendasi format JSON (utf8mb4_bin), Tidak boleh NULL                                 |
| `generated_at`      | timestamp           | Waktu generasi data, Default: `current_timestamp()`, Update: `ON UPDATE CURRENT_TIMESTAMP()` |
| `created_at`        | timestamp           | Waktu pembuatan data, Boleh NULL (Default: NULL)                                             |
| `updated_at`        | timestamp           | Waktu pembaruan data, Boleh NULL (Default: NULL)                                             |
