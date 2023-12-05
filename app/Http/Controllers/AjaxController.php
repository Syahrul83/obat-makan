<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Models\Debitur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\ObatModel as Obat;
use App\Models\DokterModel as Dokter;
use App\Models\KreditModel as Kredit;
use App\Models\PasienModel as Pasien;
use App\Models\JamShiftModel as JamShift;
use App\Models\SupplierModel as Supplier;
use App\Models\HargaObatModel as HargaObat;
use App\Models\JenisObatModel as JenisObat;
use App\Models\KartuStokModel as KartuStok;
use App\Models\PersenPpnModel as PersenPpn;
use App\Models\RacikObatModel as RacikObat;
use App\Models\StokOpnemModel as StokOpnem;
use App\Models\MarginObatModel as MarginObat;
use App\Models\ObatDetailModel as ObatDetail;
use App\Models\PabrikObatModel as PabrikObat;
use App\Models\ReturBarangModel as ReturBarang;
use App\Models\GolonganObatModel as GolonganObat;
use App\Models\KreditDetailModel as KreditDetail;
use App\Models\KreditFakturModel as KreditFaktur;
use App\Models\KomposisiObatModel as KomposisiObat;
use App\Models\PembelianObatModel as PembelianObat;
use App\Models\RacikObatDataModel as RacikObatData;
use App\Models\TransaksiKasirModel as TransaksiKasir;
use App\Models\PembelianDetailModel as PembelianDetail;
use App\Models\RacikObatDetailModel as RacikObatDetail;
use App\Models\StokOpnemDetailModel as StokOpnemDetail;
use App\Models\ReturBarangDetailModel as ReturBarangDetail;
use App\Models\RacikObatSementaraModel as RacikObatSementara;
use App\Models\TransaksiRacikObatModel as TransaksiRacikObat;
use App\Models\TransaksiKasirDetailModel as TransaksiKasirDetail;
use App\Models\RacikObatSementaraDetailModel as RacikObatSementaraDetail;

