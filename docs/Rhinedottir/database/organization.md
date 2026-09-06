
|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`guid`|varchar(36)|Unique Identifier / GUID (utf8mb4_unicode_ci), Tidak boleh NULL|
|`name`|varchar(255)|Nama (utf8mb4_unicode_ci), Tidak boleh NULL|
|`note`|text|Catatan (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)|
|`created_at`|timestamp|Waktu pemuatan data, Boleh NULL (Default: NULL)|
|`updated_at`|timestamp|Waktu pembaruan data, Boleh NULL (Default: NULL)|