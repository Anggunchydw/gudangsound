<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Penyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\Barang;
use App\Models\Paket;
use Carbon\Carbon;
use App\Services\InventoryService;
use Dcat\Admin\Http\Controllers\AdminController;

class PenyewaanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Penyewaan(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('nama_penyewa');
            $grid->column('no_tlp');
            $grid->column('tanggal_mulai');
            $grid->column('tanggal_selesai');
            $grid->column('lokasi');
            $grid->column('total_harga')
                ->display(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                });
            $grid->column('status_pembayaran', 'Status')
                ->display(function ($value) {

                    $class = $value == 'DP'
                        ? 'status-dp'
                        : 'status-lunas';

                    return "<span class='{$class}'>{$value}</span>";
                });
            $grid->column('status_badge', 'Status')
                ->display(function ($value) {
                    return $value;
                });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('nama_penyewa');
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {

                $status = $actions->row->status_sekarang;

                if (in_array($status, ['booking', 'berlangsung'])) {

                    $url = admin_url("penyewaan/{$actions->getKey()}/cancel");

                    $actions->append(
                        "<a class='btn btn-sm btn-danger'
                            href='{$url}'
                            onclick=\"return confirm('Batalkan penyewaan ini?')\">
                            <i class='feather icon-x-circle'></i> Batalkan
                        </a>"
                    );
                }
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, Penyewaan::with([
            'detailPaket',
            'detailBarang'
        ]), function (Show $show) {
            $show->field('id');
            $show->field('nama_penyewa');
            $show->field('no_tlp');
            $show->field('tanggal_mulai');
            $show->field('tanggal_selesai');
            $show->field('lokasi');
            $show->field('total_harga');
            $show->field('status_pembayaran');
            $show->field('status_penyewaan');

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(Penyewaan::with([
            'detailPaket',
            'detailBarang'
        ]), function (Form $form) {
            $form->display('id');
            $form->text('nama_penyewa')
                ->required();

            $form->mobile('no_tlp')
                ->required();

            $form->date('tanggal_mulai')
                ->required();

            $form->date('tanggal_selesai')
                ->required();

            $form->text('lokasi')
                ->required();

            //DETAIL PAKET
            $form->divider('Paket');

            $form->hasMany('detailPaket', 'Paket yang Disewa', function (Form\NestedForm $form) {

                $form->select('paket_id', 'Pilih Paket')
                    ->options(
                        Paket::pluck('nama_paket', 'id')
                    )
                    ->required();

                $form->number('jumlah_paket', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            //DETAIL BARANG
            $form->divider('Barang Satuan');

            $form->hasMany('detailBarang', 'Barang yang Disewa', function (Form\NestedForm $form) {

                $form->select('barang_id', 'Barang')
                    ->options(
                        Barang::pluck('nama_barang', 'id')
                    )
                    ->required();

                $form->number('jumlah_barang', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            $form->currency('total_harga')
                ->symbol('Rp')
                ->required();

            $form->radio('status_pembayaran', 'Status Pembayaran')
                ->options([
                    'DP'    => '<span class="status-dp">DP</span>',
                    'Lunas' => '<span class="status-lunas">Lunas</span>',
                ])
                ->default('DP')
                ->attribute(['class' => 'status-radio'])
                ->required();

            $form->hidden('status_penyewaan')->default('booking');


            $form->saving(function (Form $form) {

                try {

                    InventoryService::checkAvailability(

                        $form->tanggal_mulai,
                        $form->tanggal_selesai,

                        request()->input('detailBarang', []),

                        request()->input('detailPaket', []),

                        $form->model()->id // null saat tambah

                    );
                } catch (\Exception $e) {

                    return $form->response()->error($e->getMessage());
                }
            });
        });
    }
    public function cancel($id)
    {
        $penyewaan = \App\Models\Penyewaan::findOrFail($id);

        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        admin_success('Berhasil', 'Penyewaan berhasil dibatalkan.');

        return redirect(admin_url('penyewaan'));
    }
}
