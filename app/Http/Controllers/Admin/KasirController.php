<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JenisObatModel as JenisObat;
use App\Models\TransaksiKasirModel as TransaksiKasir;
use App\Models\TransaksiKasirDetailModel as TransaksiKasirDetail;
use App\Models\PengeluaranModel as Pengeluaran;
use App\Models\HargaObatModel as HargaObat;
use App\Models\KreditModel as Kredit;
use App\Models\KreditFakturModel as KreditFaktur;
use App\Models\KreditDetailModel as KreditDetail;
use App\Models\PemasukanModel as Pemasukan;
use App\Models\ObatModel as Obat;
use App\Models\RacikObatModel as RacikObat;
use App\Models\RacikObatDetailModel as RacikObatDetail;
use App\Models\TransaksiRacikObatModel as TransaksiRacikObat;
use App\Models\PasienModel as Pasien;
use App\Models\DokterModel as Dokter;
use App\Models\SupplierModel as Supplier;
use App\Models\PemakaianModel as Pemakaian;
use App\Models\JamShiftModel as JamShift;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use App\Models\KartuStokModel as KartuStok;
use Auth;

class KasirController extends Controller
{
	public function index() 
	{
		session()->forget('nomor_upds_relasi');
		$pelanggan  = Kredit::all();
		$supplier   = Supplier::where('status_delete',0)->get();
		$obat       = Obat::where('status_delete',0)->get();
		return view('Admin.kasir',compact('pelanggan','supplier','obat'));
	}

