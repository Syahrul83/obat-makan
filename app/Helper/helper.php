<?php

use Illuminate\Support\Facades\DB;

function format_rupiah($money)
{
    $hasil_rupiah = 'Rp. ' . number_format($money, 2, ',', '.');
    return $hasil_rupiah;
}

function menu_user_check($id, $ket, $menu)
{
    if ($ket == 'parent') {
        $menu_user_check = App\Models\MenuUserModel::where('id_users', $id)
                                                    ->where('menu_parent', $menu)
                                                    ->count();
        if ($menu_user_check > 0) {
            $result = 'true';
        } else {
            $result = 'false';
        }
    } elseif ($ket == 'child') {
        $menu_user_check = App\Models\MenuUserModel::where('id_users', $id)
                                                    ->where('menu_child', $menu)
                                                    ->count();
        if ($menu_user_check > 0) {
            $result = 'true';
        } else {
            $result = 'false';
        }
    }

    return $result;
}

function calculate_real_price($qty, $diskon, $price)
{
    $calculate = ($price + $diskon) / $qty;
    return $calculate;
}

function round_up_thousand($num, $round)
{
    return ceil($num / $round) * $round;
}

function floor_thousand($num, $round)
{
    return floor($num / $round) * $round;
}

function real_discount($num, $round, $price1, $price2)
{
    $calc1 = round_up_thousand($num, $round) + $price1;
    $calc2 = floor_thousand($num, $round) + $price1;
    if ($calc1 == $price2) {
        $result = round_up_thousand($num, $round);
    } else {
        $result = floor_thousand($num, $round);
    }

    return $result;
}

function kalkulasi_diskon($arr)
{
    $hitung_obat = $arr['harga_obat'] * $arr['jumlah'];
    // }

    $disc_nominal = ((($hitung_obat) * $arr['disc_1']) / 100);
    $sub_total    = ($hitung_obat) - ((($hitung_obat) * $arr['disc_1']) / 100);

    $disc_nominal = $disc_nominal + (($sub_total * $arr['disc_2']) / 100);
    $sub_total    = $sub_total - (($sub_total * $arr['disc_2']) / 100);

    $disc_nominal = $disc_nominal + (($sub_total * $arr['disc_3']) / 100);
    $sub_total    = $sub_total - (($sub_total * $arr['disc_3']) / 100);

    // $disc        = ($hitung_obat * $arr['disc_1']) / 100;
    // $disc        = $disc - (($disc * $arr['disc_2']) / 100);
    // $disc        = $disc - (($disc * $arr['disc_3']) / 100);

    return $disc_nominal;
}

function replace_comma_to_dot($num)
{
    $searchFor = ',';

    if (strpos($num, $searchFor) !== false) {
        $value = str_replace($searchFor, '.', $num);
    } else {
        $value = $num;
    }

    return $value;
}

function money_receipt($money)
{
    $hasil_rupiah = number_format($money, 0, '', '.');
    return $hasil_rupiah;
}

function reverse_date($date)
{
    if ($date == '') {
        $val = '';
    } else {
        $explode = explode('-', $date);
        $val = $explode[2] . '-' . $explode[1] . '-' . $explode[0];
    }

    return $val;
}

function get_discount($price, $discount)
{
    $calc = ($price * $discount) / 100;

    return $calc;
}

function get_real_price_discount($discount, $total)
{
    $calc_disc  = 1 - ($discount / 100);
    $calc_total = $total / $calc_disc;

    return $calc_total;
}

function pangkas_jam($jam)
{
    $explode = explode(':', $jam);
    return $explode[0] . ':' . $explode[1];
}

function unslug_str($str)
{
    if (strpos($str, '-') !== false) {
        $get   = explode('-', $str);
        $words =  ucwords($get[0]) . ' ' . ucwords($get[1]);
    } else {
        $words = ucwords($str);
    }

    return $words;
}

function cek_stok($stok, $satuan_stok)
{
    if ($stok == 0) {
        return 'Stok Habis';
    } else {
        return $stok . ' ' . $satuan_stok;
    }
}

function tambah_hari($date)
{
    $tiga_hari = date('Y-m-d', strtotime('+3 days', strtotime($date)));
    return $tiga_hari;
}

