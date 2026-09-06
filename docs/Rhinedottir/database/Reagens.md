[[Reagen_group]]
[[Reagen_categories]]
[[organization]]

| **NAMA KOLOM**                 | **TYPE**     | **KETERANGAN**                                                        |
| ------------------------------ | ------------ | --------------------------------------------------------------------- |
| `guid`                         | varchar(50)  | Identitas unik (ID/GUID), opsional (Default: NULL)                    |
| `noCatalog`                    | varchar(255) | Nomor katalog produk                                                  |
| `nameReagen`                   | varchar(255) | Nama reagen                                                           |
| `merk`                         | varchar(255) | Merek atau pembuat produk                                             |
| `packSize`                     | varchar(255) | Ukuran kemasan                                                        |
| `hazardOptions`                | varchar(255) | Opsi/klasifikasi bahaya                                               |
| `msds`                         | varchar(255) | Dokumen/tautan Lembar Data Keselamatan (_Material Safety Data Sheet_) |
| `signal_word`                  | varchar(25)  | Kata sinyal bahaya (misal: _Danger_, _Warning_)                       |
| `price`                        | varchar(255) | Harga produk                                                          |
| `buffer_stock`                 | int(11)      | Stok penyangga/minimal (Default: 0)                                   |
| `created_at`                   | timestamp    | Waktu pembuatan data                                                  |
| `updated_at`                   | timestamp    | Waktu pembaruan data terakhir                                         |
| `organization_guid`            | varchar(50)  | Referensi GUID organisasi/lembaga                                     |
| `group_guid`                   | varchar(50)  | Referensi GUID kelompok/grup                                          |
| `category_guid`                | varchar(50)  | Referensi GUID kategori                                               |
| `storage_location_guid`        | varchar(50)  | Referensi GUID lokasi penyimpanan saat ini                            |
| `reagent_form`                 | varchar(50)  | Bentuk fisik reagen (misal: cair, bubuk)                              |
| `recommended_storage_location` | varchar(50)  | Rekomendasi lokasi penyimpanan yang disarankan                        |
