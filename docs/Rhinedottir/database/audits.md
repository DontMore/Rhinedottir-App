[[Users]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`id`|bigint(20) UNSIGNED|ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)|
|`user_type`|varchar(255)|Tipe atau model dari pengguna yang melakukan aksi (_polymorphic class_)|
|`user_id`|char(36)|Referensi GUID/ID pengguna yang melakukan aksi|
|`event`|varchar(255)|Jenis aksi/kejadian yang terjadi (misal: _created_, _updated_, _deleted_)|
|`auditable_type`|varchar(255)|Tipe atau nama model/tabel dari data yang diubah|
|`auditable_id`|char(36)|GUID/ID dari data spesifik yang diubah|
|`old_values`|text|Data lama sebelum perubahan (biasanya format JSON)|
|`new_values`|text|Data baru setelah perubahan (biasanya format JSON)|
|`url`|text|URL endpoint/halaman yang diakses saat aksi dilakukan|
|`ip_address`|varchar(45)|Alamat IP pengguna (_supports IPv4 & IPv6_)|
|`user_agent`|varchar(1023)|Informasi _browser_/perangkat yang digunakan pengguna|
|`tags`|varchar(255)|Label atau tag tambahan untuk mengelompokkan log audit|
|`created_at`|timestamp|Waktu log audit pertama kali dicatat|
|`updated_at`|timestamp|Waktu log audit terakhir diubah|
