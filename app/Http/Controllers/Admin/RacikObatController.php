<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ObatModel as Obat;
use App\Models\PasienModel as Pasien;
use App\Models\DokterModel as Dokter;
use App\Models\RacikObatModel as RacikObat;
use App\Models\RacikObatDetailModel as RacikObatDetail;
use App\Models\RacikObatDataModel as RacikObatData;
use App\Models\RacikObatSementaraModel as RacikObatSementara;
use App\Models\RacikObatSementaraDetailModel as RacikObatSementaraDetail;
use App\Models\TransaksiRacikObatModel as TransaksiRacikObat;
use App\Models\PemakaianModel as Pemakaian;
use App\Models\JamShiftModel as JamShift;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use App\Models\KartuStokModel as KartuStok;

class RacikObatController extends Controller
{
    public function dataTransaksiRacik()
    {
        $title = 'Data Transaksi Racik Obat';
        $link  = 'penjualan';
        $page  = 'data-penjualan-racik-obat';

        return view('Admin.transaksi.transaksi-racik-obat',compact('title','link','page'));
    }

    public function dataRacikObat($id)
    {
        $title = 'Data Racik Obat';
        $link  = 'penjualan';
        $page  = 'data-penjualan-racik-obat';
        $nomor_transaksi = TransaksiRacikObat::where('id_racik_obat_data',$id)->firstOrFail()->kode_transaksi;

        return view('Admin.racik-obat.data-racik-obat',compact('title','link','page','id','nomor_transaksi'));
    }

    public function dataRacikObatDetail($id,$id_detail)
    {
        $title = 'Data Racik Obat Detail';
        $link  = 'penjualan';
        $page  = 'data-penjualan-racik-obat';
        $nomor_transaksi = TransaksiRacikObat::where('id_racik_obat_data',$id)->firstOrFail()->kode_transaksi;

        return view('Admin.racik-obat.data-racik-obat-detail',compact('title','link','page','id','id_detail','nomor_transaksi'));
    }

    public function deleteTransaksiRacikObat($id)
    {
        $transaksi = TransaksiRacikObat::where('id_transaksi_racik_obat',$id);
        RacikObatData::where('id_racik_obat_data',$transaksi->firstOrFail()->id_racik_obat_data)->delete();

        return redirect('/admin/data-penjualan-racik-obat')->with('message','Berhasil Delete Data');
    }

    public function racikObat()
    {
        // session()->forget('racikan_obat');
        // session()->put('racikan_obat',['counter' => 0, 'data_racik' => [], 'total_semua' => 0]);

        $obat   = Obat::where('status_delete',0)->get();
        $pasien = Pasien::where('status_delete',0)->get();
        $dokter = Dokter::where('status_delete',0)->get();
        return view('Admin.racik-obat',compact('obat','pasien','dokter'));
    }

