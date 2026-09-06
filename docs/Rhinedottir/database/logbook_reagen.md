[[Reagens]]
[[Users]]
[[organization]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`guid`|varchar(36)|Identitas unik berbasis GUID/UUID|
|`id`|bigint(20) UNSIGNED|ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)|
|`noCatalog`|varchar(255)|Nomor katalog produk/reagen|
|`reagen_guid`|varchar(50)|Referensi GUID yang terhubung ke tabel reagen|
|`user_id`|bigint(20) UNSIGNED|ID numerik pengguna yang mengambil/mencatat reagen|
|`batch`|varchar(50)|Nomor _batch_ / produksi reagen|
|`quantity_taken`|int(10) UNSIGNED|Jumlah reagen yang diambil|
|`note`|varchar(255)|Catatan atau keterangan tambahan pengambilan|
|`created_at`|timestamp|Waktu transaksi/data pertama kali dibuat|
|`updated_at`|timestamp|Waktu transaksi/data terakhir diubah|
|`user_guid`|varchar(36)|Referensi GUID pengguna yang mencatat data|
|`organization_guid`|varchar(50)|Referensi GUID organisasi/lembaga terkait|
