<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PembelianObatModel as PembelianObat;
use App\Models\PembelianDetailModel as PembelianDetail;
use App\Models\SupplierModel as Supplier;
use App\Models\ObatModel as Obat;
use App\Models\JenisObatModel as JenisObat;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use App\Models\KartuStokModel as KartuStok;
use App\Models\MarginObatModel as MarginObat;
use App\Models\PersenPpnModel as PersenPpn;

class PembelianController extends Controller
{
    public function index()
    {
        $title = 'Data Pembelian';
        $link  = 'pembelian';
        $page  = 'data-pembelian';
        return view('Admin.pembelian.main',compact('title','link','page'));
    }

    public function tambah() 
    {
        session()->forget('beli_obat');
        session()->put('beli_obat',['data_beli' => [], 'total_semua' => 0,  'dpp' => 0, 'ppn' => 0, 'discount' => 0]);

        $title     = 'Form Pembelian | Admin';
        $page      = 'data-pembelian';
        $link      = 'pembelian';
        // $kode_beli = generateCode('OBA','-',PembelianObat::lastNumCode(),11);
        $supplier  = Supplier::where('status_delete',0)->get();
        $obat      = Obat::where('status_delete',0)->get();
        return view('Admin.pembelian.form-pembelian',compact('title','link','page','supplier','obat'));
    }

    public function delete($id) 
    {
        PembelianObat::where('id_pembelian_obat',$id)->delete();
        return redirect('/admin/data-pembelian')->with('message','Berhasil Hapus Data Pembelian');
    }

