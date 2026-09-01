[[Reagens]]
[[Users]]

|**NAMA KOLOM**|**TYPE**|**KETERANGAN**|
|---|---|---|
|`guid`|varchar(36)|Identitas unik berbasis GUID/UUID|
|`id`|bigint(20) UNSIGNED|ID numerik utama (_Primary Key_, bertambah otomatis / _Auto Increment_)|
|`month`|int(11)|Bulan pencatatan/laporan (format angka, misal: 1-12)|
|`year`|int(11)|Tahun pencatatan/laporan (misal: 2026)|
|`noCatalog`|varchar(255)|Nomor katalog produk/reagen|
|`reagen_guid`|varchar(50)|Referensi GUID yang terhubung ke tabel reagen|
|`quantity`|int(11)|Jumlah stok awal/tersedia (Default: 0)|
|`quantity_in`|int(11)|Jumlah barang masuk (Default: 0)|
|`quantity_out`|int(11)|Jumlah barang keluar (Default: 0)|
|`quantity_actual`|int(11)|Jumlah stok fisik hasil perhitungan/realitas (Default: 0)|
|`status`|varchar(50)|Status catatan/laporan stok (Default: 0)|
|`stock_opname`|tinyint(1)|Indikator/flag status _stock opname_ (0: Belum/Tidak, 1: Ya)|
|`catatan`|text|Catatan atau keterangan tambahan|
|`user_id`|varchar(50)|ID/GUID pengguna yang mencatat data|
|`created_at`|timestamp|Waktu data pertama kali dibuat|
|`updated_at`|timestamp|Waktu data terakhir diubah|
|`organization_guid`|varchar(50)|Referensi GUID organisasi/lembaga terkait|
