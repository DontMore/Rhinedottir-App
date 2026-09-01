| **NAMA KOLOM**      | **TYPE**            | **KETERANGAN**                                                          |
| ------------------- | ------------------- | ----------------------------------------------------------------------- |
| `guid`              | varchar(50)         | Identitas unik berbasis GUID/UUID                                       |
| `mail_host`         | varchar(255)        | Server pengirim email/SMTP (Default: smtp.gmail.com)                    |
| `mail_port`         | int(11)             | Port server pengirim email (Default: 587)                               |
| `mail_encryption`   | varchar(255)        | Protokol enkripsi email (Default: tls)                                  |
| `id`                | bigint(20) UNSIGNED | ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_) |
| `mail_username`     | varchar(255)        | Username/email untuk otentikasi SMTP                                    |
| `mail_password`     | varchar(255)        | Password/App Password untuk otentikasi SMTP                             |
| `mail_from_address` | varchar(255)        | Alamat email pengirim (_sender address_)                                |
| `created_at`        | timestamp           | Waktu konfigurasi pertama kali dibuat                                   |
| `updated_at`        | timestamp           | Waktu konfigurasi terakhir diubah                                       |
