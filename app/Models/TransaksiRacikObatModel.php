<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\RacikObatDataModel as RacikObatData;

class TransaksiRacikObatModel extends Model
{
    protected $table      = 'transaksi_racik_obat';
    protected $primaryKey = 'id_transaksi_racik_obat';
    protected $guarded    = [];

    public static function getData($id = '')
    {
        if ($id == '') {
            $get = self::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                        ->join('pasien','racik_obat_data.id_pasien','=','pasien.id_pasien')
                        ->join('dokter','racik_obat_data.id_dokter','=','dokter.id_dokter')
                        ->join('users','transaksi_racik_obat.id_users','=','users.id_users');
        }
        else {
            $get = self::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                        ->join('pasien','racik_obat_data.id_pasien','=','pasien.id_pasien')
                        ->join('dokter','racik_obat_data.id_dokter','=','dokter.id_dokter')
                        ->join('users','transaksi_racik_obat.id_users','=','users.id_users')
                        ->where('transaksi_racik_obat.id_users',auth()->id());   
        }

        return $get;
    }

    public static function export($from,$to,$id_jam_shift) 
    {
        $db = self::join('racik_obat_data','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                    ->join('pasien','racik_obat_data.id_pasien','=','pasien.id_pasien')
                    ->join('dokter','racik_obat_data.id_dokter','=','dokter.id_dokter')
                    ->whereBetween('tanggal_transaksi',[$from,$to])
                    ->where('id_jam_shift',$id_jam_shift)
                    ->orderBy('tanggal_transaksi','DESC')
                    ->get();
        return $db;
    }

    public static function potonganResep($from,$to,$jam_shift)
    {
       
        // if ($jam_shift == 5) {
            $db = self::whereBetween('tanggal_transaksi',[$from,$to])->where('id_jam_shift',$jam_shift)->get();

            $disc_calc = 0;
            foreach ($db as $key => $value) {
                $sum_total = RacikObatData::join('transaksi_racik_obat','transaksi_racik_obat.id_racik_obat_data','=','racik_obat_data.id_racik_obat_data')
                                ->where('kode_transaksi',$value->kode_transaksi)
                                ->sum('total_semua');

                $disc_calc = floor_thousand(get_discount($value->diskon,$sum_total),1000);
                // if ($value->jenis_diskon == 'rupiah') {
                //     $disc = 
                // }
                // else {
                //     $disc_calc = $disc_calc + $value->diskon;
                // }
                // if ($value->diskon != 0) {
                //     $disc       = $value->diskon / 100;
                //     $real_price = $value->harga_total / $disc;
                //     $calc       = ($real_price * $value->diskon) / 100;
                //     $disc_calc  = $disc_calc + $calc;
                //     dd($calc);
                // }
                // else {
                //     $disc_calc = $disc_calc + 0;
                // }


            // }
			}

            return $disc_calc;
    }
}
