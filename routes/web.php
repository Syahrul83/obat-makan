<?php

use App\Models\PasienModel as Pasien;
use App\Models\DokterModel as Dokter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/harga-obat',function(){
// 	$obat = App\Models\ObatModel::where('status_delete',0)->get();
// 	foreach ($obat as $key => $value) {
// 		$hja_upds     = $value->harga_modal_ppn + (($value->harga_modal_ppn * $value->margin_upds) / 100);
// 		$margin_resep = $value->margin_resep == 0 ? $value->margin_upds : $value->margin_resep;
// 		if ($value->margin_resep == 0) {
// 			$hja_resep = $hja_upds;
// 		}
// 		else {
// 			$hja_upds     = $value->harga_modal_ppn + (($value->harga_modal_ppn * $value->margin_resep) / 100);
// 		}

// 		$data_update = [
// 			'hja_upds'     => $hja_upds,
// 			'margin_resep' => $margin_resep,
// 			'hja_resep'    => $hja_resep
// 		];

// 		App\Models\ObatModel::where('id_obat',$value->id_obat)->update($data_update);
// 	}
// });

// Route::get('/test-harga',function(){
// 	$obat = App\Models\ObatModel::where('status_delete',0)->where('id_obat',4)->get();
// });

// Route::get('/test-array',function() {
// 	$userdb=Array
// 			(
// 			0 => Array
// 			    (
// 			        'uid' => '100',
// 			        'name' => 'Sandra Shush',
// 			        'url' => 'urlof100'
// 			    ),

// 			1 => Array
// 			    (
// 			        'uid' => '5465',
// 			        'name' => 'Stefanie Mcmohn',
// 			        'pic_square' => 'urlof100'
// 			    ),

// 			2 => Array
// 			    (
// 			        'uid' => '40489',
// 			        'name' => 'Michael',
// 			        'pic_square' => 'urlof40489'
// 			    )
// 			);

// 			$key = array_search(2, array_column($userdb, 'uid'));
// 			if ($key == null) {
// 				dd('oke');
// 			}

// 			echo ("The key is: ".$key);
// });

Route::get('/generate-tanggal-input', function () {
    $get = App\Models\PembelianObatModel::where('tanggal_input', '0000-00-00')->get();
    foreach ($get as $key => $value) {
        App\Models\PembelianObatModel::where('id_pembelian_obat', $value->id_pembelian_obat)->update([
            'tanggal_input' => $value->tanggal_terima
        ]);
    }
    echo "Berhasil";
});

// Route::get('/test', function () {
// 	dd(request()->segments()[0]);
// });

// Route::get('/cari-supplier', function () {
//     $supplier = App\Models\ObatDetailModel::getShuffleByObat(8);
// });

// Route::get('/coba-diskon',function(){
// });

// use App\Models\KreditDetailModel as KreditDetail;
// use App\Models\KreditFakturModel as KreditFaktur;

Route::post('/reset-token', function () {
    return csrf_token();
});

// Route::get('/insert-kredit-faktur',function(){
// 	$get_id_kredit_distinct = KreditDetail::distinct()->get(['id_kredit']);

// 	$no = 1;
// 	foreach ($get_id_kredit_distinct as $key => $value) {
// 		$get_tanggal_kredit = DB::select("SELECT tanggal_jatuh_tempo, DATE(created_at) created_at FROM kredit_det WHERE id_kredit=$value->id_kredit GROUP BY created_at");

// 		foreach ($get_tanggal_kredit as $index => $val) {
// 			$nomor_faktur   = generateCode('KRD','-',$no,11);

// 			$data_kredit_faktur = [
// 				'nomor_faktur'	 => $nomor_faktur,
// 				'id_kredit'      => $value->id_kredit,
// 				'tanggal_faktur' => $val->created_at,
// 			];

// 			$id_kredit_faktur = KreditFaktur::insertGetId($data_kredit_faktur);

// 			KreditDetail::where('id_kredit',$value->id_kredit)
// 						->where('tanggal_jatuh_tempo',$val->tanggal_jatuh_tempo)
// 						->whereDate('created_at',$val->created_at)
// 						->update(['id_kredit_faktur'=>$id_kredit_faktur]);

// 			$no = $no+1;
// 		}
// 	}
// });

Route::get('/ngetes-jam', function () {
    echo App\Models\JamShiftModel::getIdJamShift();
});

Route::get('/ngetes-mod', function () {
    // echo 320 % 160;
});

Route::get('/str-limit', function () {});

Route::get('/test-diskon', function () {
    // echo round_up_thousand(10548,1000);
    // echo '<br>'.round_up_thousand(1548,1000).'<br>';
    // echo round_up_thousand(10548-1548,1000).'<br>';
});

Route::group(['middleware' => 'isLogin'], function () {
    Route::get('/', ['as' => 'login','uses' => 'AuthController@index']);
    Route::get('/login', ['as' => 'login','uses' => 'AuthController@index']);
    Route::post('/login/auth', ['as' => 'login-auth','uses' => 'AuthController@login']);
});
Route::get('/logout', ['as' => 'logout','uses' => 'AuthController@logout']);