    public function bayarRacikObat(Request $request)
    {
        $kode_transaksi   = generateCodeTrx('resep');
        $tanggal_sekarang = date('Y-m-d');
        $jam_sekarang     = date('H:i:s');
        $bayar            = $request->bayar;
        $kembalian        = $request->kembalian;
        $kode_racik       = $request->kode_racik;

        $pasien        = $request->pasien;
        $dokter        = $request->dokter;
        $nama_pasien   = $request->nama_pasien;
        $nomor_telepon = $request->nomor_telepon_pasien;
        $alamat        = $request->alamat_pasien;
        $nama_dokter   = $request->nama_dokter;
        $id_pasien     = '';
        $id_dokter     = '';

        // $get_racik         = session()->get('racikan_obat')['data_racik'];
        $total_semua_racik = $request->total_semua_racik;
        $grand_total       = $request->total_racik;
        $diskon            = $request->diskon_resep;

        if ($pasien != null) {
            $id_pasien = $pasien;
        }
        else if($nama_pasien != null) {
            $data_pasien = [
                'nama_pasien'          => $nama_pasien,
                'nomor_telepon_pasien' => $nomor_telepon,
                'alamat_pasien'        => $alamat,
                'status_delete'        => 0
            ];
			
            $check = Pasien::where('nama_pasien',$nama_pasien)
                            ->where('nomor_telepon_pasien',$nomor_telepon)
                            ->where('alamat_pasien',$alamat)
                            ->where('status_delete',0)
                            ->count();
            if ($check == 0) {
                $id_pasien = Pasien::insertGetId($data_pasien);
            }
            else {
                $id_pasien = Pasien::where('nama_pasien',$nama_pasien)
                                ->where('nomor_telepon_pasien',$nomor_telepon)
                                ->where('alamat_pasien',$alamat)
                                ->where('status_delete',0)
                                ->firstOrFail()->id_pasien;
            }

            //$id_pasien = Pasien::insertGetId($data_pasien);
        }

        if ($dokter != null) {
            $id_dokter = $dokter;
        }
        else if($nama_dokter != null) {
            $data_dokter = [
                'nama_dokter'   => $nama_dokter,
                'status_delete' => 0
            ];
			
            $check = Dokter::where('nama_dokter',$nama_dokter)
                            ->where('status_delete',0)
                            ->count();
            if ($check == 0) {
                $id_dokter = Dokter::insertGetId($data_dokter);
            }
            else {
                $id_dokter = Dokter::where('nama_dokter',$nama_dokter)
                                ->where('status_delete',0)
                                ->firstOrFail()->id_dokter;
            }

            //$id_dokter = Dokter::insertGetId($data_dokter);
        }

        $data_utama_racik = [
            'tanggal_racik'  => $tanggal_sekarang,
            'id_pasien'      => $id_pasien,
            'id_dokter'      => $id_dokter,
            'total_semua'    => $total_semua_racik
        ];

        $id_racik_data = RacikObatData::insertGetId($data_utama_racik);

        $get_racik = RacikObatSementara::where('kode_racik',$kode_racik)->get();

        foreach ($get_racik as $key => $value) {
            $data_racik = [
                'id_racik_obat_data' => $id_racik_data,
                'nama_racik'         => $value->nama_racik,
                'jenis_racik'        => $value->jenis_racik,
                'jumlah_racik'       => $value->jumlah_racik,
                'ongkos_racik'       => $value->ongkos_racik,
                'harga_total_racik'  => $value->total_racik,
                'keterangan_racik'   => $value->keterangan_racik
            ];

            $id_insert_racik = RacikObat::insertGetId($data_racik);

            $get_racik_detail = RacikObatSementaraDetail::where('id_racik_obat_sementara',$value->id_racik_obat_sementara)
                                                        ->get();
            // dd($get_racik_detail);

            foreach ($get_racik_detail as $index => $data) {
                $data_detail_racik = [
                    'id_racik_obat' => $id_insert_racik,
                    'id_obat'       => $data->id_obat,
                    'id_supplier'   => $data->id_supplier,
                    'jumlah'        => $data->jumlah,
                    'embalase'      => $data->embalase,
                    'sub_total'     => $data->sub_total
                ];

                RacikObatDetail::create($data_detail_racik);

                $data_pemakaian = [
                    'tanggal_pemakaian' => date('Y-m-d'),
					'nomor_transaksi'	=> $kode_transaksi,
                    'id_dokter'         => $id_dokter,
                    'id_supplier'       => $data->id_supplier,
                    'id_obat'           => $data->id_obat,
                    'stok_pakai'        => $data->jumlah,
                    'ket_data'          => 'dokter',
                ];

                Pemakaian::create($data_pemakaian);

                $obat = Obat::where('id_obat',$data['id_obat'])->firstOrFail();
                $data_kartu_stok = [
                    'tanggal_pakai' => $tanggal_sekarang,
                    'nomor_stok'    => $kode_transaksi,
                    'layanan'       => 'Resep',
                    'id_obat'       => $data->id_obat,
                    'retur_barang'  => 0,
                    'beli'          => 0,
                    'jual'          => $data->jumlah,
                    'saldo'         => $obat->stok_obat,
                    'keterangan'    => 'Penjualan'
                ];

                KartuStok::create($data_kartu_stok);
            }
        }
        

        $id_jam_shift = JamShift::getIdJamShift();
        $data_transaksi_racik = [
            'kode_transaksi'     => $kode_transaksi,
            'tanggal_transaksi'  => $tanggal_sekarang,
            'id_racik_obat_data' => $id_racik_data,
            'diskon'             => $diskon,
            'harga_total'        => $grand_total,
            'bayar'              => $bayar,
            'kembalian'          => $kembalian,
            'id_jam_shift'       => $id_jam_shift,
            'id_users'           => auth()->id()
        ];

        TransaksiRacikObat::create($data_transaksi_racik);

        RacikObatSementara::where('kode_racik',$kode_racik)->delete();

        $profile_instansi = ProfileInstansi::firstOrFail();
        
        $transaksi_racik = TransaksiRacikObat::join('users','transaksi_racik_obat.id_users','=','users.id_users')->where('kode_transaksi',$kode_transaksi)->firstOrFail();

        $sum_total = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('transaksi_racik_obat','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->where('kode_transaksi',$kode_transaksi)
                            ->sum('harga_total_racik');

        $get_pasien = Pasien::where('id_pasien',$id_pasien)->firstOrFail();
        $get_dokter = Dokter::where('id_dokter',$id_dokter)->firstOrFail();

        $sum_total_racik = TransaksiRacikObat::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->where('kode_transaksi',$kode_transaksi)
                                            ->sum('total_semua');

        $get_obat_non_resep = RacikObatDetail::join('racik_obat','racik_obat_detail.id_racik_obat','=','racik_obat.id_racik_obat')
                                            ->join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->join('obat','racik_obat_detail.id_obat','=','obat.id_obat')
                                            ->where('jenis_racik','-')
                                            ->where('racik_obat_data.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                            ->get();

        $get_resep_total = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                    ->whereNotIn('jenis_racik',['-'])
                                    ->where('racik_obat.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                    ->get();

        $get_obat_resep = RacikObatDetail::join('racik_obat','racik_obat_detail.id_racik_obat','=','racik_obat.id_racik_obat')
                                            ->join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->join('obat','racik_obat_detail.id_obat','=','obat.id_obat')
                                            ->whereNotIn('jenis_racik',['-'])
                                            ->where('racik_obat_data.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                            ->get();

        return view('Admin.racik-obat-print',compact('kode_transaksi','diskon','tanggal_sekarang','jam_sekarang','total_semua_racik','grand_total','bayar','kembalian','get_obat_non_resep','get_resep_total','get_obat_resep','profile_instansi','sum_total','get_pasien','get_dokter','sum_total_racik'));
    }

    public function cetakInvoice($id) 
    {
        $profile_instansi = ProfileInstansi::firstOrFail();
        
        $transaksi_racik = TransaksiRacikObat::join('users','transaksi_racik_obat.id_users','=','users.id_users')->where('id_transaksi_racik_obat',$id)->firstOrFail();

        $sum_total = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('transaksi_racik_obat','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->where('kode_transaksi',$transaksi_racik->kode_transaksi)
                            ->sum('harga_total_racik');

        $sum_total_racik = TransaksiRacikObat::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->where('kode_transaksi',$transaksi_racik->kode_transaksi)
                                            ->sum('total_semua');

        // $get_racik = TransaksiRacikObat::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')->join('racik_obat','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')->where('id_transaksi_racik_obat',$id)->get();

        // $get_racik_detail = new RacikObatDetail;

        $get_pasien = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('transaksi_racik_obat','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('pasien','racik_obat_data.id_pasien','=','pasien.id_pasien')
                            ->where('kode_transaksi',$transaksi_racik->kode_transaksi)
                            ->firstOrFail();

        $get_dokter = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('transaksi_racik_obat','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                            ->join('dokter','racik_obat_data.id_dokter','=','dokter.id_dokter')
                            ->where('kode_transaksi',$transaksi_racik->kode_transaksi)
                            ->firstOrFail();

        $get_obat_non_resep = RacikObatDetail::join('racik_obat','racik_obat_detail.id_racik_obat','=','racik_obat.id_racik_obat')
                                            ->join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->join('obat','racik_obat_detail.id_obat','=','obat.id_obat')
                                            ->where('jenis_racik','-')
                                            ->where('racik_obat_data.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                            ->get();

        $get_resep_total = RacikObat::join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                    ->whereNotIn('jenis_racik',['-'])
                                    ->where('racik_obat.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                    ->get();

        $get_obat_resep = RacikObatDetail::join('racik_obat','racik_obat_detail.id_racik_obat','=','racik_obat.id_racik_obat')
                                            ->join('racik_obat_data','racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                            ->join('obat','racik_obat_detail.id_obat','=','obat.id_obat')
                                            ->whereNotIn('jenis_racik',['-'])
                                            ->where('racik_obat_data.id_racik_obat_data',$transaksi_racik->id_racik_obat_data)
                                            ->get();

        return view('Admin.racik-obat-cetak',compact('transaksi_racik','profile_instansi','sum_total','sum_total_racik','get_pasien','get_dokter','get_obat_non_resep','get_resep_total','get_obat_resep'));
    }
}