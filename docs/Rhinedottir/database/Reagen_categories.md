[[Reagens]]
[[organization]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`id`|bigint(20) UNSIGNED|ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)|
|`guid`|char(36)|Identitas unik berbasis GUID/UUID (36 karakter)|
|`name`|varchar(100)|Nama entitas / entri|
|`organization_guid`|char(36)|Referensi GUID organisasi terkait|
|`created_at`|timestamp|Waktu data pertama kali dibuat|
|`updated_at`|timestamp|Waktu data terakhir diubah|
