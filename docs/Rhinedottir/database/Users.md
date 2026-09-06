[[Reagens]]
[[Reagen_in]]
[[Stock_histories]]
[[organization]]

| **NAMA KOLOM**      | **TYPE**            | **KETERANGAN**                                                          |
| ------------------- | ------------------- | ----------------------------------------------------------------------- |
| `guid`              | varchar(50)         | Identitas unik berbasis GUID/UUID (Default: 0)                          |
| `id`                | bigint(20) UNSIGNED | ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_) |
| `name`              | varchar(255)        | Nama lengkap pengguna                                                   |
| `username`          | varchar(255)        | Nama pengguna untuk login                                               |
| `email`             | varchar(100)        | Alamat email pengguna                                                   |
| `is_active`         | tinyint(1)          | Status keaktifan akun (1: Aktif, 0: Nonaktif, Default: 1)               |
| `last_active_at`    | timestamp           | Waktu terakhir kali pengguna aktif/login                                |
| `password`          | varchar(255)        | Kata sandi pengguna (terenkripsi/hash)                                  |
| `role`              | varchar(255)        | Peran/hak akses pengguna (misal: Admin, User)                           |
| `organization_guid` | varchar(50)         | Referensi GUID organisasi/lembaga terkait                               |
| `remember_token`    | varchar(100)        | Token untuk fitur _remember me_ saat login                              |
| `created_at`        | timestamp           | Waktu data akun pertama kali dibuat                                     |
| `updated_at`        | timestamp           | Waktu data akun terakhir diubah                                         |
