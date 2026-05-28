<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */
namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection nama_barang
     * @property Grid\Column|Collection Kategori
     * @property Grid\Column|Collection satuan
     * @property Grid\Column|Collection jumlah_total
     * @property Grid\Column|Collection stok_tersedia
     * @property Grid\Column|Collection status
     * @property Grid\Column|Collection keterangan
     * @property Grid\Column|Collection paket_id
     * @property Grid\Column|Collection barang_id
     * @property Grid\Column|Collection jumlah
     * @property Grid\Column|Collection penyewaan_id
     * @property Grid\Column|Collection jumlah_barang
     * @property Grid\Column|Collection uuid
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection detail_penyewaan_id
     * @property Grid\Column|Collection kondisi_sebelum
     * @property Grid\Column|Collection kondisi_sesudah
     * @property Grid\Column|Collection catatan
     * @property Grid\Column|Collection nama_paket
     * @property Grid\Column|Collection deskripsi
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection tanggal_masuk
     * @property Grid\Column|Collection jenis_pembayaran
     * @property Grid\Column|Collection jumlah_pengeluaran
     * @property Grid\Column|Collection tanggal_pengeluaran
     * @property Grid\Column|Collection kategori
     * @property Grid\Column|Collection tim
     * @property Grid\Column|Collection nama_penyewa
     * @property Grid\Column|Collection no_tlp
     * @property Grid\Column|Collection tanggal_mulai
     * @property Grid\Column|Collection tanggal_selesai
     * @property Grid\Column|Collection lokasi
     * @property Grid\Column|Collection total_harga
     * @property Grid\Column|Collection status_pembayaran
     * @property Grid\Column|Collection tokenable_type
     * @property Grid\Column|Collection tokenable_id
     * @property Grid\Column|Collection abilities
     * @property Grid\Column|Collection last_used_at
     * @property Grid\Column|Collection expires_at
     * @property Grid\Column|Collection ip_address
     * @property Grid\Column|Collection user_agent
     * @property Grid\Column|Collection last_activity
     * @property Grid\Column|Collection email_verified_at
     * @property Grid\Column|Collection nama_lengkap
     * @property Grid\Column|Collection role
     *
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection nama_barang(string $label = null)
     * @method Grid\Column|Collection Kategori(string $label = null)
     * @method Grid\Column|Collection satuan(string $label = null)
     * @method Grid\Column|Collection jumlah_total(string $label = null)
     * @method Grid\Column|Collection stok_tersedia(string $label = null)
     * @method Grid\Column|Collection status(string $label = null)
     * @method Grid\Column|Collection keterangan(string $label = null)
     * @method Grid\Column|Collection paket_id(string $label = null)
     * @method Grid\Column|Collection barang_id(string $label = null)
     * @method Grid\Column|Collection jumlah(string $label = null)
     * @method Grid\Column|Collection penyewaan_id(string $label = null)
     * @method Grid\Column|Collection jumlah_barang(string $label = null)
     * @method Grid\Column|Collection uuid(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection detail_penyewaan_id(string $label = null)
     * @method Grid\Column|Collection kondisi_sebelum(string $label = null)
     * @method Grid\Column|Collection kondisi_sesudah(string $label = null)
     * @method Grid\Column|Collection catatan(string $label = null)
     * @method Grid\Column|Collection nama_paket(string $label = null)
     * @method Grid\Column|Collection deskripsi(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection tanggal_masuk(string $label = null)
     * @method Grid\Column|Collection jenis_pembayaran(string $label = null)
     * @method Grid\Column|Collection jumlah_pengeluaran(string $label = null)
     * @method Grid\Column|Collection tanggal_pengeluaran(string $label = null)
     * @method Grid\Column|Collection kategori(string $label = null)
     * @method Grid\Column|Collection tim(string $label = null)
     * @method Grid\Column|Collection nama_penyewa(string $label = null)
     * @method Grid\Column|Collection no_tlp(string $label = null)
     * @method Grid\Column|Collection tanggal_mulai(string $label = null)
     * @method Grid\Column|Collection tanggal_selesai(string $label = null)
     * @method Grid\Column|Collection lokasi(string $label = null)
     * @method Grid\Column|Collection total_harga(string $label = null)
     * @method Grid\Column|Collection status_pembayaran(string $label = null)
     * @method Grid\Column|Collection tokenable_type(string $label = null)
     * @method Grid\Column|Collection tokenable_id(string $label = null)
     * @method Grid\Column|Collection abilities(string $label = null)
     * @method Grid\Column|Collection last_used_at(string $label = null)
     * @method Grid\Column|Collection expires_at(string $label = null)
     * @method Grid\Column|Collection ip_address(string $label = null)
     * @method Grid\Column|Collection user_agent(string $label = null)
     * @method Grid\Column|Collection last_activity(string $label = null)
     * @method Grid\Column|Collection email_verified_at(string $label = null)
     * @method Grid\Column|Collection nama_lengkap(string $label = null)
     * @method Grid\Column|Collection role(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection id
     * @property Show\Field|Collection name
     * @property Show\Field|Collection version
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection type
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection order
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection username
     * @property Show\Field|Collection password
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection nama_barang
     * @property Show\Field|Collection Kategori
     * @property Show\Field|Collection satuan
     * @property Show\Field|Collection jumlah_total
     * @property Show\Field|Collection stok_tersedia
     * @property Show\Field|Collection status
     * @property Show\Field|Collection keterangan
     * @property Show\Field|Collection paket_id
     * @property Show\Field|Collection barang_id
     * @property Show\Field|Collection jumlah
     * @property Show\Field|Collection penyewaan_id
     * @property Show\Field|Collection jumlah_barang
     * @property Show\Field|Collection uuid
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection detail_penyewaan_id
     * @property Show\Field|Collection kondisi_sebelum
     * @property Show\Field|Collection kondisi_sesudah
     * @property Show\Field|Collection catatan
     * @property Show\Field|Collection nama_paket
     * @property Show\Field|Collection deskripsi
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection tanggal_masuk
     * @property Show\Field|Collection jenis_pembayaran
     * @property Show\Field|Collection jumlah_pengeluaran
     * @property Show\Field|Collection tanggal_pengeluaran
     * @property Show\Field|Collection kategori
     * @property Show\Field|Collection tim
     * @property Show\Field|Collection nama_penyewa
     * @property Show\Field|Collection no_tlp
     * @property Show\Field|Collection tanggal_mulai
     * @property Show\Field|Collection tanggal_selesai
     * @property Show\Field|Collection lokasi
     * @property Show\Field|Collection total_harga
     * @property Show\Field|Collection status_pembayaran
     * @property Show\Field|Collection tokenable_type
     * @property Show\Field|Collection tokenable_id
     * @property Show\Field|Collection abilities
     * @property Show\Field|Collection last_used_at
     * @property Show\Field|Collection expires_at
     * @property Show\Field|Collection ip_address
     * @property Show\Field|Collection user_agent
     * @property Show\Field|Collection last_activity
     * @property Show\Field|Collection email_verified_at
     * @property Show\Field|Collection nama_lengkap
     * @property Show\Field|Collection role
     *
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection nama_barang(string $label = null)
     * @method Show\Field|Collection Kategori(string $label = null)
     * @method Show\Field|Collection satuan(string $label = null)
     * @method Show\Field|Collection jumlah_total(string $label = null)
     * @method Show\Field|Collection stok_tersedia(string $label = null)
     * @method Show\Field|Collection status(string $label = null)
     * @method Show\Field|Collection keterangan(string $label = null)
     * @method Show\Field|Collection paket_id(string $label = null)
     * @method Show\Field|Collection barang_id(string $label = null)
     * @method Show\Field|Collection jumlah(string $label = null)
     * @method Show\Field|Collection penyewaan_id(string $label = null)
     * @method Show\Field|Collection jumlah_barang(string $label = null)
     * @method Show\Field|Collection uuid(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection detail_penyewaan_id(string $label = null)
     * @method Show\Field|Collection kondisi_sebelum(string $label = null)
     * @method Show\Field|Collection kondisi_sesudah(string $label = null)
     * @method Show\Field|Collection catatan(string $label = null)
     * @method Show\Field|Collection nama_paket(string $label = null)
     * @method Show\Field|Collection deskripsi(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection tanggal_masuk(string $label = null)
     * @method Show\Field|Collection jenis_pembayaran(string $label = null)
     * @method Show\Field|Collection jumlah_pengeluaran(string $label = null)
     * @method Show\Field|Collection tanggal_pengeluaran(string $label = null)
     * @method Show\Field|Collection kategori(string $label = null)
     * @method Show\Field|Collection tim(string $label = null)
     * @method Show\Field|Collection nama_penyewa(string $label = null)
     * @method Show\Field|Collection no_tlp(string $label = null)
     * @method Show\Field|Collection tanggal_mulai(string $label = null)
     * @method Show\Field|Collection tanggal_selesai(string $label = null)
     * @method Show\Field|Collection lokasi(string $label = null)
     * @method Show\Field|Collection total_harga(string $label = null)
     * @method Show\Field|Collection status_pembayaran(string $label = null)
     * @method Show\Field|Collection tokenable_type(string $label = null)
     * @method Show\Field|Collection tokenable_id(string $label = null)
     * @method Show\Field|Collection abilities(string $label = null)
     * @method Show\Field|Collection last_used_at(string $label = null)
     * @method Show\Field|Collection expires_at(string $label = null)
     * @method Show\Field|Collection ip_address(string $label = null)
     * @method Show\Field|Collection user_agent(string $label = null)
     * @method Show\Field|Collection last_activity(string $label = null)
     * @method Show\Field|Collection email_verified_at(string $label = null)
     * @method Show\Field|Collection nama_lengkap(string $label = null)
     * @method Show\Field|Collection role(string $label = null)
     */
    class Show {}

    /**
     
     */
    class Form {}

}

namespace Dcat\Admin\Grid {
    /**
     
     */
    class Column {}

    /**
     
     */
    class Filter {}
}

namespace Dcat\Admin\Show {
    /**
     
     */
    class Field {}
}