	public function bayar(Request $request) 
	{
		$obat           = $request->obat_trx;
		$pcs            = $request->pcs_trx;
		$supplier       = $request->supplier_trx;
		$harga_satuan   = $request->harga_satuan;
		$harga          = $request->harga_trx;
		$sub_total_obat = $request->sub_total_obat;
		$data_kredit    = $request->data_kredit;
		$jenis_diskon   = $request->jenis_diskon;
		$tipe_trx       = $request->type_trx;
		$diskon_obat    = $request->diskon_trx;
		$diskon_urai    = $request->diskon_urai;

		// $kode_transaksi      = generateCodeTrx('upds');
		$tanggal_sekarang    = date('Y-m-d');
		$bayar_tunai         = $request->bayar_tunai;
		$kredit              = $request->kredit;
		$total               = $request->total_harga;
		$bayar               = $request->bayar;
		$kembali             = $request->kembali;
		$jenis_kasir		 = $request->jenis_kasir;

		$tanggal_jatuh_tempo = reverse_date($request->tanggal_jatuh_tempo);
		$obat_mod            = new Obat;
		$profile_instansi    = ProfileInstansi::firstOrFail();

		if ($bayar_tunai) {
			if ($data_kredit != null) {
				for ($i=0; $i < count($data_kredit); $i++) {
					KreditDetail::where('id_kredit_det',$data_kredit[$i])->where('id_obat',$obat[$i])->update(['status_kredit'=>1]);
				}
			}
			$get_id_jam     = JamShift::getIdJamShift();
			$kode_transaksi = generateCodeTrx('upds');
			$transaksi_kasir = [
				'id_users'          => Auth::user()->id_users,
				'tanggal_transaksi' => $tanggal_sekarang,
				'jam_transaksi'		=> date('H:i:s'),
				'kode_transaksi'    => $kode_transaksi,
				'total'             => $total,
				'bayar'             => $bayar,
				'kembali'           => $kembali,
				'id_jam_shift'		=> $get_id_jam,
			];
			$id = TransaksiKasir::insertGetId($transaksi_kasir);
			
			for ($i=0; $i < count($obat); $i++) {
				$detail_trans = [
					'id_transaksi'   => $id,
					'id_obat'        => $obat[$i],
					'id_supplier'	 => $supplier[$i],
					'jumlah'         => $pcs[$i],
					'sub_total_obat' => $sub_total_obat[$i],
					'diskon'		 => $diskon_urai[$i] != null ? $diskon_urai[$i] : 0,
					'jenis_diskon'	 => 'rupiah',
					'sub_total'      => $harga[$i],
					'tipe_transaksi' => $tipe_trx[$i],
					'created_at'     => date('Y-m-d H:i:s'),
					'updated_at'     => date('Y-m-d H:i:s')
				];
				TransaksiKasirDetail::create($detail_trans);

				if ($data_kredit == null) {
					$data_pemakaian = [
						'tanggal_pemakaian' => $tanggal_sekarang,
						'nomor_transaksi'	=> $kode_transaksi,
						'id_supplier'       => $supplier[$i],
						'id_obat'           => $obat[$i],
						'stok_pakai'        => $pcs[$i],
						'ket_data'          => 'supplier'
					];

	                $obat_get = Obat::where('id_obat',$obat[$i])->firstOrFail();
	                $data_kartu_stok = [
	                    'tanggal_pakai' => $tanggal_sekarang,
	                    'nomor_stok'    => $kode_transaksi,
	                    'layanan'       => 'UPDS',
	                    'id_obat'       => $obat_get->id_obat,
	                    'beli'          => 0,
	                    'jual'          => $pcs[$i],
	                    'retur_barang'	=> 0,
	                    'saldo'         => $obat_get->stok_obat,
	                    'keterangan'    => 'Penjualan'
	                ];

	                KartuStok::create($data_kartu_stok);
					Pemakaian::create($data_pemakaian);
				}
			}

			$transaksi        = TransaksiKasir::join('users','transaksi_kasir.id_users','=','users.id_users')
												->where('id_transaksi',$id)->firstOrFail();
												
			$transaksi_detail = TransaksiKasirDetail::join('obat','transaksi_kasir_det.id_obat','=','obat.id_obat')
													->where('id_transaksi',$id)->get();

			$sum_diskon         = array_sum($diskon_urai);
			$sum_sub_total_obat = array_sum($sub_total_obat);
			session()->forget('nomor_upds_relasi');

			return view('Admin.cetak-bayar',compact('transaksi','sum_diskon','transaksi_detail','profile_instansi','sum_sub_total_obat'));
		}
		elseif ($kredit) {
			if ($request->pelanggan_input == null) {
				$pelanggan = Kredit::insertGetId(['nama_pelanggan'=>$request->nama_pelanggan_input,'alamat_pelanggan' => $request->alamat_pelanggan,'nomor_telepon'=>$request->nomor_telepon]);
			}
			else {
				$pelanggan = $request->pelanggan_input;
			}

			// $nomor_faktur   = generateCode('KRD','-',KreditFaktur::lastNumCode(),11);
			if($jenis_kasir == 'upds') {
				$nomor_faktur   = generateCodeTrx('upds-kredit');
			}
			else {
				$nomor_faktur   = generateCodeTrx($jenis_kasir);
			}
			$tanggal_faktur = date('Y-m-d');
			$get_id_jam     = JamShift::getIdJamShift();
			
			//dd($obat);
			
			$data_kredit_faktur = [
				'nomor_faktur'	 => $nomor_faktur,
				'id_kredit'      => $pelanggan,
				'tanggal_faktur' => $tanggal_faktur,
				'jam_transaksi'	 => date('H:i:s'),
				'id_jam_shift'	 => $get_id_jam,
				'id_users'       => auth()->id()
			];

			$id_kredit_faktur = KreditFaktur::insertGetId($data_kredit_faktur);

			foreach ($obat as $i => $value) {
				//dd($obat[180]);

				$data_kredit = [
					'id_kredit_faktur'    => $id_kredit_faktur,
					'id_obat'             => $obat[$i],
					'id_supplier'         => $supplier[$i],
					'banyak_obat'         => $pcs[$i],
					'diskon'              => $diskon_urai[$i] != null ? $diskon_urai[$i] : 0,
					'jenis_diskon'        => 'rupiah',
					'sub_total'           => $harga[$i],
					'tanggal_jatuh_tempo' => $tanggal_jatuh_tempo,
					'status_kredit'       => 0,
					'created_at'          => date('Y-m-d H:i:s'),
					'updated_at'          => date('Y-m-d H:i:s')
				];
				KreditDetail::create($data_kredit);
				
				$data_pemakaian = [
					'tanggal_pemakaian' => date('Y-m-d'),
					'nomor_transaksi'	=> $nomor_faktur,
					'id_supplier'       => $supplier[$i],
					'id_obat'           => $obat[$i],
					'stok_pakai'        => $pcs[$i],
					'ket_data'          => 'supplier'
				];

				Pemakaian::create($data_pemakaian);

                $obat_get = Obat::where('id_obat',$obat[$i])->firstOrFail();
                $data_kartu_stok = [
                    'tanggal_pakai' => $tanggal_sekarang,
                    'nomor_stok'    => $nomor_faktur,
                    'layanan'       => 'UPDS',
                    'id_obat'       => $obat_get->id_obat,
                    'beli'          => 0,
                    'jual'          => $pcs[$i],
                    'retur_barang'	=> 0,
                    'saldo'         => $obat_get->stok_obat,
                    'keterangan'    => 'Penjualan Kredit'
                ];

                KartuStok::create($data_kartu_stok);
			}
			
			$nama_pelanggan = Kredit::where('id_kredit',$pelanggan)->firstOrFail()->nama_pelanggan;
			$kredit_faktur  = KreditFaktur::where('nomor_faktur',$nomor_faktur)->firstOrFail();

			$data           = KreditDetail::join('obat','kredit_det.id_obat','=','obat.id_obat')
											->where('tanggal_jatuh_tempo',$tanggal_jatuh_tempo)
											->where('status_kredit',0)
											->where('id_kredit_faktur',$kredit_faktur->id_kredit_faktur)
											->get();

			$total_kredit   = KreditDetail::where('tanggal_jatuh_tempo',$tanggal_jatuh_tempo)
											->where('id_kredit_faktur',$kredit_faktur->id_kredit_faktur)
											->where('status_kredit',0)
											->sum('sub_total');

			session()->forget('nomor_upds_relasi');
			return view('Admin.cetak-kredit',compact('harga_satuan','diskon_urai','total_kredit','nama_pelanggan','data','profile_instansi','nomor_faktur','kredit_faktur'));
		}
	}
}