Route::group(['prefix' => 'datatables'], function () {
    Route::get('/data-supplier-obat', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataSupplierObat']);
    Route::get('/data-pabrik-obat', ['as' => 'data-pabrik-obat-datatables','uses' => 'AjaxController@dataPabrikObat']);
    Route::get('/data-margin-obat', ['as' => 'data-margin-obat-datatables','uses' => 'AjaxController@dataMarginObat']);
    Route::get('/data-obat', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataObat']);
    Route::get('/data-obat/detail/{id}', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataObatDetail']);
    Route::get('/data-golongan-obat', ['as' => 'data-golongan-obat-datatables','uses' => 'AjaxController@dataGolonganObat']);
    Route::get('/data-obat/komposisi-obat/{id}', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataKomposisiObat']);
    Route::get('/data-jenis-obat', ['as' => 'get-obat-ajax','uses' => 'AjaxController@dataJenisObat']);
    Route::get('/data-pembelian-obat', ['as' => 'get-obat-ajax','uses' => 'AjaxController@dataPembelianObat']);
    Route::get('/data-pembelian-obat/detail/{id}', ['as' => 'get-obat-ajax','uses' => 'AjaxController@dataPembelianDetail']);
    Route::get('/data-pasien', ['as' => 'data-pasien-datatables','uses' => 'AjaxController@dataPasien']);
    Route::get('/data-debitur', ['as' => 'data-debitur-datatables','uses' => 'AjaxController@dataDebitur']);
    Route::get('/data-dokter', ['as' => 'data-dokter-datatables','uses' => 'AjaxController@dataDokter']);
    Route::get('/data-kredit', ['as' => 'data-kredit-datatables','uses' => 'AjaxController@dataKredit']);
    Route::get('/data-kredit-panel', ['as' => 'data-kredit-datatables','uses' => 'AjaxController@dataKreditPanel']);
    Route::get('/data-kredit-faktur-panel/{id}', ['as' => 'data-kredit-datatables','uses' => 'AjaxController@dataKreditFakturPanel']);
    Route::get('/data-kredit-detail-panel/{id}', ['as' => 'data-kredit-datatables','uses' => 'AjaxController@dataKreditDetailPanel']);
    Route::get('/data-jam-shift', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataJamShift']);
    Route::get('/data-transaksi', ['as' => 'data-transaksi-datatables','uses' => 'AjaxController@dataTransaksi']);
    Route::get('/data-transaksi/{id}', ['as' => 'data-transaksi-datatables','uses' => 'AjaxController@dataTransaksiDetail']);
    Route::get('/data-transaksi-racik-obat', ['as' => 'data-transaksi-datatables','uses' => 'AjaxController@dataTransaksiRacikObat']);
    Route::get('/data-racik-obat/{id}', ['as' => 'data-transaksi-datatables','uses' => 'AjaxController@dataRacikObat']);
    Route::get('/data-racik-obat-detail/{id}', ['as' => 'data-transaksi-datatables','uses' => 'AjaxController@dataRacikObatDetail']);
    Route::get('/data-stok-opnem', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataStokOpnem']);
    Route::get('/data-stok-opnem/detail/{id}', ['as' => 'data-obat-datatables','uses' => 'AjaxController@dataStokOpnemDetail']);
    Route::get('/data-users', ['as' => 'data-users-datatables','uses' => 'AjaxController@dataUsers']);
    Route::get('/history-beli', ['as' => 'history-beli-datatables','uses' => 'AjaxController@historyBeli']);
    Route::get('/kartu-stok', ['as' => 'kartu-stok-datatables','uses' => 'AjaxController@kartuStok']);
    Route::get('/data-retur-barang', ['as' => 'data-retur-barang', 'uses' => 'AjaxController@dataReturBarang']);
    Route::get('/data-retur-barang/detail/{id}', ['as' => 'data-retur-barang', 'uses' => 'AjaxController@dataReturBarangDetail']);
    Route::get('/data-ppn', ['as' => 'data-ppn-kasir', 'uses' => 'AjaxController@dataPpn']);
});

Route::group(['prefix' => 'ajax'], function () {
    Route::get('/get-obat/{id_jenis}', ['as' => 'get-obat-ajax','uses' => 'AjaxController@getObat']);
    Route::get('/get-obat-by-supplier/{id_supplier}', ['as' => 'get-obat-ajax','uses' => 'AjaxController@getObatBySupplier']);
    Route::get('/get-detail-obat/{id}/{attr}', ['as' => 'get-obat-ajax','uses' => 'AjaxController@getDetailObat']);
    Route::get('/get-info-obat/{id_obat}', ['as' => 'get-info-obat-ajax','uses' => 'AjaxController@getInfoObat']);
    Route::post('/input-obat-beli', ['as' => 'input-obat-beli','uses' => 'AjaxController@inputObatBeli']);
    Route::get('/delete-beli/{index}', ['as' => 'delete-beli','uses' => 'AjaxController@deleteBeli']);
    Route::post('/simpan-racik', ['as' => 'simpan-racik-obat','uses' => 'AjaxController@simpanRacik']);
    Route::get('/detail-racik/{index}', ['as' => 'simpan-racik-obat','uses' => 'AjaxController@detailRacik']);
    Route::get('/delete-racik', ['as' => 'simpan-racik-obat','uses' => 'AjaxController@deleteRacik']);
    Route::post('/simpan-tanpa-racik', ['as' => 'simpan-tanpa-racik-obat','uses' => 'AjaxController@simpanTanpaRacik']);
    Route::get('/delete-tanpa-racik', ['as' => 'simpan-tanpa-racik-obat','uses' => 'AjaxController@deleteTanpaRacik']);
    Route::get('/get-obat-transaksi/{id_obat}/{pcs}/{diskon}/{btn_attr}/{attr_diskon}', ['as' => 'get-obat-transaksi-ajax','uses' => 'AjaxController@getObatTransaksi']);
    Route::get('/ubah-stok/{id}/{pcs}/{jenis}', ['as' => 'ubah-stok-obat','uses' => 'AjaxController@ubahStok']);
    Route::get('/get-kredit-faktur/{id_kredit}', ['as' => 'get-kredit-detail','uses' => 'AjaxController@getKreditFaktur']);
    Route::get('/get-detail-kredit/{id_faktur}', ['as' => 'get-kredit-detail','uses' => 'AjaxController@getDetailKredit']);
    Route::post('/bayar-kredit', ['as' => 'bayar-kredit-ajax','uses' => 'AjaxController@bayarKredit']);
    Route::get('/bayar-kredit-semua/{id}', ['as' => 'bayar-kredit-ajax','uses' => 'AjaxController@bayarSemuaKredit']);
    Route::get('/get-kode-pembelian/{jenis_beli}', ['as' => 'get-kode-pembelian','uses' => 'AjaxController@getKodePembelian']);
    Route::get('/get-nomor-transaksi', ['as' => 'get-nomor-transaksi','uses' => 'AjaxController@getNomorTransaksi']);
    Route::get('/get-obat-by-transaksi', ['as' => 'get-obat-by-transaksi','uses' => 'AjaxController@getObatByTransaksi']);
    Route::get('/get-singkatan-supplier', ['as' => 'get-singkatan-supplier','uses' => 'AjaxController@getSingkatanSupplier']);
    Route::get('/get-info-pasien-resep', ['as' => 'get-info-pasien-resep','uses' => 'AjaxController@getInfoPasienResep']);
});

// Route::get('/set-input-user')

Route::group(['middleware' => 'isAdmin','prefix' => 'admin'], function () {
    Route::get('/panel', ['uses' => 'Admin\PanelController@index']);

    // CRUD SUPPLIER OBAT //
    Route::get('/data-supplier-obat', ['as' => 'data-supplier-obat','uses' => 'Admin\SupplierObatController@index']);
    Route::get('/data-supplier-obat/tambah', ['as' => 'data-supplier-obat','uses' => 'Admin\SupplierObatController@tambah']);
    Route::get('/data-supplier-obat/edit/{id}', ['as' => 'data-supplier-obat','uses' => 'Admin\SupplierObatController@edit']);
    Route::get('/data-supplier-obat/delete/{id}', ['as' => 'data-supplier-obat','uses' => 'Admin\SupplierObatController@delete']);
    Route::post('/data-supplier-obat/save', ['as' => 'data-supplier-obat','uses' => 'Admin\SupplierObatController@save']);
    // END CRUD SUPPLIER OBAT //

    // CRUD SUPPLIER OBAT //
    Route::get('/data-pabrik-obat', ['as' => 'data-pabrik-obat','uses' => 'Admin\PabrikObatController@index']);
    Route::get('/data-pabrik-obat/tambah', ['as' => 'data-pabrik-obat','uses' => 'Admin\PabrikObatController@tambah']);
    Route::get('/data-pabrik-obat/edit/{id}', ['as' => 'data-pabrik-obat','uses' => 'Admin\PabrikObatController@edit']);
    Route::get('/data-pabrik-obat/delete/{id}', ['as' => 'data-pabrik-obat','uses' => 'Admin\PabrikObatController@delete']);
    Route::post('/data-pabrik-obat/save', ['as' => 'data-pabrik-obat','uses' => 'Admin\PabrikObatController@save']);
    // END CRUD SUPPLIER OBAT //

    // CRUD MARGIN OBAT //
    Route::get('/margin-obat', ['as' => 'margin-obat','uses' => 'Admin\MarginObatController@index']);
    Route::get('/margin-obat/tambah', ['as' => 'margin-obat','uses' => 'Admin\MarginObatController@tambah']);
    Route::get('/margin-obat/edit/{id}', ['as' => 'margin-obat','uses' => 'Admin\MarginObatController@edit']);
    Route::get('/margin-obat/delete/{id}', ['as' => 'margin-obat','uses' => 'Admin\MarginObatController@delete']);
    Route::post('/margin-obat/save', ['as' => 'margin-obat','uses' => 'Admin\MarginObatController@save']);
    // END CRUD MARGIN OBAT //

    // DATA OBAT //
    Route::get('/data-obat', ['uses' => 'Admin\ObatController@index']);
    Route::get('/data-obat/tambah', ['uses' => 'Admin\ObatController@tambah']);
    Route::post('/data-obat/save', ['uses' => 'Admin\ObatController@save']);
    Route::get('/data-obat/edit/{id}', ['uses' => 'Admin\ObatController@edit']);
    Route::get('/data-obat/delete/{id}', ['uses' => 'Admin\ObatController@delete']);
    Route::get('/data-obat/rekap-obat', ['uses' => 'Admin\ObatController@rekapObat']);
    // END DATA OBAT //

    // CRUD GOLONGAN OBAT //
    Route::get('/data-golongan-obat', ['as' => 'data-golongan-obat','uses' => 'Admin\GolonganObatController@index']);
    Route::get('/data-golongan-obat/tambah', ['as' => 'data-golongan-obat','uses' => 'Admin\GolonganObatController@tambah']);
    Route::get('/data-golongan-obat/edit/{id}', ['as' => 'data-golongan-obat','uses' => 'Admin\GolonganObatController@edit']);
    Route::get('/data-golongan-obat/delete/{id}', ['as' => 'data-golongan-obat','uses' => 'Admin\GolonganObatController@delete']);
    Route::post('/data-golongan-obat/save', ['as' => 'data-golongan-obat','uses' => 'Admin\GolonganObatController@save']);
    // END CRUD GOLONGAN OBAT //

    // DATA OBAT SUPPLIER //
    Route::get('/data-obat/lihat-supplier/{id}', ['uses' => 'Admin\ObatController@obatDetail']);
    Route::get('/data-obat/lihat-supplier/{id}/delete/{id_detail}', ['uses' => 'Admin\ObatController@deleteObatDetail']);
    // END DATA OBAT SUPPLIER //

    // DATA KOMPOSISI OBAT //
    Route::get('/data-obat/komposisi-obat/{id}', ['uses' => 'Admin\ObatController@komposisiObat']);
    Route::get('/data-obat/komposisi-obat/{id}/delete/{id_detail}', ['uses' => 'Admin\ObatController@deleteKomposisiObat']);
    // END DATA KOMPOSISI OBAT //

    // DATA OBAT //
    Route::get('/data-jenis-obat', ['uses' => 'Admin\JenisObatController@index']);
    Route::get('/data-jenis-obat/tambah', ['uses' => 'Admin\JenisObatController@tambah']);
    Route::post('/data-jenis-obat/save', ['uses' => 'Admin\JenisObatController@save']);
    Route::get('/data-jenis-obat/edit/{id}', ['uses' => 'Admin\JenisObatController@edit']);
    Route::get('/data-jenis-obat/delete/{id}', ['uses' => 'Admin\JenisObatController@delete']);
    // END DATA OBAT //

    // DATA PPN //
    Route::get('/data-ppn', ['uses' => 'Admin\PersenPpnController@index']);
    Route::get('/data-ppn/edit/{id}', ['uses' => 'Admin\PersenPpnController@edit']);
    Route::post('/data-ppn/save', ['uses' => 'Admin\PersenPpnController@save']);
    // END DATA PPN //


    // DATA PASIEN Debitur //
    Route::get('/data-debitur/tambah', ['uses' => 'Admin\DebiturController@create']);
    Route::get('/data-debitur', ['uses' => 'Admin\DebiturController@index']);
    Route::post('/data-debitur/save', ['uses' => 'Admin\DebiturController@store']);
    Route::get('/data-debitur/edit/{id}', ['uses' => 'Admin\DebiturController@edit']);
    Route::get('/data-debitur/delete/{id}', ['uses' => 'Admin\DebiturController@destroy']);
    // END DATA PASIEN debitur//

    // DATA PASIEN //
    Route::get('/data-pasien', ['uses' => 'Admin\PasienController@index']);
    Route::post('/data-pasien/save', ['uses' => 'Admin\PasienController@save']);
    Route::get('/data-pasien/edit/{id}', ['uses' => 'Admin\PasienController@edit']);
    Route::get('/data-pasien/delete/{id}', ['uses' => 'Admin\PasienController@delete']);
    // END DATA DATA PASIEN //

    // DATA DOKTER //
    Route::get('/data-dokter', ['uses' => 'Admin\DokterController@index']);
    Route::post('/data-dokter/save', ['uses' => 'Admin\DokterController@save']);
    Route::get('/data-dokter/edit/{id}', ['uses' => 'Admin\DokterController@edit']);
    Route::get('/data-dokter/delete/{id}', ['uses' => 'Admin\DokterController@delete']);
    // END DATA DOKTER //

    // ROUTE KREDIT //
    Route::get('/data-kredit', ['uses' => 'Admin\DataKreditController@index']);
    Route::get('/data-kredit/detail/{id}', ['uses' => 'Admin\DataKreditController@kreditFaktur']);
    Route::get('/data-kredit/delete/{id}', ['uses' => 'Admin\DataKreditController@delete']);
    Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}', ['uses' => 'Admin\DataKreditController@kreditDetail']);
    Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}/lunas-semua', ['uses' => 'Admin\DataKreditController@lunasSemua']);
    Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}/delete/{id_detail}', ['uses' => 'Admin\DataKreditController@deleteKreditDetail']);
    Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}/lunas-hutang/{id_detail}', ['uses' => 'Admin\DataKreditController@lunasHutang']);
    Route::get('/data-kredit/detail/{id}/cetak/{id_faktur}', ['uses' => 'Admin\DataKreditController@cetakInvoice']);
    Route::get('/data-kredit/detail/{id}/delete/{id_faktur}', ['uses' => 'Admin\DataKreditController@deleteKreditFaktur']);
    Route::get('/data-kredit/detail/{id}/cetak-kredit-range/{id_faktur}', ['uses' => 'Admin\DataKreditController@cetakKreditRange']);
    // END ROUTE KREDIT //

    // ROUTE USERS //
    Route::get('/data-users', ['as' => 'data-users','uses' => 'Admin\UsersController@index']);
    Route::get('/data-users/tambah', ['as' => 'data-users','uses' => 'Admin\UsersController@tambah']);
    Route::get('/data-users/edit/{id}', ['as' => 'data-users','uses' => 'Admin\UsersController@edit']);
    Route::get('/data-users/delete/{id}', ['as' => 'data-users','uses' => 'Admin\UsersController@delete']);
    Route::get('/data-users/status-user/{id}', ['as' => 'data-users','uses' => 'Admin\UsersController@statusUser']);
    Route::post('/data-users/save', ['as' => 'data-users','uses' => 'Admin\UsersController@save']);
    // END ROUTE USERS //

    // DATA OBAT //
    Route::get('/jam-shift', ['uses' => 'Admin\JamShiftController@index']);
    Route::get('/jam-shift/tambah', ['uses' => 'Admin\JamShiftController@tambah']);
    Route::post('/jam-shift/save', ['uses' => 'Admin\JamShiftController@save']);
    Route::get('/jam-shift/edit/{id}', ['uses' => 'Admin\JamShiftController@edit']);
    Route::get('/jam-shift/delete/{id}', ['uses' => 'Admin\JamShiftController@delete']);
    // END DATA OBAT //

    // ROUTE TRANSAKSI //
    Route::get('/data-penjualan', ['uses' => 'Admin\DataTransaksiController@index']);
    Route::get('/data-penjualan/detail/{id}', ['uses' => 'Admin\DataTransaksiController@transaksiDetail']);
    Route::get('/data-penjualan/delete/{id}', ['uses' => 'Admin\DataTransaksiController@delete']);
    Route::get('/data-penjualan/detail/{id}/delete/{id_detail}', ['uses' => 'Admin\DataTransaksiController@deleteTransaksiDetail']);
    Route::get('/data-penjualan/cetak/{id}', ['uses' => 'Admin\DataTransaksiController@cetakInvoice']);
    Route::get('/data-penjualan/export', ['as' => 'data-transaksi-export','uses' => 'Admin\DataTransaksiController@export']);
    // END ROUTE TRANSAKSI //

    // ROUTE PEMBELIAN //
    Route::get('/data-pembelian', ['uses' => 'Admin\PembelianController@index']);
    Route::get('/data-pembelian/tambah', ['uses' => 'Admin\PembelianController@tambah']);
    Route::get('/data-pembelian/edit/{id}', ['uses' => 'Admin\PembelianController@edit']);
    Route::get('/data-pembelian/delete/{id}', ['uses' => 'Admin\PembelianController@delete']);
    Route::post('/data-pembelian/save', ['uses' => 'Admin\PembelianController@save']);
    Route::get('/data-pembelian/cetak/{id}', ['uses' => 'Admin\PembelianController@cetak']);
    // END ROUTE PEMBELIAN //

    // ROUTE PEMBELIAN //
    Route::get('/data-pembelian/detail/{id}', ['uses' => 'Admin\PembelianController@detail']);
    Route::get('/data-pembelian/detail/{id}/delete/{id_detail}', ['uses' => 'Admin\PembelianController@deleteDetail']);
    // END ROUTE PEMBELIAN //

    // ROUTE HISTORY BELI //
    Route::get('/history-beli', ['uses' => 'Admin\PembelianController@historyBeli']);
    // END ROUTE HISTORY BELI //

    // ROUTE KARTU STOK //
    Route::get('/kartu-stok', ['uses' => 'Admin\PembelianController@kartuStok']);
    Route::get('/kartu-stok/cetak', ['uses' => 'Admin\PembelianController@kartuStokCetak']);
    // END ROUTE KARTU STOK //

    // ROUTE RETUR BARANG //
    Route::get('/retur-barang', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@index']);
    Route::get('/retur-barang/tambah', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@tambah']);
    Route::get('/retur-barang/edit/{id}', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@edit']);
    Route::get('/retur-barang/delete/{id}', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@delete']);
    Route::post('/retur-barang/save', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@save']);
    Route::get('/retur-barang/detail/{id}', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@detail']);
    Route::get('/retur-barang/detail/{id}/delete/{id_detail}', ['as' => 'retur-barang','uses' => 'Admin\ReturBarangController@deleteDetail']);
    // END ROUTE RETUR BARANG //

    // ROUTE TRANSAKSI RACIK OBAT //
    Route::get('/data-penjualan-racik-obat', ['uses' => 'Admin\RacikObatController@dataTransaksiRacik']);
    Route::get('/data-penjualan-racik-obat/delete/{id}', ['uses' => 'Admin\RacikObatController@deleteTransaksiRacikObat']);
    Route::get('/data-penjualan-racik-obat/cetak/{id}', ['uses' => 'Admin\RacikObatController@cetakInvoice']);

    Route::get('/data-racik-obat/{id}', ['uses' => 'Admin\RacikObatController@dataRacikObat']);
    Route::get('/data-racik-obat/{id}/detail/{id_detail}', ['uses' => 'Admin\RacikObatController@dataRacikObatDetail']);
    // END ROUTE TRANSAKSI RACIK OBAT //

    // ROUTE LAPORAN DATA //
    Route::get('/laporan-data', ['uses' => 'Admin\LaporanController@index']);
    Route::get('/laporan-data/pembelian', ['uses' => 'Admin\LaporanController@laporanPembelian']);
    Route::get('/laporan-penjualan', ['uses' => 'Admin\LaporanController@laporanPenjualan']);
    // END ROUTE LAPORAN DATA //

    // ROUTE STOK OPNEM //
    Route::get('/stok-opnem', ['uses' => 'Admin\StokOpnemController@index']);
    Route::get('/stok-opnem/tambah', ['uses' => 'Admin\StokOpnemController@tambah']);
    Route::get('/stok-opnem/cetak/{id}', ['uses' => 'Admin\StokOpnemController@cetak']);
    Route::get('/stok-opnem/delete/{id}', ['uses' => 'Admin\StokOpnemController@delete']);
    Route::get('/stok-opnem/lanjut-input/{id}', ['uses' => 'Admin\StokOpnemController@lanjutInput']);
    Route::post('/stok-opnem/input-sebagian', ['uses' => 'Admin\StokOpnemController@inputSebagian']);
    Route::post('/stok-opnem/save', ['uses' => 'Admin\StokOpnemController@save']);
    Route::get('/stok-opnem/selesai-input', ['uses' => 'Admin\StokOpnemController@selesaiInput']);
    Route::get('/stok-opnem/detail/{id}', ['uses' => 'Admin\StokOpnemController@detail']);
    Route::get('/stok-opnem/export/{id}', ['uses' => 'Admin\StokOpnemController@export']);
    // END ROUTE STOK OPNEM //

    // ROUTE TRANSAKSI //
    Route::get('/penjualan', ['uses' => 'Admin\KasirController@index']);
    Route::post('/penjualan/save', ['uses' => 'Admin\KasirController@bayar']);
    // END ROUTE TRANSAKSI //

    // ROUTE TRANSAKSI //
    Route::get('/racik-obat', ['uses' => 'Admin\RacikObatController@racikObat']);
    Route::post('/racik-obat/bayar', ['uses' => 'Admin\RacikObatController@bayarRacikObat']);
    // END ROUTE TRANSAKSI //

    // ROUTE TRANSAKSI //
    Route::get('/penjualan-relasi', ['uses' => 'Admin\TransaksiRelasiController@transaksi']);
    // END ROUTE TRANSAKSI //

    // ROUTE UBAH PROFILE //
    Route::get('/ubah-profile', ['uses' => 'Admin\PanelController@ubahProfile']);
    Route::post('/ubah-profile/save', ['uses' => 'Admin\PanelController@saveProfile']);
    // END ROUTE UBAH PROFILE //
});

Route::group(['middleware' => 'isKasir','prefix' => 'kasir'], function () {
    Route::get('/panel', ['uses' => 'Kasir\PanelController@index']);

    Route::group(['middleware' => 'urlKasir'], function () {
        // CRUD SUPPLIER OBAT //
        Route::get('/data-supplier-obat', ['as' => 'data-supplier-obat','uses' => 'Kasir\SupplierObatController@index']);
        Route::get('/data-supplier-obat/tambah', ['as' => 'data-supplier-obat','uses' => 'Kasir\SupplierObatController@tambah']);
        Route::get('/data-supplier-obat/edit/{id}', ['as' => 'data-supplier-obat','uses' => 'Kasir\SupplierObatController@edit']);
        Route::post('/data-supplier-obat/save', ['as' => 'data-supplier-obat','uses' => 'Kasir\SupplierObatController@save']);
        // END CRUD SUPPLIER OBAT //

        // CRUD SUPPLIER OBAT //
        Route::get('/data-pabrik-obat', ['as' => 'data-pabrik-obat','uses' => 'Kasir\PabrikObatController@index']);
        Route::get('/data-pabrik-obat/tambah', ['as' => 'data-pabrik-obat','uses' => 'Kasir\PabrikObatController@tambah']);
        Route::get('/data-pabrik-obat/edit/{id}', ['as' => 'data-pabrik-obat','uses' => 'Kasir\PabrikObatController@edit']);
        Route::post('/data-pabrik-obat/save', ['as' => 'data-pabrik-obat','uses' => 'Kasir\PabrikObatController@save']);
        // END CRUD SUPPLIER OBAT //

        // CRUD MARGIN OBAT //
        Route::get('/margin-obat', ['as' => 'margin-obat','uses' => 'Kasir\MarginObatController@index']);
        Route::get('/margin-obat/tambah', ['as' => 'margin-obat','uses' => 'Kasir\MarginObatController@tambah']);
        Route::get('/margin-obat/edit/{id}', ['as' => 'margin-obat','uses' => 'Kasir\MarginObatController@edit']);
        Route::post('/margin-obat/save', ['as' => 'margin-obat','uses' => 'Kasir\MarginObatController@save']);
        // END CRUD MARGIN OBAT //

        // DATA OBAT //
        Route::get('/data-obat', ['uses' => 'Kasir\ObatController@index']);
        Route::get('/data-obat/tambah', ['uses' => 'Kasir\ObatController@tambah']);
        Route::post('/data-obat/save', ['uses' => 'Kasir\ObatController@save']);
        Route::get('/data-obat/edit/{id}', ['uses' => 'Kasir\ObatController@edit']);
        Route::get('/data-obat/rekap-obat', ['uses' => 'Kasir\ObatController@rekapObat']);
        // END DATA OBAT //

        // CRUD GOLONGAN OBAT //
        Route::get('/data-golongan-obat', ['as' => 'data-golongan-obat','uses' => 'Kasir\GolonganObatController@index']);
        Route::get('/data-golongan-obat/tambah', ['as' => 'data-golongan-obat','uses' => 'Kasir\GolonganObatController@tambah']);
        Route::get('/data-golongan-obat/edit/{id}', ['as' => 'data-golongan-obat','uses' => 'Kasir\GolonganObatController@edit']);
        Route::post('/data-golongan-obat/save', ['as' => 'data-golongan-obat','uses' => 'Kasir\GolonganObatController@save']);
        // END CRUD GOLONGAN OBAT //

        // DATA OBAT SUPPLIER //
        Route::get('/data-obat/lihat-supplier/{id}', ['uses' => 'Kasir\ObatController@obatDetail']);
        Route::get('/data-obat/lihat-supplier/{id}/delete/{id_detail}', ['uses' => 'Kasir\ObatController@deleteObatDetail']);
        // END DATA OBAT SUPPLIER //

        // DATA KOMPOSISI OBAT //
        Route::get('/data-obat/komposisi-obat/{id}', ['uses' => 'Kasir\ObatController@komposisiObat']);
        // END DATA KOMPOSISI OBAT //

        // DATA OBAT //
        Route::get('/data-jenis-obat', ['uses' => 'Kasir\JenisObatController@index']);
        Route::get('/data-jenis-obat/tambah', ['uses' => 'Kasir\JenisObatController@tambah']);
        Route::post('/data-jenis-obat/save', ['uses' => 'Kasir\JenisObatController@save']);
        Route::get('/data-jenis-obat/edit/{id}', ['uses' => 'Kasir\JenisObatController@edit']);
        // END DATA OBAT //

        // DATA PPN //
        Route::get('/data-ppn', ['uses' => 'Kasir\PersenPpnController@index']);
        Route::get('/data-ppn/edit/{id}', ['uses' => 'Kasir\PersenPpnController@edit']);
        Route::post('/data-ppn/save', ['uses' => 'Kasir\PersenPpnController@save']);
        // END DATA PPN //

        // DATA PASIEN //
        Route::get('/data-pasien', ['uses' => 'Kasir\PasienController@index']);
        Route::post('/data-pasien/save', ['uses' => 'Kasir\PasienController@save']);
        Route::get('/data-pasien/edit/{id}', ['uses' => 'Kasir\PasienController@edit']);
        // END DATA PASIEN //




        // DATA DOKTER //
        Route::get('/data-dokter', ['uses' => 'Kasir\DokterController@index']);
        Route::post('/data-dokter/save', ['uses' => 'Kasir\DokterController@save']);
        Route::get('/data-dokter/edit/{id}', ['uses' => 'Kasir\DokterController@edit']);
        // END DATA DOKTER //

        // ROUTE KREDIT //
        Route::get('/data-kredit', ['uses' => 'Kasir\DataKreditController@index']);
        Route::get('/data-kredit/detail/{id}', ['uses' => 'Kasir\DataKreditController@kreditFaktur']);
        Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}', ['uses' => 'Kasir\DataKreditController@kreditDetail']);
        Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}/lunas-semua', ['uses' => 'Kasir\DataKreditController@lunasSemua']);
        Route::get('/data-kredit/detail/{id}/lihat-hutang/{id_faktur}/lunas-hutang/{id_detail}', ['uses' => 'Kasir\DataKreditController@lunasHutang']);
        Route::get('/data-kredit/detail/{id}/cetak/{id_faktur}', ['uses' => 'Kasir\DataKreditController@cetakInvoice']);
        Route::get('/data-kredit/detail/{id}/cetak-kredit-range/{id_faktur}', ['uses' => 'Kasir\DataKreditController@cetakKreditRange']);
        // END ROUTE KREDIT //

        // DATA OBAT //
        Route::get('/jam-shift', ['uses' => 'Kasir\JamShiftController@index']);
        Route::get('/jam-shift/tambah', ['uses' => 'Kasir\JamShiftController@tambah']);
        Route::post('/jam-shift/save', ['uses' => 'Kasir\JamShiftController@save']);
        Route::get('/jam-shift/edit/{id}', ['uses' => 'Kasir\JamShiftController@edit']);
        // END DATA OBAT //

        // ROUTE TRANSAKSI //
        Route::get('/data-penjualan', ['uses' => 'Kasir\DataTransaksiController@index']);
        Route::get('/data-penjualan/detail/{id}', ['uses' => 'Kasir\DataTransaksiController@transaksiDetail']);
        Route::get('/data-penjualan/cetak/{id}', ['uses' => 'Kasir\DataTransaksiController@cetakInvoice']);
        Route::get('/data-penjualan/export', ['as' => 'data-transaksi-export','uses' => 'Kasir\DataTransaksiController@export']);
        // END ROUTE TRANSAKSI //

        // ROUTE PEMBELIAN //
        Route::get('/data-pembelian', ['uses' => 'Kasir\PembelianController@index']);
        Route::get('/data-pembelian/tambah', ['uses' => 'Kasir\PembelianController@tambah']);
        Route::get('/data-pembelian/edit/{id}', ['uses' => 'Kasir\PembelianController@edit']);
        Route::post('/data-pembelian/save', ['uses' => 'Kasir\PembelianController@save']);
        Route::get('/data-pembelian/cetak/{id}', ['uses' => 'Kasir\PembelianController@cetak']);
        // END ROUTE PEMBELIAN //

        // ROUTE PEMBELIAN //
        Route::get('/data-pembelian/detail/{id}', ['uses' => 'Kasir\PembelianController@detail']);
        // END ROUTE PEMBELIAN //

        // ROUTE HISTORY BELI //
        Route::get('/history-beli', ['uses' => 'Kasir\PembelianController@historyBeli']);
        // END ROUTE HISTORY BELI //

        // ROUTE KARTU STOK //
        Route::get('/kartu-stok', ['uses' => 'Kasir\PembelianController@kartuStok']);
        Route::get('/kartu-stok/cetak', ['uses' => 'Kasir\PembelianController@kartuStokCetak']);
        // END ROUTE KARTU STOK //

        // ROUTE RETUR BARANG //
        Route::get('/retur-barang', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@index']);
        Route::get('/retur-barang/tambah', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@tambah']);
        Route::get('/retur-barang/edit/{id}', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@edit']);
        Route::post('/retur-barang/save', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@save']);
        Route::get('/retur-barang/detail/{id}', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@detail']);
        Route::get('/retur-barang/detail/{id}/delete/{id_detail}', ['as' => 'retur-barang','uses' => 'Kasir\ReturBarangController@deleteDetail']);
        // END ROUTE RETUR BARANG //

        // ROUTE TRANSAKSI RACIK OBAT //
        Route::get('/data-penjualan-racik-obat', ['uses' => 'Kasir\RacikObatController@dataTransaksiRacik']);
        Route::get('/data-penjualan-racik-obat/cetak/{id}', ['uses' => 'Kasir\RacikObatController@cetakInvoice']);

        Route::get('/data-racik-obat/{id}', ['uses' => 'Kasir\RacikObatController@dataRacikObat']);
        Route::get('/data-racik-obat/{id}/detail/{id_detail}', ['uses' => 'Kasir\RacikObatController@dataRacikObatDetail']);
        // END ROUTE TRANSAKSI RACIK OBAT //

        // ROUTE LAPORAN DATA //
        Route::get('/laporan-data', ['uses' => 'Kasir\LaporanController@index']);
        Route::get('/laporan-data/pembelian', ['uses' => 'Kasir\LaporanController@laporanPembelian']);
        Route::get('/laporan-penjualan', ['uses' => 'Kasir\LaporanController@laporanPenjualan']);
        // END ROUTE LAPORAN DATA //

        // ROUTE STOK OPNEM //
        Route::get('/stok-opnem', ['uses' => 'Kasir\StokOpnemController@index']);
        Route::get('/stok-opnem/tambah', ['uses' => 'Kasir\StokOpnemController@tambah']);
        Route::get('/stok-opnem/cetak/{id}', ['uses' => 'Kasir\StokOpnemController@cetak']);
        Route::get('/stok-opnem/lanjut-input/{id}', ['uses' => 'Kasir\StokOpnemController@lanjutInput']);
        Route::post('/stok-opnem/input-sebagian', ['uses' => 'Kasir\StokOpnemController@inputSebagian']);
        Route::post('/stok-opnem/save', ['uses' => 'Kasir\StokOpnemController@save']);
        Route::get('/stok-opnem/selesai-input', ['uses' => 'Kasir\StokOpnemController@selesaiInput']);
        Route::get('/stok-opnem/detail/{id}', ['uses' => 'Kasir\StokOpnemController@detail']);
        Route::get('/stok-opnem/export/{id}', ['uses' => 'Kasir\StokOpnemController@export']);
        // END ROUTE STOK OPNEM //

        // ROUTE TRANSAKSI //
        Route::get('/penjualan', ['uses' => 'Kasir\KasirController@index']);
        Route::post('/penjualan/save', ['uses' => 'Kasir\KasirController@bayar']);
        // END ROUTE TRANSAKSI //

        // ROUTE TRANSAKSI //
        Route::get('/racik-obat', ['uses' => 'Kasir\RacikObatController@racikObat']);
        Route::post('/racik-obat/bayar', ['uses' => 'Kasir\RacikObatController@bayarRacikObat']);
        // END ROUTE TRANSAKSI //

        // ROUTE TRANSAKSI //
        Route::get('/penjualan-relasi', ['uses' => 'Kasir\TransaksiRelasiController@transaksi']);
        // END ROUTE TRANSAKSI //
    });
    // ROUTE UBAH PROFILE //
    Route::get('/ubah-profile', ['uses' => 'Kasir\PanelController@ubahProfile']);
    Route::post('/ubah-profile/save', ['uses' => 'Kasir\PanelController@saveProfile']);
    // END ROUTE UBAH PROFILE //
});



Route::get('/test', function () {
    foreach (Dokter::all() as $key => $value) {
        if (Dokter::where('nama_dokter', $value->nama_dokter)->where('status_delete', 0)->count() > 1) {
            Dokter::where('nama_dokter', $value->nama_dokter)->update(['status_delete' => 1]);
            $id = Dokter::where('nama_dokter', $value->nama_dokter)->firstOrFail()->id_dokter;
            Dokter::where('id_dokter', $id)->update(['status_delete' => 0]);
        }
    }

    foreach (Pasien::all() as $index => $val) {
        if (Pasien::where('nama_pasien', $val->nama_pasien)->where('status_delete', 0)->count() > 1) {
            Pasien::where('nama_pasien', $val->nama_pasien)->update(['status_delete' => 1]);
            $id = Pasien::where('nama_pasien', $val->nama_pasien)->firstOrFail()->id_pasien;
            Pasien::where('id_pasien', $id)->update(['status_delete' => 0]);
        }
    }
});
