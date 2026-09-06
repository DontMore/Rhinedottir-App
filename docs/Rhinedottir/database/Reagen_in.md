[[Reagens]]
[[organization]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`guid`|varchar(50)|Identitas unik (GUID/ID)|
|`Id`|bigint(20) UNSIGNED|ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)|
|`noCatalog`|varchar(255)|Nomor katalog produk|
|`reagen_guid`|char(36)|Referensi GUID yang terhubung ke tabel reagen|
|`batch`|varchar(255)|Nomor _batch_ / produksi produk|
|`quantity`|int(11)|Jumlah / stok barang|
|`expiredDate`|date|Tanggal kadaluwarsa produk|
|`note`|text|Catatan tambahan|
|`created_at`|timestamp|Waktu data pertama kali dibuat|
|`updated_at`|timestamp|Waktu data terakhir diubah|
|`user_id`|int(11)|ID pengguna yang mencatat/mengelola data (Default: 1)|
|`organization_guid`|varchar(50)|Referensi GUID organisasi/lembaga|