class AjaxController extends Controller
{
    private $level;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->level =
                Auth::user()->level_user == 2
                    ? 'admin'
                    : (Auth::user()->level_user == 1
                        ? 'kasir'
                        : '');
            return $next($request);
        });
    }

    public function dataSupplierObat()
    {
        $supplier_obat = Supplier::where('status_delete', 0)->get();
        $datatables = Datatables::of($supplier_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-supplier-obat/edit/$action->id_supplier"
                    ) .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-supplier-obat/delete/$action->id_supplier"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataPabrikObat()
    {
        $pabrik_obat = PabrikObat::where('status_delete', 0)->get();
        $datatables = Datatables::of($pabrik_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-pabrik-obat/edit/$action->id_pabrik_obat"
                    ) .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-pabrik-obat/delete/$action->id_pabrik_obat"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataMarginObat()
    {
        $margin_obat = MarginObat::all();
        $datatables = Datatables::of($margin_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/margin-obat/edit/$action->id_margin_obat"
                    ) .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                return $column;
            })
            ->editColumn('margin_upds', function ($edit) {
                return $edit->margin_upds . '%';
            })
            ->editColumn('margin_resep', function ($edit) {
                return $edit->margin_resep . '%';
            })
            ->editColumn('margin_relasi', function ($edit) {
                return $edit->margin_relasi . '%';
            })
            ->make(true);
        return $datatables;
    }

    public function dataObat(Request $request)
    {
        if ($request->cari_komposisi_obat != '') {
            $obat = Obat::join(
                'jenis_obat',
                'obat.id_jenis_obat',
                '=',
                'jenis_obat.id_jenis_obat'
            )
                ->join(
                    'golongan_obat',
                    'obat.id_golongan_obat',
                    '=',
                    'golongan_obat.id_golongan_obat'
                )
                ->join(
                    'pabrik_obat',
                    'obat.id_pabrik_obat',
                    '=',
                    'pabrik_obat.id_pabrik_obat'
                )
                ->join(
                    'komposisi_obat',
                    'obat.id_obat',
                    '=',
                    'komposisi_obat.id_obat'
                )
                ->where(
                    'nama_komposisi',
                    'like',
                    '%' . $request->cari_komposisi_obat . '%'
                )
                ->where('obat.status_delete', 0)
                ->groupBy('nama_obat')
                ->get();
        } else {
            $obat = Obat::join(
                'jenis_obat',
                'obat.id_jenis_obat',
                '=',
                'jenis_obat.id_jenis_obat'
            )
                ->join(
                    'golongan_obat',
                    'obat.id_golongan_obat',
                    '=',
                    'golongan_obat.id_golongan_obat'
                )
                ->join(
                    'pabrik_obat',
                    'obat.id_pabrik_obat',
                    '=',
                    'pabrik_obat.id_pabrik_obat'
                )
                ->where('obat.status_delete', 0)
                ->get();
        }
        $datatables = Datatables::of($obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-obat/lihat-supplier/$action->id_obat"
                    ) .
                    '">
    					  <button class="btn btn-info"> Lihat Supplier </button>
					   </a>
					   <a href="' .
                    url(
                        "/$this->level/data-obat/komposisi-obat/$action->id_obat"
                    ) .
                    '">
    					  <button class="btn btn-info"> Lihat Komposisi </button>
					   </a>
					   <a href="' .
                    url("/$this->level/data-obat/edit/$action->id_obat") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url("/$this->level/data-obat/delete/$action->id_obat") .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->editColumn('tanggal_expired', function ($edit) {
                if ($edit->tanggal_expired == '0000-00-00') {
                    $tanggal_expired = '-';
                } else {
                    $tanggal_expired = human_date($edit->tanggal_expired);
                }
                return $tanggal_expired;
            })
            ->editColumn('harga_modal', function ($edit) {
                return format_rupiah($edit->harga_modal);
            })
            ->editColumn('harga_modal_ppn', function ($edit) {
                return format_rupiah($edit->harga_modal_ppn);
            })
            ->editColumn('hja_upds', function ($edit) {
                return format_rupiah($edit->hja_upds);
            })
            ->editColumn('hja_resep', function ($edit) {
                return format_rupiah($edit->hja_resep);
            })
            ->editColumn('hja_relasi', function ($edit) {
                return format_rupiah($edit->hja_relasi);
            })
            ->make(true);
        return $datatables;
    }

    public function dataObatDetail($id)
    {
        $obat = ObatDetail::join(
            'supplier_obat',
            'obat_detail.id_supplier',
            '=',
            'supplier_obat.id_supplier'
        )
            ->where('id_obat', $id)
            ->get();
        $datatables = Datatables::of($obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-obat/lihat-supplier/$action->id_obat/delete/$action->id_obat_detail"
                    ) .
                    '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataKomposisiObat($id)
    {
        $komposisi_obat = KomposisiObat::join(
            'obat',
            'komposisi_obat.id_obat',
            '=',
            'obat.id_obat'
        )
            ->where('komposisi_obat.id_obat', $id)
            ->get();
        $datatables = Datatables::of($komposisi_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-obat/komposisi-obat/$action->id_obat/delete/$action->id_komposisi_obat"
                    ) .
                    '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataPasien()
    {
        $pasien = Pasien::where('status_delete', 0)->get();
        $datatables = Datatables::of($pasien)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/data-pasien/edit/$action->id_pasien") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-pasien/delete/$action->id_pasien"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }


    public function dataDebitur()
    {
        $debitur = Debitur::where('status_delete', 0)->get();
        $datatables = Datatables::of($debitur)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/data-debitur/edit/$action->id") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-debitur/delete/$action->id"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->editColumn('margin', function ($edit) {
                return $edit->margin . '%';
            })
            ->make(true);
        return $datatables;
    }

    public function dataDokter()
    {
        $dokter = Dokter::where('status_delete', 0)->get();
        $datatables = Datatables::of($dokter)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/data-dokter/edit/$action->id_dokter") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-dokter/delete/$action->id_dokter"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataKredit()
    {
        $kredit = Kredit::all();
        $datatables = Datatables::of($kredit)
            ->addColumn('action', function ($action) {
                return '<button class="btn btn-success kredit-button" data-id-kredit="' .
                    $action->id_kredit .
                    '"> Detail </button>';
            })
            ->addColumn('status_kredit', function ($add) {
                $checked = KreditDetail::join(
                    'kredit_faktur',
                    'kredit_det.id_kredit_faktur',
                    '=',
                    'kredit_faktur.id_kredit_faktur'
                )
                    ->where('kredit_faktur.id_kredit', $add->id_kredit)
                    ->where('status_kredit', 0)
                    ->count();

                if ($checked != 0) {
                    return '<span class="label label-danger"> Belum Lunas </span>';
                } else {
                    return '<span class="label label-success"> Tidak Ada Hutang </span>';
                }
            })
            ->rawColumns(['action', 'status_kredit'])
            ->make(true);
        return $datatables;
    }

    public function dataJenisObat()
    {
        $jenis_obat = JenisObat::where('status_delete', 0)->get();
        $datatables = Datatables::of($jenis_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-jenis-obat/edit/$action->id_jenis_obat"
                    ) .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-jenis-obat/delete/$action->id_jenis_obat"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataGolonganObat()
    {
        $golongan_obat = GolonganObat::where('status_delete', 0)->get();
        $datatables = Datatables::of($golongan_obat)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-golongan-obat/edit/$action->id_golongan_obat"
                    ) .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/data-golongan-obat/delete/$action->id_golongan_obat"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataPembelianObat(Request $request)
    {
        if ($this->level == 'admin') {
            $pembelian = PembelianObat::getData('', $request);
        } else {
            $pembelian = PembelianObat::getData(auth()->id(), $request);
        }
        $datatables = Datatables::of($pembelian)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-pembelian/cetak/$action->id_pembelian_obat"
                    ) .
                    '">
    					  		<button class="btn btn-success"> Cetak </button>
					       </a>
					       <a href="' .
                    url(
                        "/$this->level/data-pembelian/detail/$action->id_pembelian_obat"
                    ) .
                    '">
    					  		<button class="btn btn-info"> Detail </button>
					       </a>';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   	   <a href="' .
                        url(
                            "/admin/data-pembelian/delete/$action->id_pembelian_obat"
                        ) .
                        '">
						   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
						   </a>';
                }
                return $column;
            })
            ->editColumn('jenis_beli', function ($edit) {
                return ucwords($edit->jenis_beli);
            })
            ->editColumn('tanggal_terima', function ($edit) {
                return human_date($edit->tanggal_terima);
            })
            ->editColumn('tanggal_jatuh_tempo', function ($edit) {
                if ($edit->tanggal_jatuh_tempo != null) {
                    $tanggal_jatuh_tempo = human_date(
                        $edit->tanggal_jatuh_tempo
                    );
                } else {
                    $tanggal_jatuh_tempo = '-';
                }
                return $tanggal_jatuh_tempo;
            })
            ->editColumn('tanggal_input', function ($edit) {
                if ($edit->tanggal_input == '0000-00-00') {
                    $date = human_date($edit->tanggal_terima);
                } else {
                    $date = human_date($edit->tanggal_input);
                }
                return $date;
            })
            ->editColumn('total_semua', function ($edit) {
                return format_rupiah($edit->total_semua);
            })
            ->editColumn('waktu_hutang', function ($edit) {
                return $edit->waktu_hutang . ' Hari';
            })
            ->make(true);
        return $datatables;
    }

    public function dataPembelianDetail($id)
    {
        $pembelian_detail = PembelianDetail::getData($id);
        $datatables = Datatables::of($pembelian_detail)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/admin/data-pembelian/detail/$action->id_pembelian_obat/delete/$action->id_pembelian_detail"
                    ) .
                    '">
						   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
						   </a>';
                return $column;
            })
            ->editColumn('harga_obat', function ($edit) {
                return format_rupiah($edit->harga_obat);
            })
            ->editColumn('sub_total', function ($edit) {
                return format_rupiah($edit->sub_total);
            })
            ->editColumn('disc_1', function ($edit) {
                return $edit->disc_1 . '%';
            })
            ->editColumn('disc_2', function ($edit) {
                return $edit->disc_2 . '%';
            })
            ->editColumn('disc_3', function ($edit) {
                return $edit->disc_3 . '%';
            })
            ->make(true);
        return $datatables;
    }

    public function historyBeli(Request $request)
    {
        // if ($request->tanggal_dari != '' && $request->tanggal_sampai != '' && $request->id_obat != '') {
        $history_beli = PembelianDetail::join(
            'pembelian_obat',
            'pembelian_detail.id_pembelian_obat',
            '=',
            'pembelian_obat.id_pembelian_obat'
        )
            ->join(
                'supplier_obat',
                'pembelian_obat.id_supplier',
                '=',
                'supplier_obat.id_supplier'
            )
            ->join('obat', 'pembelian_detail.id_obat', '=', 'obat.id_obat')
            ->join(
                'jenis_obat',
                'obat.id_jenis_obat',
                '=',
                'jenis_obat.id_jenis_obat'
            )
            ->whereBetween('tanggal_terima', [
                reverse_date($request->tanggal_dari),
                reverse_date($request->tanggal_sampai),
            ])
            ->where('pembelian_detail.id_obat', $request->obat_cari)
            ->get();
        $datatables = Datatables::of($history_beli)
            ->editColumn('tanggal_terima', function ($edit) {
                return human_date($edit->tanggal_terima);
            })
            ->editColumn('sub_total', function ($edit) {
                return format_rupiah($edit->sub_total);
            })
            ->make(true);
        return $datatables;
        // }
    }

    public function kartuStok(Request $request)
    {
        // if ($request->tanggal_dari != '' && $request->tanggal_sampai != '' && $request->id_obat != '') {
        $kartu_stok = KartuStok::whereBetween('tanggal_pakai', [
            reverse_date($request->tanggal_dari),
            reverse_date($request->tanggal_sampai),
        ])
            ->where('id_obat', $request->obat_cari)
            ->get();
        $datatables = Datatables::of($kartu_stok)
            ->editColumn('tanggal_pakai', function ($edit) {
                return human_date($edit->tanggal_pakai);
            })
            ->make(true);
        return $datatables;
        // }
    }

    public function dataKreditPanel()
    {
        $kredit = Kredit::all();
        $datatables = Datatables::of($kredit)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/data-kredit/detail/$action->id_kredit") .
                    '">
    					  <button class="btn btn-info"> Detail </button>
					   </a>';
                // if ($this->level == 'admin') {
                // 	$column .= '
                //   <a href="'.url("/admin/data-kredit/delete/$action->id_kredit").'">
                //   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                //   </a>';
                // }
                return $column;
            })
            ->addColumn('status_kredit', function ($add) {
                $checked = KreditDetail::join(
                    'kredit_faktur',
                    'kredit_det.id_kredit_faktur',
                    '=',
                    'kredit_faktur.id_kredit_faktur'
                )
                    ->where('kredit_faktur.id_kredit', $add->id_kredit)
                    ->where('status_kredit', 0)
                    ->count();
                if ($checked != 0) {
                    return '<span class="label label-danger"> Belum Lunas </span>';
                } else {
                    return '<span class="label label-success"> Tidak Ada Hutang </span>';
                }
            })
            ->rawColumns(['action', 'status_kredit'])
            ->make(true);
        return $datatables;
    }

    public function dataKreditFakturPanel($id)
    {
        if ($this->level == 'admin') {
            $kredit_faktur = KreditFaktur::join(
                'users',
                'kredit_faktur.id_users',
                '=',
                'users.id_users'
            )
                ->where('id_kredit', $id)
                ->get();
        } else {
            $kredit_faktur = KreditFaktur::join(
                'users',
                'kredit_faktur.id_users',
                '=',
                'users.id_users'
            )
                ->where('id_kredit', $id)
                ->where('kredit_faktur.id_users', auth()->id())
                ->get();
        }

        $datatables = Datatables::of($kredit_faktur)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-kredit/detail/$action->id_kredit/cetak/$action->id_kredit_faktur"
                    ) .
                    '">
    					  <button class="btn btn-success"> Cetak </button>
					   </a>
					   <a href="' .
                    url(
                        "/$this->level/data-kredit/detail/$action->id_kredit/lihat-hutang/$action->id_kredit_faktur"
                    ) .
                    '">
    					  <button class="btn btn-info"> Lihat Hutang </button>
					   </a>';
                // if ($this->level == 'admin') {
                // 	$column .= '
                //   <a href="'.url("/admin/data-kredit/detail/$action->id_kredit/delete/$action->id_kredit_faktur").'">
                //   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                //   </a>';
                // }
                return $column;
            })
            ->addColumn('status_kredit', function ($add) {
                $checked = KreditDetail::where(
                    'id_kredit_faktur',
                    $add->id_kredit_faktur
                )
                    ->where('status_kredit', 0)
                    ->count();
                if ($checked != 0) {
                    return '<span class="label label-danger"> Belum Lunas </span>';
                } else {
                    return '<span class="label label-success"> Tidak Ada Hutang </span>';
                }
            })
            ->editColumn('tanggal_faktur', function ($edit) {
                return human_date($edit->tanggal_faktur);
            })
            ->rawColumns(['action', 'status_kredit'])
            ->make(true);
        return $datatables;
    }

    public function dataKreditDetailPanel($id)
    {
        $kredit_detail = KreditDetail::getKredit($id);
        $datatables = Datatables::of($kredit_detail)
            ->addColumn('action', function ($action) {
                $column =
                    '
    					<a href="' .
                    url(
                        "/$this->level/data-kredit/detail/$action->id_kredit/lihat-hutang/$action->id_kredit_faktur/lunas-hutang/$action->id_kredit_det"
                    ) .
                    '">
    					  <button class="btn btn-success"> Lunaskan Hutang </button>
					   </a>
    				';
                // if ($this->level == 'admin') {
                // 	$column .= '
                // 			<a href="'.url("/admin/data-kredit/detail/$action->id_kredit/lihat-hutang/$action->id_kredit_faktur/delete/$action->id_kredit_det").'">
                // 			  <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                //   </a>';
                // }
                return $column;
            })
            ->editColumn('banyak_obat', function ($edit) {
                return $edit->banyak_obat . ' ' . ucwords($edit->bentuk_satuan);
            })
            ->editColumn('tanggal_jatuh_tempo', function ($edit) {
                return human_date($edit->tanggal_jatuh_tempo);
            })
            ->editColumn('sub_total', function ($edit) {
                return format_rupiah($edit->sub_total);
            })
            ->addColumn('status_kredit', function ($add) {
                if ($add->status_kredit == 0) {
                    return '<span class="label label-danger"> Belum Lunas </span>';
                } else {
                    return '<span class="label label-success"> Lunas </span>';
                }
            })
            ->editColumn('diskon', function ($edit) {
                $diskon = '';
                if ($edit->diskon != 0) {
                    if (
                        $edit->jenis_diskon == 'persen' ||
                        $edit->jenis_diskon == ''
                    ) {
                        $diskon = $edit->diskon . '%';
                    } elseif ($edit->jenis_diskon == 'rupiah') {
                        $diskon = format_rupiah($edit->diskon);
                    }
                } else {
                    $diskon = '-';
                }

                return $diskon;
            })
            ->editColumn('diskon_rupiah', function ($edit) {
                $diskon = '';
                if ($edit->diskon != 0) {
                    if (
                        $edit->jenis_diskon == 'persen' ||
                        $edit->jenis_diskon == ''
                    ) {
                        $calc = get_discount(
                            $edit->hja_upds * $edit->banyak_obat,
                            $edit->diskon
                        );
                        $diskon = format_rupiah($calc);
                    } elseif ($edit->jenis_diskon == 'rupiah') {
                        $diskon = format_rupiah($edit->diskon);
                    }
                } else {
                    $diskon = '-';
                }

                return $diskon;
            })
            ->rawColumns(['action', 'status_kredit'])
            ->make(true);
        return $datatables;
    }

    public function dataJamShift()
    {
        $jam_shift = JamShift::where('status_delete', 0)->get();
        $datatables = Datatables::of($jam_shift)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/jam-shift/edit/$action->id_jam_shift") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
					   <a href="' .
                    url(
                        "/$this->level/jam-shift/delete/$action->id_jam_shift"
                    ) .
                    '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->make(true);
        return $datatables;
    }

    public function dataTransaksi(Request $request)
    {
        if ($this->level == 'admin') {
            $transaksi = TransaksiKasir::join(
                'users',
                'transaksi_kasir.id_users',
                '=',
                'users.id_users'
            )
                ->where(
                    'tanggal_transaksi',
                    'like',
                    '%' . reverse_date($request->tanggal_transaksi_cari) . '%'
                )
                ->where(
                    'kode_transaksi',
                    'like',
                    "%{$request->get('kode_transaksi_cari')}%"
                )
                ->orderBy('id_transaksi', 'desc')
                ->get();
        } else {
            $transaksi = TransaksiKasir::join(
                'users',
                'transaksi_kasir.id_users',
                '=',
                'users.id_users'
            )
                ->where(
                    'tanggal_transaksi',
                    'like',
                    '%' . reverse_date($request->tanggal_transaksi_cari) . '%'
                )
                ->where(
                    'kode_transaksi',
                    'like',
                    "%{$request->get('kode_transaksi_cari')}%"
                )
                ->where('transaksi_kasir.id_users', auth()->id())
                ->orderBy('id_transaksi', 'desc')
                ->get();
        }

        $datatables = Datatables::of($transaksi)
            // ->filter(function($query)use($request){
            // if ($request->has('kode_transaksi_cari')) {
            //     $query->where('kode_transaksi', 'like', "%{$request->get('kode_transaksi_cari')}%");
            // }
            // })
            ->addColumn('action', function ($action) {
                $column =
                    '
    				 <a href="' .
                    url(
                        "/$this->level/data-penjualan/cetak/$action->id_transaksi"
                    ) .
                    '">
    				 	<button class="btn btn-success"> Cetak </button>
    				 </a>
    				  <a href="' .
                    url(
                        "/$this->level/data-penjualan/detail/$action->id_transaksi"
                    ) .
                    '">
    					  <button class="btn btn-info"> Detail </button>
					   </a>';
                // if ($this->level == 'admin') {
                // 	$column .= '<a href="'.url("/admin/data-transaksi/delete/$action->id_transaksi").'">
                // 				  <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                // 			   </a>';
                // }
                return $column;
            })
            ->editColumn('tanggal_transaksi', function ($edit) {
                return human_date($edit->tanggal_transaksi);
            })
            ->editColumn('total', function ($edit) {
                return format_rupiah($edit->total);
            })
            ->editColumn('bayar', function ($edit) {
                return format_rupiah($edit->bayar);
            })
            ->editColumn('kembali', function ($edit) {
                return format_rupiah($edit->kembali);
            })
            ->make(true);
        // ->filter(function($query)use($request){
        //           if ($request->has('kode_transaksi_cari')) {
        //               $query->where('kode_transaksi', 'like', "%{$request->get('kode_transaksi_cari')}%");
        //           }
        //           if ($request->has('tanggal_transaksi')) {
        //               $query->where('tanggal_transaksi', 'like', "%{$request->get('tanggal_transaksi_cari')}%");
        //           }
        // })
        return $datatables;
    }

    public function dataTransaksiDetail($id)
    {
        $transaksi_detail = TransaksiKasirDetail::join(
            'obat',
            'transaksi_kasir_det.id_obat',
            '=',
            'obat.id_obat'
        )
            ->join(
                'supplier_obat',
                'transaksi_kasir_det.id_supplier',
                '=',
                'supplier_obat.id_supplier'
            )
            ->where('id_transaksi', $id)
            ->get();
        $datatables = Datatables::of($transaksi_detail)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/admin/data-penjualan/detail/$action->id_transaksi/delete/$action->id_transaksi_det"
                    ) .
                    '">
    					  <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->editColumn('sub_total', function ($edit) {
                return format_rupiah($edit->sub_total);
            })
            ->editColumn('diskon', function ($edit) {
                $diskon = '';
                if ($edit->diskon != 0) {
                    if (
                        $edit->jenis_diskon == 'persen' ||
                        $edit->jenis_diskon == ''
                    ) {
                        $diskon = $edit->diskon . '%';
                    } elseif ($edit->jenis_diskon == 'rupiah') {
                        $diskon = format_rupiah($edit->diskon);
                    }
                } else {
                    $diskon = '-';
                }

                return $diskon;
            })
            ->editColumn('diskon_rupiah', function ($edit) {
                $diskon = '';
                if ($edit->diskon != 0) {
                    if (
                        $edit->jenis_diskon == 'persen' ||
                        $edit->jenis_diskon == ''
                    ) {
                        $calc = get_discount(
                            $edit->hja_upds * $edit->jumlah,
                            $edit->diskon
                        );
                        $diskon = format_rupiah($calc);
                    } elseif ($edit->jenis_diskon == 'rupiah') {
                        $diskon = format_rupiah($edit->diskon);
                    }
                } else {
                    $diskon = '-';
                }

                return $diskon;
            })
            ->make(true);
        return $datatables;
    }

    public function dataTransaksiRacikObat(Request $request)
    {
        if ($this->level == 'admin') {
            $data_transaksi = TransaksiRacikObat::getData();
        } else {
            $data_transaksi = TransaksiRacikObat::getData(auth()->id());
        }

        $datatables = Datatables::of($data_transaksi)
            ->filter(function ($query) use ($request) {
                if ($request->has('kode_transaksi_cari')) {
                    $query->where(
                        'kode_transaksi',
                        'like',
                        '%' . $request->kode_transaksi_cari . '%'
                    );
                }
                if ($request->has('nama_pasien_cari')) {
                    $query->where(
                        'nama_pasien',
                        'like',
                        '%' . $request->nama_pasien_cari . '%'
                    );
                }
                if ($request->has('tanggal_transaksi_cari')) {
                    $query->where(
                        'tanggal_transaksi',
                        'like',
                        '%' .
                            reverse_date($request->tanggal_transaksi_cari) .
                            '%'
                    );
                }
            })
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-penjualan-racik-obat/cetak/$action->id_transaksi_racik_obat"
                    ) .
                    '">
    					  <button class="btn btn-success"> Cetak </button>
					   </a>
					   <a href="' .
                    url(
                        "/$this->level/data-racik-obat/$action->id_racik_obat_data"
                    ) .
                    '">
    					  <button class="btn btn-info">Detail Racik</button>
					   </a>
    				';
                // if ($this->level == 'admin') {
                // 	$column .= '
                //   <a href="'.url("/admin/data-transaksi-racik-obat/delete/$action->id_transaksi_racik_obat").'">
                //   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                //   </a>';
                // }
                return $column;
            })
            ->editColumn('diskon', function ($edit) {
                $diskon = '';
                if ($edit->diskon != 0) {
                    $diskon = $edit->diskon . '%';
                } else {
                    $diskon = '-';
                }
                return $diskon;
            })
            ->editColumn('diskon_rupiah', function ($edit) {
                $sum_total = RacikObatData::join(
                    'transaksi_racik_obat',
                    'transaksi_racik_obat.id_racik_obat_data',
                    '=',
                    'racik_obat_data.id_racik_obat_data'
                )
                    ->where('kode_transaksi', $edit->kode_transaksi)
                    ->sum('total_semua');

                return format_rupiah(
                    floor_thousand(
                        get_discount($edit->diskon, $sum_total),
                        1000
                    )
                );
            })
            ->editColumn('harga_total', function ($edit) {
                return format_rupiah($edit->harga_total);
            })
            ->editColumn('bayar', function ($edit) {
                return format_rupiah($edit->bayar);
            })
            ->editColumn('kembalian', function ($edit) {
                return format_rupiah($edit->kembalian);
            })
            ->editColumn('tanggal_transaksi', function ($edit) {
                return human_date($edit->tanggal_transaksi);
            })
            ->make(true);
        return $datatables;
    }

    public function dataRacikObat($id)
    {
        $data_racik = RacikObat::where('id_racik_obat_data', $id)->get();

        $datatables = Datatables::of($data_racik)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/data-racik-obat/$action->id_racik_obat_data/detail/$action->id_racik_obat"
                    ) .
                    '">
    					  <button class="btn btn-info">Detail Obat</button>
					   </a>
    				';
                return $column;
            })
            ->editColumn('jumlah_racik', function ($edit) {
                if ($edit->jumlah_racik == 0) {
                    $jumlah = '-';
                } else {
                    $jumlah =
                        $edit->jumlah_racik . ' ' . $edit->keterangan_racik;
                }

                return $jumlah;
            })
            ->editColumn('ongkos_racik', function ($edit) {
                return format_rupiah($edit->ongkos_racik);
            })
            ->editColumn('harga_total_racik', function ($edit) {
                return format_rupiah($edit->harga_total_racik);
            })
            ->editColumn('jenis_racik', function ($edit) {
                return unslug_str($edit->jenis_racik);
            })
            ->make(true);
        return $datatables;
    }

    public function dataRacikObatDetail($id)
    {
        $data_racik_detail = RacikObatDetail::getData($id);

        $datatables = Datatables::of($data_racik_detail)
            ->editColumn('sub_total', function ($edit) {
                return format_rupiah($edit->sub_total);
            })
            ->editColumn('embalase', function ($edit) {
                return format_rupiah($edit->embalase);
            })
            ->make(true);
        return $datatables;
    }

    public function dataStokOpnem()
    {
        $transaksi = StokOpnem::all();

        $datatables = Datatables::of($transaksi)
            ->addColumn('action', function ($action) {
                // if ($action->status_input == 0) {
                //   		$column = '<a href="'.url("/$this->level/stok-opnem/lanjut-input/$action->id_stok_opnem").'">
                //   					  <button class="btn btn-info"> Lanjut Input </button>
                // 			   </a>
                //   					<a href="'.url("/$this->level/stok-opnem/cetak/$action->id_stok_opnem").'">
                //   					  <button class="btn btn-info"> Cetak </button>
                // 			   </a>';
                // $column.='
                // 		   <a href="'.url("/$this->level/stok-opnem/detail/$action->id_stok_opnem").'">
                //  					  <button class="btn btn-info"> Detail </button>
                // 		   </a>';
                // }
                // else {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/stok-opnem/cetak/$action->id_stok_opnem"
                    ) .
                    '">
	    					  <button class="btn btn-success"> Cetak </button>
						   </a>
						   <a href="' .
                    url(
                        "/$this->level/stok-opnem/detail/$action->id_stok_opnem"
                    ) .
                    '">
	    					  <button class="btn btn-info"> Detail </button>
						   </a>
	    					<a href="' .
                    url(
                        "/$this->level/stok-opnem/export/$action->id_stok_opnem"
                    ) .
                    '">
	    					  <button class="btn btn-success"> Export <span class="fa fa-file-excel-o"></span> </button>
						   </a>';
                // }
                // if ($this->level == 'admin') {
                // $column .= '<a href="'.url("/admin/stok-opnem/delete/$action->id_stok_opnem").'">
                //  					  <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
                // 		   </a>';
                // }
                return $column;
            })
            ->editColumn('tanggal_stok_opnem', function ($edit) {
                return human_date($edit->tanggal_stok_opnem);
            })
            ->addColumn('total_nilai', function ($add) {
                $total = StokOpnemDetail::where(
                    'id_stok_opnem',
                    $add->id_stok_opnem
                )->sum('sub_nilai');

                return format_rupiah($total);
            })
            ->make(true);
        return $datatables;
    }

    public function dataStokOpnemDetail($id)
    {
        $stok_opnem_detail = StokOpnemDetail::join(
            'obat',
            'stok_opnem_detail.id_obat',
            '=',
            'obat.id_obat'
        )
            ->where('id_stok_opnem', $id)
            ->get();

        $datatables = Datatables::of($stok_opnem_detail)
            ->editColumn('tanggal_expired', function ($edit) {
                return human_date($edit->tanggal_expired);
            })
            ->editColumn('harga_modal', function ($edit) {
                return format_rupiah($edit->harga_modal);
            })
            ->editColumn('sub_nilai', function ($edit) {
                return format_rupiah($edit->sub_nilai);
            })
            ->make(true);
        return $datatables;
    }

    public function dataReturBarang()
    {
        $retur_barang = ReturBarang::all();
        $datatables = Datatables::of($retur_barang)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/$this->level/retur-barang/detail/$action->id_retur_barang"
                    ) .
                    '">
    					  <button class="btn btn-info"> Detail </button>
					   </a>
    				';
                if ($this->level == 'admin') {
                    $column .=
                        '
					   <a href="' .
                        url(
                            "/$this->level/retur-barang/delete/$action->id_retur_barang"
                        ) .
                        '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>';
                }
                return $column;
            })
            ->editColumn('tanggal_retur', function ($edit) {
                return human_date($edit->tanggal_retur);
            })
            ->editColumn('total_nominal_retur', function ($edit) {
                return format_rupiah($edit->total_nominal_retur);
            })
            ->make(true);
        return $datatables;
    }

    public function dataReturBarangDetail($id)
    {
        $retur_barang_detail = ReturBarangDetail::join(
            'obat',
            'retur_barang_detail.id_obat',
            '=',
            'obat.id_obat'
        )
            ->where('id_retur_barang', $id)
            ->get();

        $datatables = Datatables::of($retur_barang_detail)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url(
                        "/admin/retur-barang/detail/$action->id_retur_barang/delete/$action->id_retur_barang_detail"
                    ) .
                    '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->editColumn('nominal_retur', function ($edit) {
                return format_rupiah($edit->nominal_retur);
            })
            ->make(true);
        return $datatables;
    }

    public function dataUsers()
    {
        $users = User::whereNotIn('level_user', [2])
            ->where('status_delete', 0)
            ->get();
        $datatables = Datatables::of($users)
            ->addColumn('action', function ($action) {
                $array = [
                    0 => ['class' => 'btn-success', 'text' => 'Aktifkan'],
                    1 => ['class' => 'btn-danger', 'text' => 'Non Aktifkan'],
                ];
                $column =
                    '<a href="' .
                    url("/admin/data-users/status-user/$action->id_users") .
                    '">
    					  <button class="btn ' .
                    $array[$action->active]['class'] .
                    '"> ' .
                    $array[$action->active]['text'] .
                    ' </button>
					   </a>
					   <a href="' .
                    url("/admin/data-users/edit/$action->id_users") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>
					   <a href="' .
                    url("/$this->level/data-users/delete/$action->id_users") .
                    '">
					   	   <button class="btn btn-danger" onclick="return confirm(\'Yakin Hapus ?\');"> Hapus </button>
					   </a>
    				';
                return $column;
            })
            ->editColumn('active', function ($edit) {
                $array = [
                    0 => [
                        'class' => 'label label-danger',
                        'text' => 'Non Aktif',
                    ],
                    1 => ['class' => 'label label-success', 'text' => 'Aktif'],
                ];
                return '<span class="' .
                    $array[$edit->active]['class'] .
                    '">' .
                    $array[$edit->active]['text'] .
                    '</span>';
            })
            ->rawColumns(['active', 'action'])
            ->make(true);
        return $datatables;
    }

    public function dataPpn()
    {
        $persen_ppn = PersenPpn::all();
        $datatables = Datatables::of($persen_ppn)
            ->addColumn('action', function ($action) {
                $column =
                    '<a href="' .
                    url("/$this->level/data-ppn/edit/$action->id_persen_ppn") .
                    '">
    					  <button class="btn btn-warning"> Edit </button>
					   </a>';
                return $column;
            })
            ->editColumn('ppn', function ($edit) {
                return $edit->ppn . '%';
            })
            ->make(true);
        return $datatables;
    }

    // AJAX PROSES //
    public function getObat($id_jenis)
    {
        $obat = Obat::where('id_jenis_obat', $id_jenis)->get();
        $array[0] =
            '<option value="" selected="selected" disabled="disabled">=== Pilih Obat === </option>';
        foreach ($obat as $key => $value) {
            $html =
                '<option value="' .
                $value->kode_obat .
                '">' .
                $value->nama_obat .
                ' | ' .
                cek_stok($value->stok_obat) .
                '</option>';
            $array[] = $html;
        }
        return response()->json($array);
    }

    public function getObatBySupplier($id_supplier)
    {
        $obat = ObatDetail::join(
            'obat',
            'obat_detail.id_obat',
            '=',
            'obat.id_obat'
        )
            ->join(
                'supplier_obat',
                'obat_detail.id_supplier',
                'supplier_obat.id_supplier'
            )
            ->where('obat_detail.id_supplier', $id_supplier)
            ->get();

        $array[0] =
            '<option value="" selected="selected" disabled="disabled">=== Pilih Obat === </option>';
        foreach ($obat as $key => $value) {
            $html =
                '<option value="' .
                $value->id_obat .
                '">' .
                $value->nama_obat .
                ' | ' .
                cek_stok($value->stok_obat, $value->satuan_obat) .
                '</option>';
            $array[] = $html;
        }
        return response()->json($array);
    }

    public function getDetailObat($id_obat, $attr)
    {
        $obat = Obat::join(
            'pabrik_obat',
            'obat.id_pabrik_obat',
            '=',
            'pabrik_obat.id_pabrik_obat'
        )
            ->where('id_obat', $id_obat)
            ->firstOrFail();
        if ($attr == 'relasi') {
            $hja_obat = $obat->hja_relasi;
        } else {
            $hja_obat = $obat->hja_upds;
        }

        $komposisi_obat = KomposisiObat::where(
            'id_obat',
            $obat->id_obat
        )->get();

        foreach ($komposisi_obat as $index => $data) {
            $html_2 =
                '<div class="form-group"><input type="text" class="form-control" value="' .
                $data->nama_komposisi .
                ' ' .
                $data->takaran_komposisi .
                '" disabled="disabled"></div>';
            $array_2[] = $html_2;
        }
        if (count($komposisi_obat) == 0) {
            $array_2[0] =
                '<div class="form-group"><input type="text" class="form-control" disabled="disabled"></div>';
        }

        $data_return = [
            'harga_modal' => $obat->harga_modal,
            'harga_modal_ppn' => $obat->harga_modal_ppn,
            'harga_jual_obat' => $hja_obat,
            'pabrik' => $obat->nama_pabrik,
            'satuan_obat' => $obat->satuan_obat,
            'data_komposisi' => $array_2,
        ];
        return response()->json($data_return);
    }

    public function getInfoObat($id_obat)
    {
        $get_obat = Obat::where('id_obat', $id_obat)->firstOrFail();
        $get_jenis = Obat::jenisObat($id_obat);
        $supplier = ObatDetail::join(
            'supplier_obat',
            'obat_detail.id_supplier',
            '=',
            'supplier_obat.id_supplier'
        )
            ->where('id_obat', $id_obat)
            ->get();
        $margin_obat = MarginObat::firstOrFail();

        $hja_resep = $get_obat->hja_resep;

        $array[0] =
            '<option value="" selected="selected" disabled="disabled">=== Pilih Supplier Obat === </option>';
        foreach ($supplier as $key => $value) {
            $html =
                '<option value="' .
                $value->id_supplier .
                '">' .
                $value->nama_supplier .
                '</option>';
            $array[] = $html;
        }

        return response()->json([
            'jenis_obat' => $get_jenis->nama_jenis_obat,
            'stok_obat' => $get_obat->stok_obat,
            'hna' => $get_obat->harga_modal,
            'hja_resep' => $hja_resep,
            'harga_modal_ppn' => $get_obat->harga_modal_ppn,
            'margin_resep' => $margin_obat->margin_resep,
            'format' => format_rupiah($hja_resep),
            'supplier_obat' => $array,
            'satuan_obat' => $get_obat->satuan_obat,
        ]);
    }

    public function inputObatBeli(Request $request)
    {
        $obat = $request->obat_beli;
        $jumlah = $request->jumlah;
        $satuan = $request->satuan;
        $jenis_ppn = $request->jenis_ppn;
        $ppn = $request->ppn;
        $harga_obat = replace_comma_to_dot($request->harga_obat);
        $disc_1 =
            $request->disc_1 == '' ? 0 : replace_comma_to_dot($request->disc_1);
        $disc_2 =
            $request->disc_2 == '' ? 0 : replace_comma_to_dot($request->disc_2);
        $disc_3 =
            $request->disc_3 == '' ? 0 : replace_comma_to_dot($request->disc_3);

        // dd([$disc_1,$disc_2,$disc_3]);

        $get_harga_modal = Obat::where('id_obat', $obat)->firstOrFail()
            ->harga_modal;

        if ($jenis_ppn == 'exclude-ppn') {
            $ppn = PersenPpn::firstOrFail()->ppn;
        } else {
            $ppn = 0;
        }

        $session_obat_beli = session()->get('beli_obat');
        $get_beli = $session_obat_beli['data_beli'];

        $no = count($get_beli) + 1;

        // if ($disc_1 != 0 && $disc_2 != 0 && $disc_3 != 0) {
        // 	$disc_total  = $disc_1+$disc_2+$disc_3;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 != 0 && $disc_2 != 0 && $disc_3 == 0) {
        // 	$disc_total  = $disc_1+$disc_2;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 == 0 && $disc_2 != 0 && $disc_3 != 0) {
        // 	$disc_total  = $disc_2+$disc_3;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 != 0 && $disc_2 == 0 && $disc_3 != 0) {
        // 	$disc_total  = $disc_1+$disc_3;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 != 0 && $disc_2 == 0 && $disc_3 == 0) {
        // 	$disc_total  = $disc_1;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 == 0 && $disc_2 != 0 && $disc_3 == 0) {
        // 	$disc_total  = $disc_2;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 == 0 && $disc_2 == 0 && $disc_3 != 0) {
        // 	$disc_total  = $disc_3;
        // 	// $disc_total  = $disc_total + (($disc_total * $ppn) / 100);
        // 	// $ppn_calc    = (($harga_obat * $ppn) / 100) * $jumlah;
        // 	// $ppn_calc = (($harga_obat * $jumlah) * $ppn) / 100;
        // 	$sub_total = ($harga_obat * $jumlah) - ((($harga_obat * $jumlah) * $disc_total) / 100);
        // 	// $sub_total   = $disc_hitung;
        // }
        // else if ($disc_1 == 0 && $disc_2 == 0 && $disc_3 == 0) {
        // 	$sub_total = $harga_obat * $jumlah;
        // 	$sub_total = $sub_total;
        // 	// $ppn_calc  = (($harga_obat * $ppn) / 100) * $jumlah;
        // }

        $disc_nominal = ($harga_obat * $jumlah * $disc_1) / 100;
        $sub_total =
            $harga_obat * $jumlah - ($harga_obat * $jumlah * $disc_1) / 100;

        $disc_nominal = $disc_nominal + ($sub_total * $disc_2) / 100;
        $sub_total = $sub_total - ($sub_total * $disc_2) / 100;

        $disc_nominal = $disc_nominal + ($sub_total * $disc_3) / 100;
        $sub_total = $sub_total - ($sub_total * $disc_3) / 100;
        // if ($jenis_ppn != 'exclude-ppn') {
        // 	$ppn_calc = 0;
        // }

        $nama_obat = Obat::where('id_obat', $obat)->firstOrFail()->nama_obat;
        $html =
            '<tr>
					<td class="number-beli"></td>
					<td>' .
            $nama_obat .
            '</td>
					<td>' .
            $jumlah .
            '</td>
					<td>' .
            format_rupiah($harga_obat) .
            '</td>
					<td>' .
            $disc_1 .
            '%</td>
					<td>' .
            $disc_2 .
            '%</td>
					<td>' .
            $disc_3 .
            '%</td>
					<td>' .
            format_rupiah($sub_total) .
            '</td>
					<td><button class="btn btn-danger delete-beli" id-delete="' .
            count($get_beli) .
            '">X</button></td>
				</tr>
				';

        if ($jenis_ppn == 'exclude-ppn') {
            $ppn = PersenPpn::firstOrFail()->ppn;
        } else {
            $ppn = 0;
        }

        $dpp_total = $session_obat_beli['dpp'] + $sub_total;
        // $dpp_total   = number_format((float)($dpp_total),2,'.','');
        $ppn_total = ($dpp_total * $ppn) / 100;
        // $ppn_total   = number_format((float)($ppn_total),2,'.','');
        $total_semua = $dpp_total + $ppn_total;
        // $total_semua = number_format((float)($total_semua_total),2,'.','');
        $disc_total = $disc_nominal + $session_obat_beli['discount'];

        $data_session = [
            'id_obat' => $obat,
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            // 'ppn'       => $ppn_sub,
            'jenis_ppn' => $jenis_ppn,
            'disc_nominal' => $disc_nominal,
            'harga_modal' => $get_harga_modal,
            'harga_obat' => $harga_obat,
            'disc_1' => $disc_1,
            'disc_2' => $disc_2,
            'disc_3' => $disc_3,
            'sub_total' => $sub_total,
            'data_html' => $html,
        ];
        array_push($get_beli, $data_session);
        session()->put('beli_obat', [
            'data_beli' => $get_beli,
            'total_semua' => $total_semua,
            'dpp' => $dpp_total,
            'ppn' => $ppn_total,
            'discount' => $disc_total,
        ]);

        // dd(ceil(session()->get('beli_obat')['dpp']));
        return response()->json([
            'data_beli' => $data_session,
            'total_semua' => $total_semua,
            'dpp' => $dpp_total,
            'ppn' => $ppn_total,
            'discount' => $disc_total,
        ]);
    }

    public function deleteBeli($index)
    {
        $get_beli = session()->get('beli_obat')['data_beli'];
        $total_semua = session()->get('beli_obat')['total_semua'];
        // $ppn      = session()->get('beli_obat')['ppn'];
        $dpp = session()->get('beli_obat')['dpp'];
        $disc_total = session()->get('beli_obat')['discount'];
        $get_ppn = PersenPpn::firstOrFail()->ppn;

        $discount = $disc_total - $get_beli[$index]['disc_nominal'];

        if ($get_beli[$index]['jenis_ppn'] == 'exclude-ppn') {
            $dpp = $dpp - $get_beli[$index]['sub_total'];
            $ppn = ($dpp * $get_ppn) / 100;
            $total_semua = $dpp + $ppn;
        } else {
            $dpp = $dpp - $get_beli[$index]['sub_total'];
            $ppn = 0;
            $total_semua = $dpp;
        }

        unset($get_beli[$index]);

        session()->put('beli_obat', [
            'data_beli' => $get_beli,
            'total_semua' => $total_semua,
            'dpp' => $dpp,
            'ppn' => $ppn,
            'discount' => $discount,
        ]);

        return response()->json([
            'total_semua' => $total_semua,
            'dpp' => $dpp,
            'ppn' => $ppn,
            'discount' => $discount,
        ]);
    }

    public function simpanRacik(Request $request)
    {
        $nama_racik = $request->nama_racik;
        $jenis_racik =
            $request->jenis_racik != null ? $request->jenis_racik : '-';
        $jumlah_racik =
            $request->jumlah_racik != null ? $request->jumlah_racik : 0;
        $ongkos_racik =
            $request->ongkos_racik != null ? $request->ongkos_racik : 0;
        $keterangan_racik = $request->keterangan_racik;
        $type_racik = $request->type_racik;
        $obat = $request->obat;
        $satuan_obat = $request->satuan_obat;
        $jumlah = $request->jumlah;
        $embalase = $request->embalase;
        $harga_total = $request->harga_total;
        $kode_racik =
            $request->kode_racik == null
                ? (string) Str::uuid()
                : $request->kode_racik;
        $total_semua = $request->total_semua;

        // ============================= //
        // $session_racik = session()->get('racikan_obat');
        // $racikan_obat  = $session_racik['data_racik'];
        // $no            = $session_racik['counter']+1;

        // $nama_racik    = $nama_racik != null ? $nama_racik : 'Obat Tanpa Racik '.$no;
        // $total_racik   = 0;

        // foreach ($obat as $key => $value) {
        // 	$get_obat = Obat::where('id_obat',$value)->firstOrFail();
        // 	$supplier = ObatDetail::getShuffleByObat($value)->id_supplier;

        // 	$data_obat[] = [
        // 		'id_obat'      => $value,
        // 		'id_supplier'  => $supplier,
        // 		'nama_obat'    => $get_obat->nama_obat,
        // 		'jumlah'       => $jumlah[$key],
        // 		'embalase'	   => $embalase[$key],
        // 		'harga_satuan' => $get_obat->hja_resep,
        // 		'satuan_obat'  => $satuan_obat[$key],
        // 		'harga_total'  => $harga_total[$key]
        // 	];

        // 	$total_racik = $total_racik+$data_obat[$key]['harga_total']+$data_obat[$key]['embalase'];
        // }

        // $sub_total_racik    = $total_racik;

        // $jumlah_racik_table = $jumlah_racik != 0 ? $jumlah_racik : '-';

        // $data_racik = [
        // 	'nama_racik'       => $nama_racik,
        // 	'jenis_racik'      => $jenis_racik,
        // 	'jumlah_racik'     => $jumlah_racik,
        // 	'ongkos_racik'     => $ongkos_racik,
        // 	'keterangan_racik' => $keterangan_racik,
        // 	'data_obat'        => $data_obat,
        // 	'table' 	   	   => '<tr>
        // 							 <td class="number-resep"></td>
        // 							 <td>'.$nama_racik.'</td>
        // 							 <td>'.$jumlah_racik_table.' '.$keterangan_racik.'</td>
        // 							 <td>'.format_rupiah($sub_total_racik+$ongkos_racik).'</td>
        // 							 <td><button class="btn btn-info detail-racik" data-toggle="modal" data-target="#modal-detail-racik" id-detail="'.count($racikan_obat).'"><span class="fa fa-info"></span></button>
        // 							 <button class="btn btn-danger delete-racik" id-delete="'.count($racikan_obat).'">X</button></td>
        // 						  </tr>',
        // 	'type_racik' 	   => 'input-racik',
        // 	'total_racik' 	   => $sub_total_racik
        // ];

        // array_push($racikan_obat,$data_racik);

        // session()->put('racikan_obat',['counter' => $no, 'data_racik' => $racikan_obat, 'total_semua' => $total_semua]);

        //=================================================//

        $data_resep = [
            'kode_racik' => $kode_racik,
            'nama_racik' => $nama_racik,
            'jenis_racik' => $jenis_racik,
            'jumlah_racik' => $jumlah_racik,
            'ongkos_racik' => $ongkos_racik,
            'total_racik' => 0,
            'keterangan_racik' => $keterangan_racik,
        ];
        $id_racik_obat_sementara = RacikObatSementara::insertGetId($data_resep);

        $total_racik = 0;
        foreach ($obat as $key => $value) {
            $get_obat = Obat::where('id_obat', $value)->firstOrFail();
            $supplier = ObatDetail::getShuffleByObat($value)->id_supplier;

            $data_obat[] = [
                'id_racik_obat_sementara' => $id_racik_obat_sementara,
                'id_obat' => $value,
                'id_supplier' => $supplier,
                // 'nama_obat'            => $get_obat->nama_obat,
                'jumlah' => $jumlah[$key],
                'embalase' => $embalase[$key],
                // 'harga_satuan'            => $get_obat->hja_resep,
                // 'satuan_obat'          => $satuan_obat[$key],
                'sub_total' => $harga_total[$key],
            ];

            $total_racik =
                $total_racik +
                ($data_obat[$key]['sub_total'] + $data_obat[$key]['embalase']);

            RacikObatSementaraDetail::create($data_obat[$key]);
        }

        // $sub_total_racik    = $total_racik;
        $total_semua = $total_semua + $total_racik + $ongkos_racik;
        RacikObatSementara::where(
            'id_racik_obat_sementara',
            $id_racik_obat_sementara
        )->update(['total_racik' => $total_racik]);

        $data_racik = [
            'nama_racik' => $nama_racik,
            'jenis_racik' => $jenis_racik,
            'jumlah_racik' => $jumlah_racik,
            'ongkos_racik' => $ongkos_racik,
            'keterangan_racik' => $keterangan_racik,
            // 'data_obat'        => $data_obat,
            'table' =>
                '<tr>
									 <td class="number-resep"></td>
									 <td>' .
                $nama_racik .
                '</td>
									 <td>' .
                $jumlah_racik .
                ' ' .
                $keterangan_racik .
                '</td>
									 <td>' .
                format_rupiah($total_racik + $ongkos_racik) .
                '</td>
									 <td><button class="btn btn-info detail-racik" data-toggle="modal" data-target="#modal-detail-racik" id-detail="' .
                $id_racik_obat_sementara .
                '"><span class="fa fa-info"></span></button>
									 <button class="btn btn-danger delete-racik" id-delete="' .
                $id_racik_obat_sementara .
                '">X</button></td>
								  </tr>',
            'type_racik' => 'input-racik',
            'total_racik' => $total_racik,
            'kode_racik' => $kode_racik,
        ];

        return response()->json([
            'data_racik' => $data_racik,
            'total_semua' => $total_semua,
            'kode_racik' => $kode_racik,
        ]);
    }

    public function detailRacik($index)
    {
        // $get_racik   = session()->get('racikan_obat')['data_racik'];
        // $table = '';
        $get_racik_sementara = RacikObatSementara::where(
            'id_racik_obat_sementara',
            $index
        )->firstOrFail();
        $no = 0;
        // for ($i=0; $i < 1; $i++) {
        // $nama_racik       = $get_racik[$index]['nama_racik'];
        // $jumlah_racik     = $get_racik[$index]['jumlah_racik'] != 0 ? $get_racik[$index]['jumlah_racik'] : '-';
        // $ongkos_racik     = $get_racik[$index]['ongkos_racik'];
        // $keterangan_racik = $get_racik[$index]['keterangan_racik'];
        $no = $no + 1;
        $table =
            '
				<tr>
					<td>' .
            $no .
            '</td>
					<td>' .
            $get_racik_sementara->nama_racik .
            '</td>
					<td>' .
            $get_racik_sementara->jumlah_racik .
            ' ' .
            $get_racik_sementara->keterangan_racik .
            '</td>
					<td>' .
            format_rupiah($get_racik_sementara->ongkos_racik) .
            '</td>
				<tr>
			';
        $get_racik_sementara_detail = RacikObatSementaraDetail::join(
            'obat',
            'racik_obat_sementara_detail.id_obat',
            '=',
            'obat.id_obat'
        )
            ->where('id_racik_obat_sementara', $index)
            ->get();
        foreach ($get_racik_sementara_detail as $key => $value) {
            // $nama_obat   = $get_racik[$index]['data_obat'][$j]['nama_obat'];
            // $jumlah      = $get_racik[$index]['data_obat'][$j]['jumlah'];
            // $satuan_obat = $get_racik[$index]['data_obat'][$j]['satuan_obat'];
            // $harga_total = $get_racik[$index]['data_obat'][$j]['harga_total'];
            // $embalase = $get_racik[$index]['data_obat'][$j]['embalase'];
            $no = $no + 1;
            $table .=
                '
					<tr>
						<td>' .
                $no .
                '</td>
						<td>' .
                $value->nama_obat .
                ' + Embalase</td>
						<td>' .
                $value->jumlah .
                ' ' .
                $value->satuan_obat .
                '</td>
						<td>' .
                format_rupiah($value->sub_total + $value->embalase) .
                '</td>
					<tr>
				';
        }
        // }

        return response()->json($table);
    }

    public function deleteRacik(Request $request)
    {
        $id_racik = $request->id_racik;
        $total_semua = $request->total_semua;
        // $get_racik   = session()->get('racikan_obat')['data_racik'];
        // $total_semua = session()->get('racikan_obat')['total_semua'];
        // $counter 	 = session()->get('racikan_obat')['counter'];
        // $total_semua = $total_semua - ($get_racik[$index]['total_racik'] + $get_racik[$index]['ongkos_racik']);
        // unset($get_racik[$index]);

        // session()->put('racikan_obat',['counter' => $counter,'data_racik' => $get_racik, 'total_semua' => $total_semua]);

        $get_racik_sementara = RacikObatSementara::where(
            'id_racik_obat_sementara',
            $id_racik
        )->firstOrFail();
        $total_semua =
            $total_semua -
            ($get_racik_sementara->total_racik +
                $get_racik_sementara->ongkos_racik);

        RacikObatSementara::where(
            'id_racik_obat_sementara',
            $id_racik
        )->delete();

        return response()->json($total_semua);
    }

    public function simpanTanpaRacik(Request $request)
    {
        // $session_racik = session()->get('racikan_obat');
        // $counter       = $session_racik['counter'];
        // $racikan_obat  = $session_racik['data_racik'];

        $obat = $request->obat;
        $satuan_obat = $request->satuan_obat;
        $jumlah = $request->jumlah;
        $embalase = $request->embalase;
        $harga_total = $request->harga_total;
        $kode_racik =
            $request->kode_racik == null
                ? (string) Str::uuid()
                : $request->kode_racik;
        $total_racik = 0;
        $total_semua = $request->total_semua;

        $check_racik = RacikObatSementara::where(
            'kode_racik',
            $kode_racik
        )->where('nama_racik', 'Obat Tanpa Racik');
        if ($check_racik->count() == 0) {
            $data_resep = [
                'kode_racik' => $kode_racik,
                'nama_racik' => 'Obat Tanpa Racik',
                'jenis_racik' => '-',
                'jumlah_racik' => 0,
                'ongkos_racik' => 0,
                'total_racik' => 0,
                'keterangan_racik' => '-',
            ];
            $id_racik_obat_sementara = RacikObatSementara::insertGetId(
                $data_resep
            );
        } else {
            $id_racik_obat_sementara = $check_racik->firstOrFail()
                ->id_racik_obat_sementara;
        }

        // $get_index = array_search('Obat Tanpa Racik',array_column($racikan_obat,'nama_racik'));
        // if (count($racikan_obat) > 0 && (string)$get_index != '') {
        // 	$get_obat_tanpa_racik  = $racikan_obat[$get_index]['data_obat'];
        // 	$get_table_tanpa_racik = $racikan_obat[$get_index]['table'];
        // 	$total_racik           = $racikan_obat[$get_index]['total_racik'];
        // 	$no = 1;

        // 	foreach ($obat as $key => $value) {
        // 		$no        = $counter+1;
        // 		$get_obat  = Obat::where('id_obat',$value)->firstOrFail();
        // 		$supplier  = ObatDetail::getShuffleByObat($value)->id_supplier;
        // 		$id_delete = array_key_last($get_obat_tanpa_racik)+1;

        // 		$data_obat_tanpa_racik = [
        // 			'id_obat'      => $value,
        // 			'id_supplier'  => $supplier,
        // 			'nama_obat'    => $get_obat->nama_obat,
        // 			'jumlah'       => $jumlah[$key],
        // 			'embalase'	   => $embalase[$key],
        // 			'harga_satuan' => $get_obat->hja_resep,
        // 			'satuan_obat'  => $satuan_obat[$key],
        // 			'harga_total'  => $harga_total[$key]
        // 		];

        // 		$total_semua = $session_racik['total_semua'] - $total_racik;
        // 		$total_racik = $total_racik+$data_obat_tanpa_racik['harga_total']+$data_obat_tanpa_racik['embalase'];

        // 		$table_html = '<tr><td class="number-resep"></td><td>'.$get_obat->nama_obat.'</td><td>'.$jumlah[$key].'</td><td>'.format_rupiah($harga_total[$key]+$embalase[$key]).'</td><td><button class="btn btn-danger delete-tanpa-racik" id-delete="'.$id_delete.'">X</button></td></tr>';

        // 		$table_obat_tanpa_racik[] = $table_html;

        // 		array_push($get_obat_tanpa_racik,$data_obat_tanpa_racik);
        // 		array_push($get_table_tanpa_racik,$table_html);
        // 	}

        // 	$data_tanpa_racik = [
        // 		'nama_racik'       => 'Obat Tanpa Racik',
        // 		'jenis_racik'      => '-',
        // 		'jumlah_racik'     => 0,
        // 		'ongkos_racik'     => 0,
        // 		'keterangan_racik' => '-',
        // 		'data_obat'        => $get_obat_tanpa_racik,
        // 		'table'            => $get_table_tanpa_racik,
        // 		'total_racik'      => $total_racik,
        // 		'type_racik'       => 'input-tanpa-racik'
        // 	];

        // 	$racikan_obat[$get_index] = $data_tanpa_racik;
        // 	$total_semua = $total_semua + $total_racik;

        // 	$data_return = ['table' => $table_obat_tanpa_racik];
        // }
        // else {
        // 	$counter = session()->get('racikan_obat')['counter'];
        // 	$no      = 0;
        // 	$index   = 0;
        // 	foreach ($obat as $key => $value) {
        // 		$no       = $counter+$key+1;
        // 		$get_obat = Obat::where('id_obat',$value)->firstOrFail();
        // 		$supplier = ObatDetail::getShuffleByObat($value)->id_supplier;

        // 		$data_obat_tanpa_racik[] = [
        // 			'id_obat'      => $value,
        // 			'id_supplier'  => $supplier,
        // 			'nama_obat'    => $get_obat->nama_obat,
        // 			'jumlah'       => $jumlah[$key],
        // 			'embalase'	   => $embalase[$key],
        // 			'harga_satuan' => $get_obat->hja_resep,
        // 			'satuan_obat'  => $satuan_obat[$key],
        // 			'harga_total'  => $harga_total[$key]
        // 		];

        // 		$total_racik = $total_racik+$data_obat_tanpa_racik[$key]['harga_total']+$data_obat_tanpa_racik[$key]['embalase'];

        // 		$table_html = '<tr><td class="number-resep"></td><td>'.$get_obat->nama_obat.'</td><td>'.$jumlah[$key].'</td><td>'.format_rupiah($harga_total[$key]+$embalase[$key]).'</td><td><button class="btn btn-danger delete-tanpa-racik" id-delete="'.$index.'">X</button></td></tr>';

        // 		$table_obat_tanpa_racik[] = $table_html;

        // 		$index = $index+1;
        // 	}

        // 	$data_tanpa_racik = [
        // 		'nama_racik'       => 'Obat Tanpa Racik',
        // 		'jenis_racik'      => '-',
        // 		'jumlah_racik'     => 0,
        // 		'ongkos_racik'     => 0,
        // 		'keterangan_racik' => '-',
        // 		'data_obat'        => $data_obat_tanpa_racik,
        // 		'table'            => $table_obat_tanpa_racik,
        // 		'total_racik'      => $total_racik,
        // 		'type_racik'       => 'input-tanpa-racik'
        // 	];

        // 	array_push($racikan_obat,$data_tanpa_racik);

        // 	$total_semua = $session_racik['total_semua'] + $total_racik;

        // 	$data_return = ['table' => $table_obat_tanpa_racik];
        // }
        // session()->put('racikan_obat',['counter' => $no,'data_racik'=>$racikan_obat,'total_semua' => $total_semua]);
        // ================================ //
        foreach ($obat as $key => $value) {
            $get_obat = Obat::where('id_obat', $value)->firstOrFail();
            $supplier = ObatDetail::getShuffleByObat($value)->id_supplier;

            $data_obat_tanpa_racik[] = [
                'id_racik_obat_sementara' => $id_racik_obat_sementara,
                'id_obat' => $value,
                'id_supplier' => $supplier,
                'jumlah' => $jumlah[$key],
                'embalase' => $embalase[$key],
                'sub_total' => $harga_total[$key],
            ];

            $insert_get_id = RacikObatSementaraDetail::insertGetId(
                $data_obat_tanpa_racik[$key]
            );

            $total_racik =
                $total_racik +
                $data_obat_tanpa_racik[$key]['sub_total'] +
                $data_obat_tanpa_racik[$key]['embalase'];

            $table_html =
                '<tr><td class="number-resep"></td><td>' .
                $get_obat->nama_obat .
                '</td><td>' .
                $jumlah[$key] .
                '</td><td>' .
                format_rupiah($harga_total[$key] + $embalase[$key]) .
                '</td><td><button class="btn btn-danger delete-tanpa-racik" id-delete="' .
                $insert_get_id .
                '">X</button></td></tr>';

            $table_obat_tanpa_racik[] = $table_html;
        }

        $total_semua = $total_semua + $total_racik;

        $total_racik_old = RacikObatSementara::where(
            'id_racik_obat_sementara',
            $id_racik_obat_sementara
        )->firstOrFail()->total_racik;

        RacikObatSementara::where(
            'id_racik_obat_sementara',
            $id_racik_obat_sementara
        )->update(['total_racik' => $total_racik + $total_racik_old]);

        $data_return = ['table' => $table_obat_tanpa_racik];

        return response()->json([
            'data_racik' => $data_return,
            'total_semua' => $total_semua,
            'kode_racik' => $kode_racik,
        ]);
    }

    public function deleteTanpaRacik(Request $request)
    {
        $total_semua = $request->total_semua;
        $id_racik_obat_detail = $request->id_racik;
        $get_id_racik_obat_sementara = RacikObatSementaraDetail::where(
            'id_racik_obat_sementara_detail',
            $id_racik_obat_detail
        )->firstOrFail()->id_racik_obat_sementara;
        $get_detail = RacikObatSementaraDetail::where(
            'id_racik_obat_sementara_detail',
            $id_racik_obat_detail
        )->firstOrFail();
        $check = RacikObatSementaraDetail::where(
            'id_racik_obat_sementara',
            $get_id_racik_obat_sementara
        )->count();

        if ($check > 1) {
            $total_semua =
                $total_semua - ($get_detail->embalase + $get_detail->sub_total);

            RacikObatSementaraDetail::where(
                'id_racik_obat_sementara_detail',
                $id_racik_obat_detail
            )->delete();
            $sum_sub_total = RacikObatSementaraDetail::where(
                'id_racik_obat_sementara',
                $get_id_racik_obat_sementara
            )->sum('sub_total');
            $sum_embalase = RacikObatSementaraDetail::where(
                'id_racik_obat_sementara',
                $get_id_racik_obat_sementara
            )->sum('embalase');

            RacikObatSementara::where(
                'id_racik_obat_sementara',
                $get_id_racik_obat_sementara
            )->update(['total_racik' => $sum_sub_total + $sum_embalase]);
        } else {
            $total_semua =
                $total_semua - ($get_detail->embalase + $get_detail->sub_total);

            RacikObatSementara::where(
                'id_racik_obat_sementara',
                $get_id_racik_obat_sementara
            )->delete();
        }

        return response()->json($total_semua);
    }

    public function getObatTransaksi(
        $id_obat,
        $pcs,
        $diskon = 0,
        $btn_attr,
        $attr_diskon
    ) {
        if (!session()->has('nomor_upds_relasi')) {
            session()->put('nomor_upds_relasi', 1);
        }
        $obat = Obat::where('id_obat', $id_obat)->firstOrFail();
        $uuid = (string) Str::uuid();
        $supplier = ObatDetail::getShuffleByObat($id_obat)->id_supplier;
        $nomor = session()->get('nomor_upds_relasi');

        // if($obat->stok_obat == 0) {
        // 	$data = ['log'=>false,'message'=>'Stok Obat Habis'];
        // }
        // elseif ($pcs > $obat->stok_obat) {
        // 	$data = ['log'=>false,'message'=>'Obat Hanya Tersisa '.$obat->stok_obat.' '.$obat->satuan_obat];
        // }
        // else {
        $uraian_diskon = 0;
        $kalkulasi_diskon = 0;

        if ($btn_attr == 'kasir-upds') {
            $satuan_harga_obat_ = $obat->hja_upds;
            $kalkulasi = $obat->hja_upds * $pcs;
            if ($diskon != 0) {
                if ($attr_diskon == 'persen') {
                    $uraian_diskon = ($kalkulasi * $diskon) / 100;
                    if ($uraian_diskon < 1000) {
                        $harga = $obat->hja_upds * $pcs;
                        $uraian_diskon = 0;

                        $harga_jual_obat_sub_ = round_up_thousand($harga, 1000);
                    } else {
                        $harga = $kalkulasi - ($kalkulasi * $diskon) / 100;

                        $harga_jual_obat_sub_ = round_up_thousand($harga, 1000);
                        $round_up_diskon = round_up_thousand(
                            $uraian_diskon,
                            1000
                        );
                        $floor_diskon = floor_thousand($uraian_diskon, 1000);
                        $hitung = $harga_jual_obat_sub_ + $round_up_diskon;
                        if (round_up_thousand($kalkulasi, 1000) == $hitung) {
                            $uraian_diskon = $round_up_diskon;
                        } else {
                            $uraian_diskon = $floor_diskon;
                        }
                    }
                } elseif ($attr_diskon == 'rupiah') {
                    if ($diskon < 1000) {
                        $harga = $obat->hja_upds * $pcs;
                    // $uraian_diskon = 0;
                    } else {
                        $harga = $kalkulasi - $diskon;
                    }

                    // $harga = $kalkulasi - $diskon;
                    $uraian_diskon = $diskon;
                    $harga_jual_obat_sub_ = round_up_thousand($harga, 1000);
                }
            } else {
                $harga = $obat->hja_upds * $pcs;
                $harga_jual_obat_sub_ = round_up_thousand($harga, 1000);
            }

            $kalkulasi = round_up_thousand($kalkulasi, 1000);
        } elseif ($btn_attr == 'kasir-relasi') {
            $satuan_harga_obat_ = $obat->hja_relasi;
            if ($diskon == 0) {
                $kalkulasi = $obat->hja_relasi * $pcs;
                $harga_jual_obat_sub_ = $kalkulasi;
            } else {
                $kalkulasi = $obat->hja_relasi * $pcs;
                $uraian_diskon = ($kalkulasi * $diskon) / 100;
                $harga_jual_obat_sub_ = $kalkulasi - $uraian_diskon;
                // $harga_jual_obat_sub_ = $kalkulasi * $pcs;
            }
        }

        $table =
            '<tr>
						<td class="number-kasir"></td>
						<td>' .
            $obat->nama_obat .
            '</td>
						<td>' .
            $pcs .
            '</td>
						<td>' .
            format_rupiah($satuan_harga_obat_) .
            '
						<td>' .
            format_rupiah($uraian_diskon) .
            '</td>
						<td>' .
            format_rupiah($harga_jual_obat_sub_) .
            '</td>
						<td><button class="btn btn-danger delete" data-id="' .
            $uuid .
            '">X</button></td>
					 </tr>';
        $input_hidden =
            '<div target-id="' .
            $uuid .
            '">
								<input type="hidden" name="obat_trx[]" value="' .
            $id_obat .
            '">
								<input type="hidden" name="pcs_trx[]" value="' .
            $pcs .
            '">
								<input type="hidden" name="diskon_trx[]" value="' .
            $diskon .
            '">
								<input type="hidden" name="diskon_urai[]" value="' .
            $uraian_diskon .
            '">
								<input type="hidden" name="harga_trx[]" value="' .
            $harga_jual_obat_sub_ .
            '">
								<input type="hidden" name="harga_satuan[]" value="' .
            $satuan_harga_obat_ .
            '">
								<input type="hidden" name="supplier_trx[]" value="' .
            $supplier .
            '">
								<input type="hidden" name="jenis_diskon[]" value="rupiah">
								<input type="hidden" name="type_trx[]" value="bayar-tunai">
								<input type="hidden" name="sub_total_obat[]" value="' .
            $kalkulasi .
            '">
								<input type="hidden" name="harga_total" value="' .
            $kalkulasi .
            '">
								<input type="hidden" name="diskon_total" value="' .
            $uraian_diskon .
            '">
							</div>';
        $nomor = $nomor + 1;
        session()->put('nomor_upds_relasi', $nomor);
        $data = [
            'uuid' => $uuid,
            'data_table' => $table,
            'input_hidden' => $input_hidden,
            'harga' => $harga_jual_obat_sub_,
            'kalkulasi' => $kalkulasi,
            'diskon' => $uraian_diskon,
            'log' => true,
        ];
        // }
        return response()->json($data);
    }

    public function ubahStok($id, $pcs, $nm_hrg)
    {
        $harga =
            HargaObat::where('id_obat', $id)
                ->where('nama_harga', $nm_hrg)
                ->firstOrFail()->harga_total * $pcs;
        return response()->json($harga);
    }

    public function getKreditFaktur($id_kredit)
    {
        $kredit = KreditFaktur::whereExists(function ($query) {
            $query
                ->select('*')
                ->from('kredit_det')
                ->whereColumn(
                    'kredit_det.id_kredit_faktur',
                    'kredit_faktur.id_kredit_faktur'
                );
        })
            ->where('id_kredit', $id_kredit)
            ->get();

        $data = [];
        foreach ($kredit as $key => $value) {
            $count = $key + 1;
            $data[] =
                '<tr>
						<td>' .
                $count .
                '</td>
						<td>' .
                $value->nomor_faktur .
                '</td>
						<td>' .
                human_date($value->tanggal_faktur) .
                '</td>
						<td><button class="btn btn-info kredit-faktur-btn" id-kredit-faktur="' .
                $value->id_kredit_faktur .
                '">Lihat Hutang</button></td>
						</tr>';
        }
        return response()->json($data);
    }

    public function getDetailKredit($id_faktur)
    {
        $kredit = KreditDetail::getData($id_faktur);
        $data = [];
        foreach ($kredit as $key => $value) {
            $count = $key + 1;
            if ($value->jenis_diskon == 'persen') {
                $diskon = $value->diskon . '%';
                $diskon_urai = get_discount(
                    $value->hja_upds * $value->banyak_obat,
                    $value->diskon
                );
            } elseif ($value->jenis_diskon == 'rupiah') {
                $diskon = format_rupiah($value->diskon);
                $diskon_urai = $diskon;
            }
            $data[] =
                '<tr>
						<td>' .
                $count .
                '</td>
						<td>' .
                $value->nama_obat .
                '</td>
						<td>' .
                human_date($value->tanggal_jatuh_tempo) .
                '</td>
						<td>' .
                $value->banyak_obat .
                ' ' .
                ucwords($value->bentuk_satuan) .
                '</td>
						<td>' .
                $diskon .
                '</td>
						<td>' .
                format_rupiah($diskon_urai) .
                '</td>
						<td>' .
                format_rupiah($value->sub_total) .
                '</td>
						<td><input type="checkbox" name="val_kredit[]" value="' .
                $value->id_kredit_det .
                '" target-id-kredit="' .
                $value->id_kredit .
                '"></td>
						</tr>';
        }
        return response()->json($data);
    }

    public function bayarKredit(Request $request)
    {
        $kredit_det = $request->val_kredit;
        $total = 0;
        $uuid = (string) Str::uuid();
        $nama_pelanggan = '';
        for ($i = 0; $i < count($kredit_det); $i++) {
            $get = KreditDetail::join(
                'obat',
                'kredit_det.id_obat',
                '=',
                'obat.id_obat'
            )
                ->where('id_kredit_det', $kredit_det[$i])
                ->firstOrFail();
            $diskon_urai = ($get->hja_upds * $get->diskon) / 100;

            $nama_pelanggan = KreditDetail::join(
                'kredit_faktur',
                'kredit_det.id_kredit_faktur',
                '=',
                'kredit_faktur.id_kredit_faktur'
            )
                ->join(
                    'kredit',
                    'kredit_faktur.id_kredit',
                    '=',
                    'kredit.id_kredit'
                )
                ->where('id_kredit_det', $kredit_det[$i])
                ->firstOrFail()->nama_pelanggan;

            $table[] =
                '<tr>
						<td>' .
                $get->nama_obat .
                '</td>
						<td>' .
                $get->banyak_obat .
                '</td>
						<td>' .
                format_rupiah($get->hja_upds) .
                '</td>
						<td>' .
                format_rupiah($diskon_urai) .
                '</td>
						<td>' .
                format_rupiah($get->sub_total) .
                '</td>
						<td><button class="btn btn-danger delete" data-id="' .
                $uuid .
                '">X</button></td>
					 </tr>';
            $input_hidden[] =
                '<div target-id="' .
                $uuid .
                '">
							<input type="hidden" name="obat_trx[]" value="' .
                $get->id_obat .
                '">
							<input type="hidden" name="supplier_trx[]" value="' .
                $get->id_supplier .
                '">
							<input type="hidden" name="diskon_trx[]" value="' .
                $get->diskon .
                '">
							<input type="hidden" name="diskon_urai[]" value="' .
                $diskon_urai .
                '">
							<input type="hidden" name="pcs_trx[]" value="' .
                $get->banyak_obat .
                '">
							<input type="hidden" name="harga_trx[]" value="' .
                $get->sub_total .
                '">
							<input type="hidden" name="harga_satuan[]" value="' .
                $get->harga_jual_obat .
                '">
							<input type="hidden" name="jen_hrg_trx[]" value="' .
                $get->jenis_harga .
                '">
							<input type="hidden" name="jenis_diskon[]" value="' .
                $get->jenis_diskon .
                '">
							<input type="hidden" name="data_kredit[]" value="' .
                $get->id_kredit_det .
                '">
							<input type="hidden" name="type_trx[]" value="bayar-kredit">
						</div>';
            $total += $get->sub_total;
        }
        $array = [
            'nama_pelanggan' => $nama_pelanggan,
            'table' => $table,
            'input_hidden' => $input_hidden,
            'total_harga' => $total,
        ];
        return response()->json($array);
    }

    public function bayarSemuaKredit($id)
    {
        $total = 0;
        $uuid = (string) Str::uuid();
        // for ($i=0; $i < count($kredit_det); $i++) {
        $nama_pelanggan = KreditFaktur::join(
            'kredit',
            'kredit_faktur.id_kredit',
            '=',
            'kredit.id_kredit'
        )
            ->where('id_kredit_faktur', $id)
            ->firstOrFail()->nama_pelanggan;

        $get = KreditDetail::join(
            'kredit_faktur',
            'kredit_det.id_kredit_faktur',
            '=',
            'kredit_faktur.id_kredit_faktur'
        )
            ->join('obat', 'kredit_det.id_obat', '=', 'obat.id_obat')
            ->where('kredit_det.id_kredit_faktur', $id)
            ->get();

        foreach ($get as $key => $value) {
            $diskon_urai = ($value->hja_upds * $value->diskon) / 100;

            $table[] =
                '<tr>
						<td>' .
                $value->nama_obat .
                '</td>
						<td>' .
                $value->banyak_obat .
                '</td>
						<td>' .
                format_rupiah($value->hja_upds) .
                '</td>
						<td>' .
                format_rupiah($diskon_urai) .
                '</td>
						<td>' .
                format_rupiah($value->sub_total) .
                '</td>
						<td><button class="btn btn-danger delete" data-id="' .
                $uuid .
                '">X</button></td>
					 </tr>';
            $input_hidden[] =
                '<div target-id="' .
                $uuid .
                '">
							<input type="hidden" name="obat_trx[]" value="' .
                $value->id_obat .
                '">
							<input type="hidden" name="supplier_trx[]" value="' .
                $value->id_supplier .
                '">
							<input type="hidden" name="diskon_trx[]" value="' .
                $value->diskon .
                '">
							<input type="hidden" name="diskon_urai[]" value="' .
                $diskon_urai .
                '">
							<input type="hidden" name="pcs_trx[]" value="' .
                $value->banyak_obat .
                '">
							<input type="hidden" name="harga_trx[]" value="' .
                $value->sub_total .
                '">
							<input type="hidden" name="harga_satuan[]" value="' .
                $value->harga_jual_obat .
                '">
							<input type="hidden" name="jen_hrg_trx[]" value="' .
                $value->jenis_harga .
                '">
							<input type="hidden" name="jenis_diskon[]" value="' .
                $value->jenis_diskon .
                '">
							<input type="hidden" name="data_kredit[]" value="' .
                $value->id_kredit_det .
                '">
							<input type="hidden" name="type_trx[]" value="bayar-kredit">
						</div>';
            $total += $value->sub_total;
        }
        // }
        $array = [
            'nama_pelanggan' => $nama_pelanggan,
            'table' => $table,
            'input_hidden' => $input_hidden,
            'total_harga' => $total,
        ];
        return response()->json($array);
    }

    public function getKodePembelian($jenis_beli)
    {
        // if ($jenis_beli == 'konsinyasi') {
        // 	return generateCodeTrx($jenis_beli);
        // }
        // else {
        return generateCodeTrx('beli-' . $jenis_beli);
        // }
    }

    public function getNomorTransaksi(Request $request)
    {
        $tanggal_transaksi = reverse_date($request->tanggal_transaksi);
        $get_upds = TransaksiKasir::where(
            'tanggal_transaksi',
            $tanggal_transaksi
        )->get();
        $get_resep = TransaksiRacikObat::where(
            'tanggal_transaksi',
            $tanggal_transaksi
        )->get();
        $get_kredit = KreditFaktur::where(
            'tanggal_faktur',
            $tanggal_transaksi
        )->get();
        $nomor_transaksi[] =
            '<option value="" selected disabled>=== Pilih Nomor Transaksi === </option>';

        foreach ($get_upds as $key => $value) {
            $nomor_transaksi[] =
                '<option value="' .
                $value->kode_transaksi .
                '|upds">' .
                $value->kode_transaksi .
                '</option>';
        }

        foreach ($get_resep as $key => $value) {
            $nomor_transaksi[] =
                '<option value="' .
                $value->kode_transaksi .
                '|resep">' .
                $value->kode_transaksi .
                '</option>';
        }

        foreach ($get_kredit as $key => $value) {
            $nomor_transaksi[] =
                '<option value="' .
                $value->nomor_faktur .
                '|kredit">' .
                $value->nomor_faktur .
                '</option>';
        }

        return response()->json($nomor_transaksi);
    }

    public function getObatByTransaksi(Request $request)
    {
        $kode_transaksi = $request->kode_transaksi;
        $explode_kode_transaksi = explode('|', $kode_transaksi);

        if ($explode_kode_transaksi[1] == 'upds') {
            $upds = TransaksiKasirDetail::join(
                'obat',
                'transaksi_kasir_det.id_obat',
                '=',
                'obat.id_obat'
            )
                ->join(
                    'transaksi_kasir',
                    'transaksi_kasir_det.id_transaksi',
                    '=',
                    'transaksi_kasir.id_transaksi'
                )
                ->where('kode_transaksi', $explode_kode_transaksi[0])
                ->get();

            foreach ($upds as $key => $value) {
                $no = $key + 1;
                // $data[] = '<div class="col-md-4">
                // 				<div class="form-group">
                // 					<label for="">Obat</label>
                // 					<input type="text" class="form-control" value="'.$value->nama_obat.'">
                // 					<input type="hidden" name="obat[]" value="'.$value->id_obat.'">
                // 				</div>
                // 			</div>
                // 			<div class="col-md-4">
                // 				<div class="form-group">
                // 					<label for="">Stok Transaksi</label>
                // 					<input type="number" class="form-control" name="stok_transaksi[]" value="'.$value->jumlah.'" readonly>
                // 				</div>
                // 			</div>
                // 			<div class="col-md-4">
                // 				<div class="form-group">
                // 					<label for="">Stok Retur</label>
                // 					<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" required="required">
                // 				</div>
                // 			</div>';
                $data[] =
                    '<tr>
								<td>
									' .
                    $no .
                    '
								</td>
								<td>
									' .
                    $value->nama_obat .
                    '
									<input type="hidden" name="obat[]" value="' .
                    $value->id_obat .
                    '">
								</td>
								<td>
									<input type="number" class="form-control" name="stok_transaksi[]" value="' .
                    $value->jumlah .
                    '" readonly>
								</td>
								<td>
									<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" id-stok-retur="' .
                    $no .
                    '" required="required">
								</td>
								<td>
									<input type="text" class="form-control" id-harga-terbilang="' .
                    $no .
                    '" readonly>
									<input type="hidden" value="' .
                    $value->hja_upds .
                    '" id-harga-satuan="' .
                    $no .
                    '" hja-obat="upds">
									<input type="hidden" name="harga_retur[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<input type="number" class="form-control harga-fleksibel" id-harga-fleksibel-input="' .
                    $no .
                    '">
									<input type="hidden" name="harga_fleksibel[]" id-harga-fleksibel="' .
                    $no .
                    '">
									<input type="hidden" name="harga_retur_backup[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<button type="button" class="btn btn-danger delete-retur" id-delete="' .
                    $no .
                    '">X</button>
								</td>
							</tr>
							<tr>
								<td colspan="6" align="right">
									<label id-harga-fleksibel-label="' .
                    $no .
                    '">Rp 0,00</label>
								</td>
							</tr>';
            }
        } elseif ($explode_kode_transaksi[1] == 'resep') {
            $resep = RacikObatDetail::join(
                'racik_obat',
                'racik_obat_detail.id_racik_obat',
                '=',
                'racik_obat.id_racik_obat'
            )
                ->join(
                    'racik_obat_data',
                    'racik_obat.id_racik_obat_data',
                    '=',
                    'racik_obat_data.id_racik_obat_data'
                )
                ->join(
                    'transaksi_racik_obat',
                    'transaksi_racik_obat.id_racik_obat_data',
                    '=',
                    'racik_obat_data.id_racik_obat_data'
                )
                ->join('obat', 'racik_obat_detail.id_obat', '=', 'obat.id_obat')
                ->where('kode_transaksi', $explode_kode_transaksi[0])
                ->get();
            foreach ($resep as $key => $value) {
                $no = $key + 1;
                // $data[] = '<div class="col-md-4">
                // 	<div class="form-group">
                // 		<label for="">Obat</label>
                // 		<input type="text" class="form-control" value="'.$value->nama_obat.'">
                // 		<input type="hidden" name="obat[]" value="'.$value->id_obat.'">
                // 	</div>
                // </div>
                // <div class="col-md-4">
                // 	<div class="form-group">
                // 		<label for="">Stok Transaksi</label>
                // 		<input type="number" class="form-control" name="stok_transaksi[]" value="'.$value->jumlah.'" readonly>
                // 	</div>
                // </div>
                // <div class="col-md-4">
                // 	<div class="form-group">
                // 		<label for="">Stok Retur</label>
                // 		<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" required="required">
                // 	</div>
                // </div>';
                $data[] =
                    '<tr>
								<td>
									' .
                    $no .
                    '
								</td>
								<td>
									' .
                    $value->nama_obat .
                    '
									<input type="hidden" name="obat[]" value="' .
                    $value->id_obat .
                    '">
								</td>
								<td>
									<input type="number" class="form-control" name="stok_transaksi[]" value="' .
                    $value->jumlah .
                    '" readonly>
								</td>
								<td>
									<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" required="required" id-stok-retur="' .
                    $no .
                    '">
								</td>
								<td>
									<input type="text" class="form-control" id-harga-terbilang="' .
                    $no .
                    '" readonly>
									<input type="hidden" value="' .
                    $value->hja_resep .
                    '" id-harga-satuan="' .
                    $no .
                    '" hja-obat="resep">
									<input type="hidden" name="harga_retur[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<input type="number" class="form-control harga-fleksibel" id-harga-fleksibel-input="' .
                    $no .
                    '">
									<input type="hidden" name="harga_fleksibel[]" id-harga-fleksibel="' .
                    $no .
                    '">
									<input type="hidden" name="harga_retur_backup[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<label id-harga-fleksibel-label="' .
                    $no .
                    '">Rp 0,00</label>
								</td>
								<td>
									<button type="button" class="btn btn-danger delete-retur" id-delete="' .
                    $no .
                    '">X</button>
								</td>';
            }
        } elseif ($explode_kode_transaksi[1] == 'kredit') {
            $kredit = KreditFaktur::join(
                'kredit_det',
                'kredit_faktur.id_kredit_faktur',
                '=',
                'kredit_det.id_kredit_faktur'
            )
                ->join('obat', 'kredit_det.id_obat', '=', 'obat.id_obat')
                ->where('nomor_faktur', $explode_kode_transaksi[0])
                ->get();
            foreach ($kredit as $key => $value) {
                $no = $key + 1;
                if (is_float($value->sub_total)) {
                    $hja = $value->hja_relasi;
                    $hja_obat = 'relasi';
                } else {
                    $hja = $value->hja_upds;
                    $hja_obat = 'upds';
                }
                $data[] =
                    '<tr>
								<td>
									' .
                    $no .
                    '
								</td>
								<td>
									' .
                    $value->nama_obat .
                    '
									<input type="hidden" name="obat[]" value="' .
                    $value->id_obat .
                    '">
								</td>
								<td>
									<input type="number" class="form-control" name="stok_transaksi[]" value="' .
                    $value->banyak_obat .
                    '" readonly>
								</td>
								<td>
									<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" required="required" id-stok-retur="' .
                    $no .
                    '">
								</td>
								<td>
									<input type="text" class="form-control" id-harga-terbilang="' .
                    $no .
                    '" readonly>
									<input type="hidden" value="' .
                    $hja .
                    '" id-harga-satuan="' .
                    $no .
                    '" hja-obat="' .
                    $hja_obat .
                    '">
									<input type="hidden" name="harga_retur[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<input type="number" class="form-control harga-fleksibel" id-harga-fleksibel-input="' .
                    $no .
                    '">
									<input type="hidden" name="harga_fleksibel[]" id-harga-fleksibel="' .
                    $no .
                    '">
									<input type="hidden" name="harga_retur_backup[]" id-harga="' .
                    $no .
                    '">
								</td>
								<td>
									<label id-harga-fleksibel-label="' .
                    $no .
                    '">Rp 0,00</label>
								</td>
								<td>
									<button type="button" class="btn btn-danger delete-retur" id-delete="' .
                    $no .
                    '">X</button>
								</td>';
            }
        }

        return response()->json($data);
    }

    public function getSingkatanSupplier(Request $request)
    {
        $supplier = Supplier::where(
            'id_supplier',
            $request->id_supplier
        )->firstOrFail();

        return response()->json($supplier->singkatan_supplier);
    }

    public function getInfoPasienResep(Request $request)
    {
        $pasien = Pasien::where(
            'id_pasien',
            $request->id_pasien
        )->firstOrFail();

        return response()->json([
            'alamat_pasien' => $pasien->alamat_pasien,
            'nomor_telepon_pasien' => $pasien->nomor_telepon_pasien,
        ]);
    }
    // END AJAX PROSES //
}