function human_date($date)
{
    if($date != '0000-00-00') {
        $explode  = explode('-', $date);
        $new_date = $explode[2] . ' ' . month($explode[1]) . ' ' . $explode[0];
    } else {
        $new_date = '-';
    }
    return $new_date;
}

function month($month)
{
    $array = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return $array[$month];
}

function makeAcronym($str)
{
    $get = strtoupper($str);
    if (strlen($str) > 3) {
        $word[0] = substr($get, 0, 1);
        $word[1] = substr($get, 2, 1);
        $word[2] = substr($get, -1);
    } else {
        $word[0] = $get;
    }
    return implode('', $word);
}

function generateCode($key, $separator, $num, $length)
{
    $zero = '';
    for ($i = 0; $i < 11; $i++) {
        $zero .= 0;
    }
    $new_code = str_pad($num, $length, $zero, STR_PAD_LEFT);
    $generate = $key . $separator . $new_code;
    return $generate;
}

function midnight_time($time, $time2)
{
    if ($time >= '00:00' && $time < $time2) {
        $explode_time = explode(':', $time);
        $convert_time = (int)$explode_time[0] + 25;
        $time = (string)$convert_time . ':' . $explode_time[1];
    }

    return $time;
}

function count_ppn($price1, $ppn)
{
    $total = ($price * $ppn) / 100;

    return $total;
}