    public function save(Request $request) 
    {   
        $nomor_faktur        = $request->nomor_faktur;
        $jenis_beli          = $request->jenis_beli;
        $supplier            = $request->supplier;
        $tanggal_terima      = reverse_date($request->tanggal_terima);
        $tanggal_jatuh_tempo = reverse_date($request->tanggal_jatuh_tempo);
        $waktu_hutang        = $request->waktu_hutang;
        $kode_pembelian      = generateCodeTrx('beli-'.$jenis_beli);
        $session_beli_obat   = session()->get('beli_obat');
        $tanggal_input       = date('Y-m-d');

        $get_beli    = $session_beli_obat['data_beli'];
        $total_semua = '';

        $data_beli = [
            'kode_pembelian'      => $kode_pembelian,
            'nomor_faktur'        => $nomor_faktur,
            'id_supplier'         => $supplier,
            'jenis_beli'          => $jenis_beli,
            'tanggal_terima'      => $tanggal_terima,
            'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo == '' ? NULL : $tanggal_jatuh_tempo,
            'waktu_hutang'        => $waktu_hutang,
            'total_dpp'           => $session_beli_obat['dpp'],
            'total_ppn'           => $session_beli_obat['ppn'],
            'total_semua'         => $session_beli_obat['total_semua'],
            'tanggal_input'       => $tanggal_input,
            'id_users'            => auth()->id()
        ];

        $id_pembelian_obat = PembelianObat::insertGetId($data_beli);
        
        $margin_obat = MarginObat::firstOrFail();

        foreach ($get_beli as $key => $value) {
            $data_beli_detail = [
                'id_pembelian_obat' => $id_pembelian_obat,
                'id_obat'           => $value['id_obat'],
                'jumlah'            => $value['jumlah'],
                'harga_obat'        => $value['harga_obat'],
                'disc_1'            => $value['disc_1'],
                'disc_2'            => $value['disc_2'],
                'disc_3'            => $value['disc_3'],
                'sub_total'         => $value['sub_total']
            ];

            $obat = Obat::where('id_obat',$value['id_obat'])->firstOrFail();
            $ppn  = PersenPpn::firstOrFail()->ppn;
            if ($value['jenis_ppn'] == 'include-ppn') {
                if ($value['harga_obat'] > $obat->harga_modal_ppn) {
                    $harga_modal_ppn = $value['harga_obat'];
                    if ($obat->kunci_hja_upds != 1) {
                        $hja_upds        = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_upds) / 100);
                    }
                    if ($obat->kunci_hja_resep != 1) {
                        $hja_resep       = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_resep) / 100);
                    }
                    if ($obat->kunci_hja_relasi != 1) {
                        $hja_relasi      = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_relasi) / 100);
                    }
                    Obat::where('id_obat',$value['id_obat'])
                        ->update([
                            'harga_modal'     => $value['harga_obat'],
                            'harga_modal_ppn' => $value['harga_obat'],
                            'hja_upds'        => $hja_upds,
                            'hja_resep'       => $hja_resep,
                            'hja_relasi'      => $hja_relasi
                        ]);
                }
            }
            else {
                if ($value['harga_obat'] > $obat->harga_modal) {
                    $harga_modal_ppn = $value['harga_obat'] + (($value['harga_obat'] * $ppn) / 100);
                    if ($obat->kunci_hja_upds != 1) {
                        $hja_upds        = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_upds) / 100);
                    }
                    if ($obat->kunci_hja_resep != 1) {
                        $hja_resep       = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_resep) / 100);
                    }
                    if ($obat->kunci_hja_relasi != 1) {
                        $hja_relasi      = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_relasi) / 100);
                    }
                    
                    Obat::where('id_obat',$value['id_obat'])
                        ->update([
                            'harga_modal'     => $value['harga_obat'],
                            'harga_modal_ppn' => $harga_modal_ppn,
                            'hja_upds'        => $hja_upds,
                            'hja_resep'       => $hja_resep,
                            'hja_relasi'      => $hja_relasi
                        ]);
                }
            }

            PembelianDetail::create($data_beli_detail);

            $data_kartu_stok[] = [
                'tanggal_pakai' => $tanggal_terima,
                'nomor_stok'    => $kode_pembelian,
                'layanan'       => 'Beli',
                'id_obat'       => $value['id_obat'],
                'beli'          => $value['jumlah'],
                'jual'          => 0,
                'retur_barang'  => 0,
                'saldo'         => $obat->stok_obat+$value['jumlah'],
                'keterangan'    => 'Pembelian'
            ];
        }

        $message = 'Berhasil Input Data Pembelian';
        KartuStok::insert($data_kartu_stok);

        $pembelian = PembelianObat::join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                                ->where('kode_pembelian',$kode_pembelian)
                                ->firstOrFail();

        $pembelian_detail = PembelianDetail::join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                                            ->where('id_pembelian_obat',$id_pembelian_obat)
                                            ->get();

        $profile_instansi = ProfileInstansi::firstOrFail();

        // $calc_dpp = PembelianDetail::where('id_pembelian_obat',$id_pembelian_obat)
        //                             ->sum('sub_total');

        // $calc_ppn = PembelianDetail::where('id_pembelian_obat',$id_pembelian_obat)
        //                             ->sum('ppn');

        return view('Admin.pembelian.pembelian-print',compact('pembelian','pembelian_detail','profile_instansi'));
    }

    public function cetak($id)
    {
        $pembelian = PembelianObat::join('users','pembelian_obat.id_users','=','users.id_users')
                                ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                                ->where('id_pembelian_obat',$id)
                                ->firstOrFail();

        $pembelian_detail = PembelianDetail::join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                                            ->where('id_pembelian_obat',$id)
                                            ->get();

        $profile_instansi = ProfileInstansi::firstOrFail();

        $calc_dpp = PembelianDetail::where('id_pembelian_obat',$id)
                                    ->sum('sub_total');

        return view('Admin.pembelian.pembelian-cetak',compact('pembelian','pembelian_detail','profile_instansi','calc_dpp'));
    }

    public function detail($id)
    {
        $title = 'Data Pembelian Detail';
        $page  = 'data-pembelian';
        return view('Admin.pembelian.main-detail',compact('title','page','id'));
    }

    public function deleteDetail($id,$id_detail) 
    {
        PembelianDetail::where('id_pembelian_detail',$id_detail)->delete();
        return redirect('/admin/data-pembelian/detail/'.$id)->with('message','Berhasil Hapus Data Pembelian');
    }

    public function historyBeli()
    {
        $title = 'History Beli';
        $link  = 'pembelian';
        $page  = 'history-beli';
        $obat  = Obat::where('status_delete',0)->get();

        return view('Admin.history-beli.main',compact('title','link','page','obat'));
    }

    public function kartuStok()
    {
        $title = 'Kartu Stok';
        $link  = 'pembelian';
        $page  = 'kartu-stok';
        $obat  = Obat::where('status_delete',0)->get();

        return view('Admin.kartu-stok.main',compact('title','link','page','obat'));
    }

    public function kartuStokCetak(Request $request)
    {
        $tanggal_dari   = reverse_date($request->tanggal_dari);
        $tanggal_sampai = reverse_date($request->tanggal_sampai);
        $obat_cari      = $request->obat_cari;

        $nama_obat = Obat::where('id_obat',$obat_cari)->firstOrFail()->nama_obat;

        $get_kartu_stok = KartuStok::whereBetween('tanggal_pakai',[$tanggal_dari,$tanggal_sampai])
                                        ->where('id_obat',$obat_cari)
                                        ->get();

        $sum_beli = KartuStok::whereBetween('tanggal_pakai',[$tanggal_dari,$tanggal_sampai])
                                        ->where('id_obat',$obat_cari)
                                        ->sum('beli');

        $sum_jual = KartuStok::whereBetween('tanggal_pakai',[$tanggal_dari,$tanggal_sampai])
                                        ->where('id_obat',$obat_cari)
                                        ->sum('jual');

        $sum_saldo = KartuStok::whereBetween('tanggal_pakai',[$tanggal_dari,$tanggal_sampai])
                                        ->where('id_obat',$obat_cari)
                                        ->sum('saldo');

        return view('Admin.kartu-stok.cetak-kartu-stok',compact('tanggal_dari','tanggal_sampai','nama_obat','get_kartu_stok','sum_beli','sum_jual','sum_saldo'));
    }
}
