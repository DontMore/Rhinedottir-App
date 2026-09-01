[[Reagens]]
[[organization]]

| **NAMA KOLOM**      | **TYPE**            | **KETERANGAN**                                                            |
| ------------------- | ------------------- | ------------------------------------------------------------------------- |
| `guid`              | varchar(36)         | Unique Identifier / GUID (utf8mb4_unicode_ci), Boleh NULL (Default: NULL) |
| `stockId`           | bigint(20) UNSIGNED | Primary key, Auto Increment, Tidak boleh NULL                             |
| `noCatalog`         | varchar(255)        | Nomor Katalog (utf8mb4_unicode_ci), Tidak boleh NULL                      |
| `reagen_guid`       | varchar(50)         | GUID Reagen (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)              |
| `quantity`          | int(11)             | Jumlah stok / Kuantitas, Tidak boleh NULL                                 |
| `created_at`        | timestamp           | Waktu pembuatan data, Boleh NULL (Default: NULL)                          |
| `updated_at`        | timestamp           | Waktu pembaruan data, Boleh NULL (Default: NULL)                          |
| `organization_guid` | varchar(50)         | GUID Organisasi (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)          |