function generateCodeTrx($ket)
{
    $tahun = substr(date('Y'), -2);
    $bulan = date('m');
    if ($ket == 'upds') {
        $ket_num         = 2;
        $kode            = $tahun . $bulan . $ket_num;
        $transaksi_kasir = new App\Models\TransaksiKasirModel();
        $count_transaksi = $transaksi_kasir->where('kode_transaksi', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_kode_transaksi = $transaksi_kasir->where('kode_transaksi', 'like', $kode . '%')->orderBy('kode_transaksi', 'DESC')->firstOrFail()->kode_transaksi;
            $substr_kode        = (int)substr($get_kode_transaksi, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'upds-kredit') {
        $ket_num         = 2;
        $kode            = $tahun . $bulan . $ket_num;
        $kredit_faktur = new App\Models\KreditFakturModel();
        $count_transaksi = $kredit_faktur->where('nomor_faktur', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_nomor_faktur = $kredit_faktur->where('nomor_faktur', 'like', $kode . '%')->orderBy('nomor_faktur', 'DESC')->firstOrFail()->nomor_faktur;
            $substr_kode        = (int)substr($get_nomor_faktur, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'relasi') {
        $ket_num         = 1;
        $kode            = $tahun . $bulan . $ket_num;
        $kredit_faktur = new App\Models\KreditFakturModel();
        $count_transaksi = $kredit_faktur->where('nomor_faktur', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_nomor_faktur = $kredit_faktur->where('nomor_faktur', 'like', $kode . '%')->orderBy('nomor_faktur', 'DESC')->firstOrFail()->nomor_faktur;
            $substr_kode        = (int)substr($get_nomor_faktur, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'resep') {
        $ket_num         = 3;
        $kode            = $tahun . $bulan . $ket_num;
        $transaksi_racik_obat = new App\Models\TransaksiRacikObatModel();
        $count_transaksi = $transaksi_racik_obat->where('kode_transaksi', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_kode_transaksi = $transaksi_racik_obat->where('kode_transaksi', 'like', $kode . '%')->orderBy('kode_transaksi', 'DESC')->firstOrFail()->kode_transaksi;
            $substr_kode        = (int)substr($get_kode_transaksi, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'beli-kredit') {
        $ket_num         = 4;
        $kode            = $tahun . $bulan . $ket_num;
        $pembelian_obat = new App\Models\PembelianObatModel();
        $count_transaksi = $pembelian_obat->where('kode_pembelian', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_kode_transaksi = $pembelian_obat->where('jenis_beli', 'kredit')->where('kode_pembelian', 'like', $kode . '%')->orderBy('kode_pembelian', 'DESC')->firstOrFail()->kode_pembelian;
            $substr_kode        = (int)substr($get_kode_transaksi, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'beli-cash') {
        $ket_num         = 5;
        $kode            = $tahun . $bulan . $ket_num;
        $pembelian_obat = new App\Models\PembelianObatModel();
        $count_transaksi = $pembelian_obat->where('kode_pembelian', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_kode_transaksi = $pembelian_obat->where('jenis_beli', 'cash')->where('kode_pembelian', 'like', $kode . '%')->orderBy('kode_pembelian', 'DESC')->firstOrFail()->kode_pembelian;
            $substr_kode        = (int)substr($get_kode_transaksi, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'beli-konsinyasi') {
        $ket_num         = 6;
        $kode            = $tahun . $bulan . $ket_num;
        $pembelian_obat = new App\Models\PembelianObatModel();
        $count_transaksi = $pembelian_obat->where('kode_pembelian', 'like', $kode . '%')->count();
        if ($count_transaksi == 0) {
            $number = 1;
        } else {
            $get_kode_transaksi = $pembelian_obat->where('jenis_beli', 'konsinyasi')->where('kode_pembelian', 'like', $kode . '%')->orderBy('kode_pembelian', 'DESC')->firstOrFail()->kode_pembelian;
            $substr_kode        = (int)substr($get_kode_transaksi, -4);
            $number             = $substr_kode + 1;
        }
    } elseif ($ket == 'retur-barang') {
        $ket_num = 7;
        $kode = $tahun . $bulan . $ket_num;
        $retur_barang = new App\Models\ReturBarangModel();
        $count_retur_barang = $retur_barang->where('nomor_retur', 'like', $kode . '%')->count();
        if ($count_retur_barang == 0) {
            $number = 1;
        } else {
            $get_kode_retur_barang = $retur_barang->where('nomor_retur', 'like', $kode . '%')->orderBy('nomor_retur', 'DESC')->firstOrFail()->nomor_retur;
            $substr_kode        = (int)substr($get_kode_retur_barang, -4);
            $number             = $substr_kode + 1;
        }
    }
    $generate_code = $kode . str_pad($number, 4, '0000', STR_PAD_LEFT);

    return $generate_code;
}

function check_kode_transaksi($kode_transaksi)
{
    $substr  = substr($kode_transaksi, 4);
    $substr2 = substr($substr, 0, -4);
    $ket     = '';
    if ($substr2 == '2') {
        $ket = 'upds';
    } elseif ($substr2 == '3') {
        $ket = 'resep';
    } elseif ($substr2 == '3') {
        $ket = 'resep';
    }

    return $ket;
}

function generateUuid()
{
    $str = (string)Illuminate\Support\Str::Uuid();
    return $str;
}

function date_excel($date)
{
    if($date != '0000-00-00') {
        $new_date = date('d/m/Y', strtotime($date));
    } else {
        $new_date = '-';
    }

    return $new_date;
}


function retur($trx, $obat_id)
{

    $retur_barang_id = DB::table('retur_barang')
    ->where('nomor_transaksi', $trx)
    ->value('id_retur_barang');

    $count = DB::table('retur_barang_detail')

     ->where('id_retur_barang', $retur_barang_id)
     ->where('id_obat', $obat_id)
     ->count();


    if($count < 1 or $count == null) {
        return  $stok_retur = DB::table('retur_barang_detail')
          ->where('id_retur_barang', $retur_barang_id)
          ->where('id_obat', $obat_id)
          ->value('stok_retur');


    } elseif($count > 1) {

        $stok = DB::table('retur_barang_detail')
        ->where('id_retur_barang', $retur_barang_id)
        ->where('id_obat', $obat_id)
        ->get();
        // dd($hit = $count - 1);
        // // dd($stok[2]->stok_retur);
        // if (empty($stok)) {
        //     $hit = $count - 1;
        //     $range = range(0, $hit);
        //     $stok_retur = $stok[$range]->stok_retur;

        // } else {
        return    $stok_retur = 3; // Set a default value if $stok is empty
        // }
    }

}

function returRingkas($tgl_awal, $tgl_akhir, $obat_id)
{
    $stok_retur = DB::table('retur_barang')
        ->leftJoin('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')
        ->whereBetween('retur_barang.tanggal_transaksi', [$tgl_awal, $tgl_akhir])
        ->where('retur_barang_detail.id_obat', $obat_id)
        ->select(['retur_barang_detail.id_obat', DB::raw('SUM(retur_barang_detail.stok_retur) as stok_retur')])
        ->groupBy('retur_barang_detail.id_obat')
        ->get();

    if ($stok_retur->isEmpty()) {
        $stok_retur = 0;
    } else {
        $stok_retur = $stok_retur->first()->stok_retur;
    }
    return $stok_retur ?? 0;
}
