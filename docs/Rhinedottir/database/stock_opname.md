[[organization]]
[[Users]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`guid`|varchar(36)|Unique Identifier / GUID (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)|
|`id`|bigint(20) UNSIGNED|Primary key, Auto Increment, Tidak boleh NULL|
|`month`|int(11)|Bulan, Boleh NULL (Default: 0)|
|`year`|int(11)|Tahun, Tidak boleh NULL (Default: 0)|
|`user_id`|bigint(20) UNSIGNED|ID Pengguna (Foreign Key), Tidak boleh NULL|
|`status`|tinyint(1)|Status penanda/flag, Tidak boleh NULL (Default: 0)|
|`created_at`|timestamp|Waktu pembuatan data, Boleh NULL (Default: NULL)|
|`updated_at`|timestamp|Waktu pembaruan data, Boleh NULL (Default: NULL)|
|`organization_guid`|varchar(50)|GUID Organisasi (utf8mb4_unicode_ci), Boleh NULL (Default: NULL)|
