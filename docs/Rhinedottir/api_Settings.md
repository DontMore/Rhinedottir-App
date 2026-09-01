
| **NAMA KOLOM**    | **TYPE**     | **KETERANGAN**                                                                                       |
| ----------------- | ------------ | ---------------------------------------------------------------------------------------------------- |
| `guid`            | char(36)     | Identitas unik berbasis GUID/UUID (36 karakter)                                                      |
| `api_token`       | varchar(80)  | Token rahasia untuk autentikasi API                                                                  |
| `gas_web_app_url` | varchar(255) | URL aplikasi web Google Apps Script (GAS)                                                            |
| `is_active`       | tinyint(1)   | Status keaktifan integrasi/layanan (0: Nonaktif, 1: Aktif, Default: 0)                               |
| `push_is_active`  | tinyint(1)   | Status keaktifan fitur pengiriman/sinkronisasi otomatis (_push_) (0: Nonaktif, 1: Aktif, Default: 0) |
| `push_interval`   | varchar(255) | Frekuensi interval pengiriman data (Default: daily / harian)                                         |
| `last_push_at`    | timestamp    | Waktu terakhir kali data berhasil dikirim (_push_)                                                   |
| `selected_tables` | longtext     | Daftar tabel yang dipilih untuk disinkronkan (format JSON/Data terstruktur)                          |
| `created_at`      | timestamp    | Waktu konfigurasi pertama kali dibuat                                                                |
| `updated_at`      | timestamp    | Waktu konfigurasi terakhir diubah                                                                    |