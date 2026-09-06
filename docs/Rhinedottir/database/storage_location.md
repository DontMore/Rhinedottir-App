[[organization]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`id`|bigint(20) UNSIGNED|Primary key, Auto Increment, Tidak boleh NULL|
|`guid`|char(36)|Unique Identifier / GUID (utf8mb4_unicode_ci), Tidak boleh NULL|
|`code`|varchar(50)|Kode pengenal (utf8mb4_unicode_ci), Tidak boleh NULL|
|`name`|varchar(100)|Nama (utf8mb4_unicode_ci), Tidak boleh NULL|
|`state_type`|enum('solid', 'liquid')|Tipe wujud/fase ('solid', 'liquid') (utf8mb4_unicode_ci), Tidak boleh NULL|
|`allowed_hazards`|longtext|Bahaya yang diizinkan format JSON (utf8mb4_bin), Boleh NULL (Default: NULL)|
|`capacity`|int(11)|Kapasitas, Boleh NULL (Default: NULL)|
|`description`|text|Deskripsi / Keterangan tambahan (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)|
|`organization_guid`|char(36)|GUID Organisasi (utf8mb4_unicode_ci), Tidak boleh NULL|
|`created_at`|timestamp|Waktu pembuatan data, Boleh NULL (Default: NULL)|
|`updated_at`|timestamp|Waktu pembaruan data, Boleh NULL (Default: NULL)|
