[[Reagens]]
[[Users]]
[[organization]]

| **NAMA KOLOM**      | **TYPE**            | **KETERANGAN**                                                               |
| ------------------- | ------------------- | ---------------------------------------------------------------------------- |
| `guid`              | varchar(36)         | Identitas unik berbasis GUID/UUID                                            |
| `id`                | bigint(20) UNSIGNED | ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)      |
| `noCatalog`         | varchar(255)        | Nomor katalog produk/reagen                                                  |
| `nameReagen`        | varchar(255)        | Nama reagen                                                                  |
| `merk`              | varchar(255)        | Merek atau pembuat reagen                                                    |
| `packSize`          | varchar(255)        | Ukuran kemasan reagen                                                        |
| `quantity`          | int(11)             | Jumlah / kuantitas barang                                                    |
| `userId`            | bigint(20) UNSIGNED | ID numerik pengguna yang menginput/mengelola data                            |
| `user_guid`         | varchar(50)         | Referensi GUID pengguna terkait                                              |
| `status`            | tinyint(1)          | Status entri/transaksi (0: Nonaktif/Pending, 1: Aktif/Disetujui, Default: 0) |
| `created_at`        | timestamp           | Waktu data pertama kali dibuat                                               |
| `updated_at`        | timestamp           | Waktu data terakhir diubah                                                   |
| `organization_guid` | varchar(50)         | Referensi GUID organisasi/lembaga terkait                                    |
