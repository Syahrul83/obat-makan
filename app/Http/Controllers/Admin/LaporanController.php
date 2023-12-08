<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransaksiKasirModel as TransaksiKasir;
use App\Models\TransaksiKasirDetailModel as TransaksiKasirDetail;
use App\Models\TransaksiRacikObatModel as TransaksiRacikObat;
use App\Models\PembelianObatModel as PembelianObat;
use App\Models\PembelianDetailModel as PembelianDetail;
use App\Models\SupplierModel as Supplier;
use App\Models\PemakaianModel as Pemakaian;
use App\Models\DokterModel as Dokter;
use App\Models\ObatModel as Obat;
use App\Models\JamShiftModel as JamShift;
use App\Models\PabrikObatModel as PabrikObat;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use App\Models\RacikObatModel as RacikObat;
use App\Models\RacikObatDataModel as RacikObatData;
use App\Models\RacikObatDetailModel as RacikObatDetail;
use App\Models\ReturBarangModel as ReturBarang;
use App\Models\ReturBarangDetailModel as ReturBarangDetail;
use App\Models\KreditModel as Kredit;
use App\Models\KreditFakturModel as KreditFaktur;
use App\Models\KreditDetailModel as KreditDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use DB;

// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanController extends Controller
{
    public function index()
    {
        $title = 'Laporan Data';
        $page  = 'laporan-data';

        return view('Admin.laporan-data.main', compact('title', 'page'));
    }

    public function laporanPenjualan(Request $request)
    {
        $btn_act = $request->btn_act;

        if ($btn_act == 'laporan-penjualan-harian') {
            $this->laporanPenjualanHarian($request);
        }
        if ($btn_act == 'laporan-upds') {
            $this->laporanTransaksi($request);
        }
        if ($btn_act == 'laporan-resep') {
            $this->laporanTransaksiRacikObat($request);
        }
        if ($btn_act == 'laporan-pemakaian-obat') {
            $this->laporanPemakaianObat($request);
        }
        if ($btn_act == 'laporan-pemakaian-dokter') {
            $this->laporanPemakaianDokter($request);
        }
        if ($btn_act == 'laporan-pemakaian-supplier') {
            $this->laporanPemakaianSupplier($request);
        }
        if ($btn_act == 'laporan-pemakaian-pabrik') {
            $this->laporanPemakaianPabrik($request);
        }
    }

    public function laporanPembelian(Request $request)
    {
        $btn_act = $request->btn_act;

        if ($btn_act == 'laporan-faktur') {
            $this->laporanFaktur($request);
        }
        if ($btn_act == 'laporan-beli-tunai') {
            $this->laporanBeliTunai($request);
        }
        if ($btn_act == 'laporan-jatuh-tempo') {
            $this->laporanJatuhTempo($request);
        }
        if ($btn_act == 'laporan-retur-barang') {
            $this->laporanReturBarang($request);
        }
        if ($btn_act == 'laporan-konsinyasi') {
            $this->laporanKonsinyasi($request);
        }
        if ($btn_act == 'laporan-konsinyasi-jatuh-tempo') {
            $this->laporanKonsinyasiJatuhTempo($request);
        }
    }

    public function laporanPenjualanHarian(Request $request)
    {
        ob_end_clean();
        $from      = reverse_date($request->from);
        $to        = reverse_date($request->to);
        $title     = 'Laporan Penjualan Harian ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile   = ProfileInstansi::firstOrFail();
        $jam_shift = JamShift::where('status_delete', 0)->get();

        $fileName  = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        foreach ($jam_shift as $key => $value) {
            $spreadsheet->setActiveSheetIndex($key)->setTitle($value->ket_shift);

            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A4', 'Laporan Penjualan Harian');
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Shift : ' . $value->ket_shift);
            $spreadsheet->getActiveSheet()->setCellValue('A6', 'Tanggal : ' . human_date($from) . ' s/d ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:H3');
            $spreadsheet->getActiveSheet()->mergeCells('A4:H4');
            $spreadsheet->getActiveSheet()->mergeCells('A5:H5');
            $spreadsheet->getActiveSheet()->mergeCells('A6:H6');
            $spreadsheet->getActiveSheet()->getStyle('A1:A6')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $count_trx_resep = TransaksiRacikObat::whereBetween('tanggal_transaksi', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_trx_upds = TransaksiKasir::whereBetween('tanggal_transaksi', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_kredit = KreditFaktur::whereBetween('tanggal_faktur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_obat_resep = RacikObatData::join('racik_obat_detail', 'racik_obat_data.id_racik_obat_data', '=', 'racik_obat_detail.id_racik_obat_detail')
                                            ->join('transaksi_racik_obat', 'racik_obat_data.id_racik_obat_data', '=', 'transaksi_racik_obat.id_racik_obat_data')
                                            ->whereBetween('tanggal_racik', [$from,$to])
                                            ->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_obat_upds = TransaksiKasir::join('transaksi_kasir_det', 'transaksi_kasir.id_transaksi', '=', 'transaksi_kasir_det.id_transaksi')
                                            ->whereBetween('tanggal_transaksi', [$from,$to])
                                            ->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_obat_kredit = KreditDetail::join('kredit_faktur', 'kredit_det.id_kredit_faktur', '=', 'kredit_faktur.id_kredit_faktur')->whereBetween('tanggal_faktur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $jasa_resep = RacikObat::join('racik_obat_data', 'racik_obat.id_racik_obat_data', '=', 'racik_obat_data.id_racik_obat_data')
                                    ->join('transaksi_racik_obat', 'racik_obat_data.id_racik_obat_data', '=', 'transaksi_racik_obat.id_racik_obat_data')
                                    ->whereBetween('tanggal_racik', [$from,$to])
                                    ->where('id_jam_shift', $value->id_jam_shift)
                                    ->sum('ongkos_racik');

            $embalase_resep = RacikObat::join('racik_obat_data', 'racik_obat.id_racik_obat_data', '=', 'racik_obat_data.id_racik_obat_data')
                                        ->join('racik_obat_detail', 'racik_obat.id_racik_obat', '=', 'racik_obat_detail.id_racik_obat')
                                        ->join('transaksi_racik_obat', 'racik_obat_data.id_racik_obat_data', '=', 'transaksi_racik_obat.id_racik_obat_data')
                                        ->whereBetween('tanggal_racik', [$from,$to])
                                        ->where('id_jam_shift', $value->id_jam_shift)->sum('embalase');

            $potongan_resep  = TransaksiRacikObat::potonganResep($from, $to, $value->id_jam_shift);
            $potongan_upds   = TransaksiKasirDetail::potonganUpds($from, $to, $value->id_jam_shift);
            $potongan_kredit = KreditDetail::potonganKredit($from, $to, $value->id_jam_shift);

            $netto_resep = TransaksiRacikObat::whereBetween('tanggal_transaksi', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('harga_total');
            $netto_upds  = TransaksiKasir::whereBetween('tanggal_transaksi', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('total');
            $netto_kredit = KreditDetail::join('kredit_faktur', 'kredit_det.id_kredit_faktur', '=', 'kredit_faktur.id_kredit_faktur')->whereBetween('tanggal_faktur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('sub_total');

            $spreadsheet->getActiveSheet()->setCellValue('A8', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B8', 'Pelanggan');
            $spreadsheet->getActiveSheet()->setCellValue('C8', 'Lembar');
            $spreadsheet->getActiveSheet()->setCellValue('D8', 'R/');
            $spreadsheet->getActiveSheet()->setCellValue('E8', 'Jasa');
            $spreadsheet->getActiveSheet()->setCellValue('F8', 'Embalase');
            $spreadsheet->getActiveSheet()->setCellValue('G8', 'Potongan');
            $spreadsheet->getActiveSheet()->setCellValue('H8', 'Sub Total');
            $spreadsheet->getActiveSheet()->setCellValue('A9', 'Penjualan Kredit');
            $spreadsheet->getActiveSheet()->mergeCells('A9:H9');
            $spreadsheet->getActiveSheet()->getStyle('A9:H9')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->setCellValue('A10', '1.');
            $spreadsheet->getActiveSheet()->setCellValue('B10', 'Kredit');
            $spreadsheet->getActiveSheet()->setCellValue('C10', $count_kredit);
            $spreadsheet->getActiveSheet()->setCellValue('D10', $count_obat_kredit);
            $spreadsheet->getActiveSheet()->setCellValue('E10', 0);
            $spreadsheet->getActiveSheet()->setCellValue('F10', 0);
            $spreadsheet->getActiveSheet()->setCellValue('G10', $potongan_kredit);
            $spreadsheet->getActiveSheet()->setCellValue('H10', $netto_kredit);
            DB::connection()->enableQueryLog();
            $count_retur_kredit = ReturBarang::join('kredit_faktur', 'retur_barang.nomor_transaksi', '=', 'kredit_faktur.nomor_faktur')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_retur_obat_kredit = ReturBarang::join('kredit_faktur', 'retur_barang.nomor_transaksi', '=', 'kredit_faktur.nomor_faktur')->join('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $total_retur_kredit = ReturBarang::join('kredit_faktur', 'retur_barang.nomor_transaksi', '=', 'kredit_faktur.nomor_faktur')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('total_nominal_retur');

            $spreadsheet->getActiveSheet()->setCellValue('A11', '2.');
            $spreadsheet->getActiveSheet()->setCellValue('B11', 'Retur');
            $spreadsheet->getActiveSheet()->setCellValue('C11', $count_retur_kredit);
            $spreadsheet->getActiveSheet()->setCellValue('D11', $count_retur_obat_kredit);
            $spreadsheet->getActiveSheet()->setCellValue('E11', 0);
            $spreadsheet->getActiveSheet()->setCellValue('F11', 0);
            $spreadsheet->getActiveSheet()->setCellValue('G11', 0);
            $spreadsheet->getActiveSheet()->setCellValue('H11', $total_retur_kredit);

            $spreadsheet->getActiveSheet()->setCellValue('A12', 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('C12', '=SUM(C10:C11)');
            $spreadsheet->getActiveSheet()->setCellValue('D12', '=SUM(D10:D11)');
            $spreadsheet->getActiveSheet()->setCellValue('E12', '=SUM(E10:E11)');
            $spreadsheet->getActiveSheet()->setCellValue('F12', '=SUM(F10:F11)');
            $spreadsheet->getActiveSheet()->setCellValue('G12', '=SUM(G10:G11)');
            $spreadsheet->getActiveSheet()->setCellValue('H12', '=H10-H11');
            $spreadsheet->getActiveSheet()->mergeCells('A12:B12');
            $spreadsheet->getActiveSheet()->getStyle('A12:B12')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->setCellValue('A13', 'Penjualan Tunai');
            $spreadsheet->getActiveSheet()->mergeCells('A13:H13');
            $spreadsheet->getActiveSheet()->getStyle('A13:H13')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->setCellValue('A14', '1.');
            $spreadsheet->getActiveSheet()->setCellValue('B14', 'Resep Tunai');
            $spreadsheet->getActiveSheet()->setCellValue('C14', $count_trx_resep);
            $spreadsheet->getActiveSheet()->setCellValue('D14', $count_obat_resep);
            $spreadsheet->getActiveSheet()->setCellValue('E14', $jasa_resep);
            $spreadsheet->getActiveSheet()->setCellValue('F14', $embalase_resep);
            $spreadsheet->getActiveSheet()->setCellValue('G14', $potongan_resep);
            $spreadsheet->getActiveSheet()->setCellValue('H14', $netto_resep);

            $spreadsheet->getActiveSheet()->setCellValue('A15', '2.');
            $spreadsheet->getActiveSheet()->setCellValue('B15', 'UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('C15', $count_trx_upds);
            $spreadsheet->getActiveSheet()->setCellValue('D15', $count_obat_upds);
            $spreadsheet->getActiveSheet()->setCellValue('E15', 0);
            $spreadsheet->getActiveSheet()->setCellValue('F15', 0);
            $spreadsheet->getActiveSheet()->setCellValue('G15', $potongan_upds);
            $spreadsheet->getActiveSheet()->setCellValue('H15', $netto_upds);

            $total_upds = ReturBarang::join('transaksi_kasir', 'retur_barang.nomor_transaksi', '=', 'transaksi_kasir.kode_transaksi')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('total_nominal_retur');

            $total_resep = ReturBarang::join('transaksi_racik_obat', 'retur_barang.nomor_transaksi', '=', 'transaksi_racik_obat.kode_transaksi')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->sum('total_nominal_retur');

            $total_retur_tunai = $total_upds + $total_resep;

            $count_retur_upds = ReturBarang::join('transaksi_kasir', 'retur_barang.nomor_transaksi', '=', 'transaksi_kasir.kode_transaksi')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_retur_obat_upds = ReturBarang::join('transaksi_kasir', 'retur_barang.nomor_transaksi', '=', 'transaksi_kasir.kode_transaksi')->join('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_retur_resep = ReturBarang::join('transaksi_racik_obat', 'retur_barang.nomor_transaksi', '=', 'transaksi_racik_obat.kode_transaksi')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_retur_obat_resep = ReturBarang::join('transaksi_racik_obat', 'retur_barang.nomor_transaksi', '=', 'transaksi_racik_obat.kode_transaksi')->join('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')->whereBetween('tanggal_retur', [$from,$to])->where('id_jam_shift', $value->id_jam_shift)->count();

            $count_retur_tunai      = $count_retur_upds + $count_retur_resep;
            $count_retur_obat_tunai = $count_retur_obat_upds + $count_retur_obat_resep;

            $spreadsheet->getActiveSheet()->setCellValue('A16', '3.');
            $spreadsheet->getActiveSheet()->setCellValue('B16', 'Retur');
            $spreadsheet->getActiveSheet()->setCellValue('C16', $count_retur_tunai);
            $spreadsheet->getActiveSheet()->setCellValue('D16', $count_retur_obat_tunai);
            $spreadsheet->getActiveSheet()->setCellValue('E16', 0);
            $spreadsheet->getActiveSheet()->setCellValue('F16', 0);
            $spreadsheet->getActiveSheet()->setCellValue('G16', 0);
            $spreadsheet->getActiveSheet()->setCellValue('H16', $total_retur_tunai);

            $spreadsheet->getActiveSheet()->setCellValue('A17', 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('C17', '=SUM(C14:C16)');
            $spreadsheet->getActiveSheet()->setCellValue('D17', '=SUM(D14:D16)');
            $spreadsheet->getActiveSheet()->setCellValue('E17', '=SUM(E14:E16)');
            $spreadsheet->getActiveSheet()->setCellValue('F17', '=SUM(F14:F16)');
            $spreadsheet->getActiveSheet()->setCellValue('G17', '=SUM(G14:G16)');
            $spreadsheet->getActiveSheet()->setCellValue('H17', '=SUM(H14:H15)-(H16)');
            $spreadsheet->getActiveSheet()->mergeCells('A17:B17');
            $spreadsheet->getActiveSheet()->getStyle('A17:B17')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->setCellValue('A18', 'GRAND TOTAL');
            // $spreadsheet->getActiveSheet()->setCellValue('C12','=SUM(C10:C11)');
            // $spreadsheet->getActiveSheet()->setCellValue('D12','=SUM(D10:D11)');
            // $spreadsheet->getActiveSheet()->setCellValue('E12','=SUM(E10:E11)');
            // $spreadsheet->getActiveSheet()->setCellValue('F12','=SUM(F10:F11)');
            // $spreadsheet->getActiveSheet()->setCellValue('G12','=SUM(G10:G11)');
            $spreadsheet->getActiveSheet()->setCellValue('H18', '=H12+H17');
            $spreadsheet->getActiveSheet()->mergeCells('A18:G18');

            // dd(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

            $styleTable = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
            $spreadsheet->getActiveSheet()->getStyle('A8:H18')->applyFromArray($styleTable);

            $spreadsheet->getActiveSheet()->getStyle('E10:H18')->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');

            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            $spreadsheet->createSheet();
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanTransaksi(Request $request)
    {
        ob_end_clean();
        $from     = reverse_date($request->from);
        $to       = reverse_date($request->to);
        $title    = 'Laporan Transaksi Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile  = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $jam_shift   = JamShift::where('status_delete', 0)->get();
        foreach ($jam_shift as $key_index => $arr) {
            $spreadsheet->setActiveSheetIndex($key_index)->setTitle("$arr->ket_shift");
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Kasir Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:F5');
            $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Kode Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Tanggal Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Bayar');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Kembali');

            $no = 8;

            $get = TransaksiKasir::export($from, $to, $arr->id_jam_shift);
            foreach ($get as $key => $value) {
                $count = $key + 1;
                $spreadsheet->getActiveSheet()->setCellValue('A' . $no, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $no, "$value->kode_transaksi");
                $spreadsheet->getActiveSheet()->setCellValue('C' . $no, human_date($value->tanggal_transaksi));
                $spreadsheet->getActiveSheet()->setCellValue('D' . $no, $value->total);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $no, $value->bayar);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $no, $value->kembali);
                $no = $no + 1;
            }

            $spreadsheet->getActiveSheet()->setCellValue('A' . $no, 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('D' . $no, "=SUM(D8:D$no)");
            $spreadsheet->getActiveSheet()->setCellValue('E' . $no, "=SUM(E8:E$no)");
            $spreadsheet->getActiveSheet()->setCellValue('F' . $no, "=SUM(F8:F$no)");
            $spreadsheet->getActiveSheet()->mergeCells("A$no:C$no");
            $spreadsheet->getActiveSheet()->getStyle("A$no:C$no")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('D8:F' . $no)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');

            $spreadsheet->createSheet();
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        }
        $spreadsheet->setActiveSheetIndex(0);


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanTransaksiRacikObat(Request $request)
    {
        ob_end_clean();
        $from     = reverse_date($request->from);
        $to       = reverse_date($request->to);
        $title    = 'Laporan Transaksi Racik Obat Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile  = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $jam_shift   = JamShift::where('status_delete', 0)->get();

        $spreadsheet = new Spreadsheet();
        foreach ($jam_shift as $index => $data) {
            $spreadsheet->setActiveSheetIndex($index)->setTitle("$data->ket_shift");
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Transaksi Non UPDS Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:G3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:G5');
            $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nama Pasien');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Dokter');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Bayar');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Kembali');

            $no  = 8;
            $get = TransaksiRacikObat::export($from, $to, $data->id_jam_shift);
            foreach ($get as $key => $value) {
                $count = $key + 1;
                $spreadsheet->getActiveSheet()->setCellValue('A' . $no, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $no, human_date($value->tanggal_transaksi));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $no, $value->nama_pasien);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $no, $value->nama_dokter);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $no, $value->harga_total);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $no, $value->bayar);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $no, $value->kembalian);
                $no++;
            }

            $spreadsheet->getActiveSheet()->setCellValue('A' . $no, 'Total');
            $spreadsheet->getActiveSheet()->setCellValue('E' . $no, "=SUM(E8:E$no)");
            $spreadsheet->getActiveSheet()->setCellValue('F' . $no, "=SUM(F8:F$no)");
            $spreadsheet->getActiveSheet()->setCellValue('G' . $no, "=SUM(G8:G$no)");
            $spreadsheet->getActiveSheet()->mergeCells("A$no:D$no");
            $spreadsheet->getActiveSheet()->getStyle("A$no:D$no")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('E8:G' . $no)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');

            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    // public function laporanPemakaianObat(Request $request)
    // {
    //     ob_end_clean();
    //     $from    = reverse_date($request->from);
    //     $to      = reverse_date($request->to);
    //     $title   = 'Laporan Pemakaian Obat Dari Tanggal '.human_date($from).' Sampai Tanggal '.human_date($to);
    //     $profile = ProfileInstansi::firstOrFail();

    //     $fileName = $title.'.xlsx';

    //     $spreadsheet = new Spreadsheet();
    //     $spreadsheet->getActiveSheet()->setCellValue('A1',$profile->nama_instansi);
    //     $spreadsheet->getActiveSheet()->setCellValue('A3',$profile->alamat_instansi);
    //     $spreadsheet->getActiveSheet()->setCellValue('A5','Laporan Pemakaian Dari '.human_date($from).' Sampai '.human_date($to));
    //     $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
    //     $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
    //     $spreadsheet->getActiveSheet()->mergeCells('A5:F5');
    //     $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
    //         'alignment'=>[
    //             'horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    //         ]
    //     ]);
    //     $spreadsheet->getActiveSheet()->setCellValue('A7','No.');
    //     $spreadsheet->getActiveSheet()->setCellValue('B7','Tanggal Pakai Dari');
    //     $spreadsheet->getActiveSheet()->setCellValue('C7','Tanggal Pakai Sampai');
    //     $spreadsheet->getActiveSheet()->setCellValue('D7','Pabrik');
    //     $spreadsheet->getActiveSheet()->setCellValue('E7','Nama Obat');
    //     $spreadsheet->getActiveSheet()->setCellValue('F7','Jenis Obat');
    //     $spreadsheet->getActiveSheet()->setCellValue('G7','Golongan Obat');
    //     $spreadsheet->getActiveSheet()->setCellValue('H7','Stok Pakai');
    //     $spreadsheet->getActiveSheet()->setCellValue('I7','Hna');

    //     $obat = Pemakaian::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
    //                         ->whereBetween('tanggal_pemakaian',[$from,$to])
    //                         ->distinct()
    //                         ->get(['pemakaian_obat.id_obat']);

    //     $cell = 8;
    //     $sum  = 0;
    //     foreach ($obat as $index => $value) {
    //         $sum_pakai = Pemakaian::getSumExportObat($from,$to,$value->id_obat);
    //         $count     = $index+1;

    //         $get_obat  = Obat::join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
    //                 ->join('golongan_obat','obat.id_golongan_obat','=','golongan_obat.id_golongan_obat')
    //                 ->join('pabrik_obat','obat.id_pabrik_obat','=','pabrik_obat.id_pabrik_obat')
    //                 ->where('id_obat',$value->id_obat)
    //                 ->firstOrFail();

    //         $tanggal_pakai_dari = Pemakaian::where('id_obat',$value->id_obat)
    //                                 ->whereBetween('tanggal_pemakaian',[$from,$to])
    //                                 ->orderBy('tanggal_pemakaian','ASC')
    //                                 ->firstOrFail()
    //                                 ->tanggal_pemakaian;

    //         $tanggal_pakai_sampai = Pemakaian::where('id_obat',$value->id_obat)
    //                                 ->whereBetween('tanggal_pemakaian',[$from,$to])
    //                                 ->orderBy('tanggal_pemakaian','DESC')
    //                                 ->firstOrFail()
    //                                 ->tanggal_pemakaian;


    //         $spreadsheet->getActiveSheet()->setCellValue('A'.$cell,$count);
    //         $spreadsheet->getActiveSheet()->setCellValue('B'.$cell,date_excel($tanggal_pakai_dari));
    //         $spreadsheet->getActiveSheet()->setCellValue('c'.$cell,date_excel($tanggal_pakai_sampai));
    //         $spreadsheet->getActiveSheet()->setCellValue('D'.$cell,$get_obat->nama_pabrik);
    //         $spreadsheet->getActiveSheet()->setCellValue('E'.$cell,$get_obat->nama_obat);
    //         $spreadsheet->getActiveSheet()->setCellValue('F'.$cell,$get_obat->nama_jenis_obat);
    //         $spreadsheet->getActiveSheet()->setCellValue('G'.$cell,$get_obat->nama_golongan);
    //         $spreadsheet->getActiveSheet()->setCellValue('H'.$cell,$sum_pakai);
    //         $spreadsheet->getActiveSheet()->setCellValue('I'.$cell,$get_obat->harga_modal * $sum_pakai);
    //         $cell = $cell+1;
    //     }

    //     $spreadsheet->getActiveSheet()->setCellValue('A'.$cell,'Total Pakai Stok');
    //     $spreadsheet->getActiveSheet()->setCellValue('H'.$cell,"=SUM(H8:H$cell)");
    //     $spreadsheet->getActiveSheet()->setCellValue('I'.$cell,"=SUM(I8:I$cell)");

    //     $spreadsheet->getActiveSheet()->getStyle('I8:I'.$cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
    //     $spreadsheet->getActiveSheet()->mergeCells("A$cell:G$cell");
    //     $spreadsheet->getActiveSheet()->getStyle("A$cell:G$cell")->applyFromArray([
    //         'alignment'=>[
    //             'horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    //         ]
    //     ]);

    //     $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

    //     $spreadsheet->getActiveSheet()->getPageSetup()
    //                 ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    //     $spreadsheet->getActiveSheet()->getPageSetup()
    //                 ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

    //     $writer = new Xlsx($spreadsheet);
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename="'.$fileName.'"');
    //     $writer->save('php://output');
    // }

    public function laporanPemakaianObat(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Pemakaian Obat Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)->setTitle('Rincian');
        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Pemakaian Obat Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:F5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pakai');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jenis Obat');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Golongan Obat');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'Stok Pakai');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'Retur');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hna');


        $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                            ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                            ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
                            ->whereBetween('tanggal_pemakaian', [$from,$to])
                            // ->distinct()
                            // ->orderBy('tanggal_pemakaian', 'DESC')
                            ->get();


        // $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
        // ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
        // ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
        // ->join('retur_barang', 'pemakaian_obat.nomor_transaksi', '=', 'retur_barang.nomor_transaksi')
        // ->join('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')
        // ->whereBetween('pemakaian_obat.tanggal_pemakaian', [$from,$to])
        // ->orderBy('pemakaian_obat.tanggal_pemakaian', 'DESC')
        // ->get();

        // dd($obat);

        $cell = 8;
        $sum  = 0;



        // $get1 = ReturBarangDetail::join('retur_barang', 'retur_barang_detail.id_retur_barang', '=', 'retur_barang.id_retur_barang')
        // ->join('pemakaian_obat', 'retur_barang.nomor_transaksi', '=', 'pemakaian_obat.nomor_transaksi')
        // ->distinct('pemakaian_obat.id_pemakaian')
        // ->whereBetween('pemakaian_obat.tanggal_pemakaian', [$from, $to])
        // ->whereBetween('retur_barang.tanggal_retur', [$from, $to])
        // ->where('pemakaian_obat.id_obat', '=', 3368)
        // ->get();

        //  dd($get1->pluck('id_obat'));



        // $stok[] = [
        //       'id_pemakaian' => $val['id_pemakaian'],
        //       'id_obat' => $val['id_obat'],
        //       'stok_retur' => $value1['stok_retur'],
        //   ];


        // if ($val->id_obat === $stok[$index2]['id_obat']) {

        //     $stok_retur4[$val->id_pemakaian][$val->id_obat] = $stok[$index2]['stok_retur'];

        // }

        $get1   = ReturBarangDetail::join('retur_barang', 'retur_barang_detail.id_retur_barang', '=', 'retur_barang.id_retur_barang')
        ->join('obat', 'retur_barang_detail.id_obat', '=', 'obat.id_obat')
        ->whereBetween('tanggal_retur', [$from,$to])
           ->orderBy('retur_barang.tanggal_retur', 'DESC')
          ->orderBy('retur_barang_detail.id_obat', 'DESC')
        ->get([ 'retur_barang_detail.id_obat','retur_barang.nomor_transaksi','retur_barang_detail.stok_retur']);


        $pem =  Pemakaian::whereIn('nomor_transaksi', $get1->pluck('nomor_transaksi'))
        ->whereIn('id_obat', $get1->pluck('id_obat'))
        ->whereBetween('tanggal_pemakaian', [$from,$to])
        ->orderBy('tanggal_pemakaian', 'DESC')
          ->orderBy('id_obat', 'DESC')

        ->get(['pemakaian_obat.id_pemakaian', 'pemakaian_obat.id_obat']);


        $stok = [];
        $stok_retur4 = [];
        foreach ($pem as $index2 => $val) {
            foreach ($get1 as $key => $value1) {
                $stok[] = [
                    'id_obat' => $value1->id_obat,
                     'nomor_transaksi' => $value1->nomor_transaksi,
                    'stok_retur' => $value1->stok_retur,
                ];

                if ($val->id_obat ===  $stok[$index2]['id_obat']) {

                    $stok_retur4[$val->id_pemakaian][$val->id_obat] = $stok[$index2]['stok_retur'];

                }

            }

        }





        // dd($stok_retur4);


        // $pem =  Pemakaian::rightJoin('retur_barang_detail', 'pemakaian_obat.id_obat', '=', 'retur_barang_detail.id_obat')
        // ->whereIn('pemakaian_obat.nomor_transaksi', $get1->pluck('nomor_transaksi'))
        // ->whereIn('retur_barang_detail.id_retur_barang_detail', $get1->pluck('id_retur_barang_detail'))
        // ->whereIn('pemakaian_obat.id_obat', $get1->pluck('id_obat'))
        // ->whereBetween('tanggal_pemakaian', [$from,$to])
        // ->orderBy('pemakaian_obat.id_obat', 'ASC')
        // ->get();




        // $pem =  Pemakaian::join('retur_barang', 'retur_barang.nomor_transaksi', '=', 'pemakaian_obat.nomor_transaksi')
        // ->join('retur_barang_detail', 'retur_barang.id_retur_barang', '=', 'retur_barang_detail.id_retur_barang')
        // ->whereIn('pemakaian_obat.nomor_transaksi', $get1->pluck('nomor_transaksi'))
        // ->whereIn('retur_barang_detail.id_retur_barang_detail', $get1->pluck('id_retur_barang_detail'))
        // ->whereIn('pemakaian_obat.id_obat', $get1->pluck('id_obat'))
        // ->whereBetween('retur_barang.tanggal_retur', [$from, $to])
        // ->whereBetween('tanggal_pemakaian', [$from,$to])
        // ->orderBy('pemakaian_obat.id_obat', 'ASC')
        // ->get();

        // dd($pem);
        // $stok_retur4 = [];
        // // foreach ($obat as $index => $value) {
        // foreach ($get1   as $key => $value1) {

        //     // $stok_retur1[$index][$value->id_obat] = $value1->stok_retur;
        //     // $stok_retur2[$value1->id_retur_barang_detail] = $value1->stok_retur;
        //     // $stok_retur3[$key]['id_retur_barang_detail'] = $value1->id_retur_barang_detail;
        //     // $stok_retur3[$key]['id_obat'] = $value1->id_obat;



        //     $stok_retur4[][$value1->id_obat] = $value1->stok_retur;


        // }
        // }

        // foreach ($obat as $index2 => $val) {
        //     foreach ($get1 as $key => $value1) {
        //         if ($val->id_obat == $value1->id_obat && $val->nomor_transaksi == $value1->nomor_transaksi) {
        //             if (!isset($stok_retur4[$index2][$val->id_obat])) {
        //                 $stok_retur4[$index2][$val->id_obat] = array();
        //             }
        //             $stok_retur4[$index2][$val->id_obat][] = $value1->stok_retur;
        //         }
        //     }
        // }
        // dd($stok_retur4);

        foreach ($obat as $index => $value) {
            // dd($value);
            $sum_pakai = Pemakaian::getSumExportObat($from, $to, $value->id_obat);
            $count     = $index + 1;

            $tes[] = $index;

            //     $retur_barang_id = DB::table('retur_barang')
            //     ->whereBetween('retur_barang.tanggal_transaksi', [$from,$to])
            //     ->where('nomor_transaksi', $value->nomor_transaksi)
            //     ->value('id_retur_barang');

            // $stok_retur = DB::table('retur_barang_detail')
            // ->whereIn('id_retur_barang', $stok_retur3)
            // ->where('id_obat', $value->id_obat)
            // ->value('stok_retur');




            // $stok_retur1 = 0;
            // foreach ($stok_retur as $key => $value1) {

            //     $stok_retur1 = $value1->stok_retur;
            // }

            // dd($stok_retur1 ?? 0);

            // $get_obat  = Obat::join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
            //         ->join('golongan_obat','obat.id_golongan_obat','=','golongan_obat.id_golongan_obat')
            //         ->join('pabrik_obat','obat.id_pabrik_obat','=','pabrik_obat.id_pabrik_obat')
            //         ->where('id_obat',$value->id_obat)
            //         ->firstOrFail();

            // $tanggal_pakai_dari = Pemakaian::where('id_obat',$value->id_obat)
            //                         ->whereBetween('tanggal_pemakaian',[$from,$to])
            //                         ->orderBy('tanggal_pemakaian','ASC')
            //                         ->firstOrFail()
            //                         ->tanggal_pemakaian;

            // $tanggal_pakai_sampai = Pemakaian::where('id_obat',$value->id_obat)
            //                         ->whereBetween('tanggal_pemakaian',[$from,$to])
            //                         ->orderBy('tanggal_pemakaian','DESC')
            //                         ->firstOrFail()
            //                         ->tanggal_pemakaian;


            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, date_excel($value->tanggal_pemakaian));
            $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $value->nomor_transaksi);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $value->nama_obat);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $value->nama_jenis_obat);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, $value->nama_golongan);
            $stok_pakai =  $stok_retur4[$value->id_pemakaian][$value->id_obat] ?? 0;
            $hasil = $value->stok_pakai - $stok_pakai;

            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $value->stok_pakai);




            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, -$stok_pakai);



            // $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $stok_pakai);








            // $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, -$stok_retur1  ?? '0');
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $value->harga_modal *  $hasil);


            $cell = $cell + 1;

        }
        // dd($stok_retur4);
        // dd($tes);
        // dd($stok_retur4[0][3368]);

        // dd(array_sum($stok_retur2));

        $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'Total Pakai Stok');
        $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, "=SUM(G8:G$cell)");
        $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, "=SUM(H8:H$cell)");
        $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, "=SUM(I8:I$cell)");

        $spreadsheet->getActiveSheet()->getStyle('I8:I' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $spreadsheet->getActiveSheet()->mergeCells("A$cell:F$cell");
        $spreadsheet->getActiveSheet()->getStyle("A$cell:F$cell")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $spreadsheet->createSheet();

        //new tab shpreed sheed ringkas
        $spreadsheet->setActiveSheetIndex(1)->setTitle('Ringkas');

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Pemakaian Obat Ringkas Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:F5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pakai');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jenis Obat');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Golongan Obat');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'Stok Pakai');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'Retur');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hna');

        $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                            ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                            ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
                            ->whereBetween('tanggal_pemakaian', [$from,$to])
                            // ->distinct()
                            ->groupBy('pemakaian_obat.id_obat')
                            ->orderBy('tanggal_pemakaian', 'DESC')
                            ->get();

        $cell = 8;
        $sum  = 0;
        foreach ($obat as $index => $value) {
            $sum_total_pakai = Pemakaian::getSumExportObat($from, $to, $value->id_obat);
            $count           = $index + 1;

            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, date_excel($value->tanggal_pemakaian));
            $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $value->nomor_transaksi);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $value->nama_obat);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $value->nama_jenis_obat);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, $value->nama_golongan);
            $stok_pakai = returRingkas($from, $to, $value->id_obat);
            $hasil =  $sum_total_pakai - $stok_pakai;
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $sum_total_pakai);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $stok_pakai != 0 ? -$stok_pakai : 0);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $value->harga_modal * $hasil);
            $cell = $cell + 1;
        }

        $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'Total Pakai Stok');
        $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, "=SUM(G8:G$cell)");
        $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, "=SUM(H8:H$cell)");
        $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, "=SUM(I8:I$cell)");

        $spreadsheet->getActiveSheet()->getStyle('I8:I' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $spreadsheet->getActiveSheet()->mergeCells("A$cell:F$cell");
        $spreadsheet->getActiveSheet()->getStyle("A$cell:F$cell")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanPemakaianDokter(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Pemakaian Per Dokter Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $get_dokter = Dokter::where('status_delete', 0)->whereExists(function ($query) use ($from, $to) {
            $query->select("*")
                ->from('pemakaian_obat')
                ->whereBetween('tanggal_pemakaian', [$from,$to])
                ->whereColumn('pemakaian_obat.id_dokter', 'dokter.id_dokter');
        })->get();

        $get_index_worksheet = 0;
        foreach ($get_dokter as $key => $value) {
            // dd($value);
            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($value->nama_dokter, 20));
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', $value->nama_dokter);
            $spreadsheet->getActiveSheet()->setCellValue('A6', 'Laporan Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->mergeCells('A6:J6');
            $spreadsheet->getActiveSheet()->getStyle('A1:A6')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $get_pakai = Pemakaian::getExport($from, $to, $value->id_dokter, '', 'dokter');

            $cell = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;

            foreach ($get_pakai as $index => $data) {
                $count = $index + 1;
                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, date_excel($data->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $data->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $data->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, $data->nama_jenis_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $data->harga_modal * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $data->hja_upds * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $data->hja_resep * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $data->hja_relasi * $data->stok_pakai);
                $sum  = $sum + ($data->harga_modal * $data->stok_pakai);
                $sum2 = $sum2 + ($data->hja_upds * $data->stok_pakai);
                $sum3 = $sum3 + ($data->hja_resep * $data->stok_pakai);
                $sum4 = $sum4 + ($data->hja_relasi * $data->stok_pakai);
                $cell = $cell + 1;
            }
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell . ':F' . $cell);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':F' . $cell)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($value->nama_dokter, 20));

            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Pemakaian Obat Dokter ' . $value->nama_dokter . ' Ringkas Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                                ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                                ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
                                ->where('id_dokter', $value->id_dokter)
                                ->whereBetween('tanggal_pemakaian', [$from,$to])
                                ->groupBy('pemakaian_obat.id_obat')
                                ->orderBy('tanggal_pemakaian', 'DESC')
                                ->get();

            $cell_ringkas = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            foreach ($obat as $index => $v) {
                $sum_total_pakai = Pemakaian::getSumExportObatPerDokter($from, $to, $v->id_obat, $value->id_dokter);
                $count           = $index + 1;

                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell_ringkas, date_excel($v->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell_ringkas, $v->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell_ringkas, $v->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell_ringkas, $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell_ringkas, $v->satuan_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $v->harga_modal * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $v->hja_upds * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $v->hja_resep * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $v->hja_relasi * $sum_total_pakai);
                $sum  = $sum + ($v->harga_modal * $sum_total_pakai);
                $sum2 = $sum2 + ($v->hja_upds * $sum_total_pakai);
                $sum3 = $sum3 + ($v->hja_resep * $sum_total_pakai);
                $sum4 = $sum4 + ($v->hja_relasi * $sum_total_pakai);
                $cell_ringkas = $cell_ringkas + 1;
            }

            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell_ringkas . ':F' . $cell_ringkas);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell_ringkas . ':F' . $cell_ringkas)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell_ringkas)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanPemakaianSupplier(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Pemakaian Per Supplier Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $get_supplier = Supplier::whereExists(function ($query) use ($from, $to) {
            $query->select("*")
                ->from('pemakaian_obat')
                ->whereBetween('tanggal_pemakaian', [$from,$to])
                ->whereColumn('pemakaian_obat.id_supplier', 'supplier_obat.id_supplier');
        })->get();

        $get_index_worksheet = 0;
        foreach ($get_supplier as $key => $value) {

            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($value->nama_supplier, 20));
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', $value->nama_supplier);
            $spreadsheet->getActiveSheet()->setCellValue('A6', 'Laporan Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->mergeCells('A6:J6');
            $spreadsheet->getActiveSheet()->getStyle('A1:A6')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $get_pakai = Pemakaian::getExport($from, $to, '', $value->id_supplier, 'supplier');

            $cell = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            foreach ($get_pakai as $index => $data) {
                $count = $index + 1;
                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, date_excel($data->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $data->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $data->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, $data->nama_jenis_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $data->harga_modal * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $data->hja_upds * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $data->hja_resep * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $data->hja_relasi * $data->stok_pakai);
                $sum  = $sum + ($data->harga_modal * $data->stok_pakai);
                $sum2 = $sum2 + ($data->hja_upds * $data->stok_pakai);
                $sum3 = $sum3 + ($data->hja_resep * $data->stok_pakai);
                $sum4 = $sum4 + ($data->hja_relasi * $data->stok_pakai);
                $cell = $cell + 1;
            }
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell . ':F' . $cell);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':F' . $cell)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($value->nama_supplier, 20));

            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Pemakaian Obat Supplier ' . $value->nama_supplier . ' Ringkas Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                                ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                                ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
                                ->where('id_supplier', $value->id_supplier)
                                ->whereBetween('tanggal_pemakaian', [$from,$to])
                                ->groupBy('pemakaian_obat.id_obat')
                                ->orderBy('tanggal_pemakaian', 'DESC')
                                ->get();

            $cell_ringkas = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            foreach ($obat as $index => $v) {
                $sum_total_pakai = Pemakaian::getSumExportObatPerSupplier($from, $to, $v->id_obat, $value->id_supplier);
                $count           = $index + 1;

                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell_ringkas, date_excel($v->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell_ringkas, $v->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell_ringkas, $v->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell_ringkas, $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell_ringkas, $v->satuan_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $v->harga_modal * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $v->hja_upds * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $v->hja_resep * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $v->hja_relasi * $sum_total_pakai);
                $sum  = $sum + ($v->harga_modal * $sum_total_pakai);
                $sum2 = $sum2 + ($v->hja_upds * $sum_total_pakai);
                $sum3 = $sum3 + ($v->hja_resep * $sum_total_pakai);
                $sum4 = $sum4 + ($v->hja_relasi * $sum_total_pakai);
                $cell_ringkas = $cell_ringkas + 1;
            }

            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell_ringkas . ':F' . $cell_ringkas);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell_ringkas . ':F' . $cell_ringkas)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell_ringkas)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanPemakaianPabrik(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Pemakaian Per Pabrik Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $get_pabrik = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                                    ->select('id_pabrik_obat')
                                    ->whereBetween('tanggal_pemakaian', [$from,$to])
                                    ->distinct('obat.id_pabrik_obat')
                                    ->get();

        $get_index_worksheet = 0;

        foreach ($get_pabrik as $key => $value) {
            // $get_index_worksheet = $key;

            $pabrik_model = PabrikObat::where('id_pabrik_obat', $value->id_pabrik_obat)->firstOrFail();
            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($pabrik_model->nama_pabrik, 20));
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', $pabrik_model->nama_pabrik);
            $spreadsheet->getActiveSheet()->setCellValue('A6', 'Laporan Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->mergeCells('A6:J6');
            $spreadsheet->getActiveSheet()->getStyle('A1:A6')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $get_pakai = Pemakaian::getExportByPabrik($from, $to, $value->id_pabrik_obat);

            $cell = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            foreach ($get_pakai as $index => $data) {
                $count = $index + 1;
                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, date_excel($data->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $data->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $data->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, $data->nama_jenis_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $data->harga_modal * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $data->hja_upds * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $data->hja_resep * $data->stok_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $data->hja_relasi * $data->stok_pakai);
                $sum  = $sum + ($data->harga_modal * $data->stok_pakai);
                $sum2 = $sum2 + ($data->hja_upds * $data->stok_pakai);
                $sum3 = $sum3 + ($data->hja_resep * $data->stok_pakai);
                $sum4 = $sum4 + ($data->hja_relasi * $data->stok_pakai);
                $cell = $cell + 1;
            }
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell . ':F' . $cell);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell . ':F' . $cell)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            // $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
            $spreadsheet->setActiveSheetIndex($get_index_worksheet)->setTitle(str_limit($pabrik_model->nama_pabrik, 20));
            $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
            $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Pemakaian Obat Pabrik ' . $pabrik_model->nama_pabrik . ' Ringkas Dari ' . human_date($from) . ' Sampai ' . human_date($to));
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->mergeCells('A5:J5');
            $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
            $spreadsheet->getActiveSheet()->setCellValue('B7', 'Tanggal Pemakaian');
            $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nomor Transaksi');
            $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
            $spreadsheet->getActiveSheet()->setCellValue('E7', 'Jumlah');
            $spreadsheet->getActiveSheet()->setCellValue('F7', 'Satuan');
            $spreadsheet->getActiveSheet()->setCellValue('G7', 'Hna');
            $spreadsheet->getActiveSheet()->setCellValue('H7', 'Hja UPDS');
            $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hja Resep');
            $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hja Relasi');

            $obat = Pemakaian::join('obat', 'pemakaian_obat.id_obat', '=', 'obat.id_obat')
                                ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                                ->join('golongan_obat', 'obat.id_golongan_obat', '=', 'golongan_obat.id_golongan_obat')
                                ->where('id_pabrik_obat', $value->id_pabrik_obat)
                                ->whereBetween('tanggal_pemakaian', [$from,$to])
                                ->groupBy('pemakaian_obat.id_obat')
                                ->orderBy('tanggal_pemakaian', 'DESC')
                                ->get();

            $cell_ringkas = 8;
            $sum  = 0;
            $sum2 = 0;
            $sum3 = 0;
            $sum4 = 0;
            foreach ($obat as $index => $v) {
                $sum_total_pakai = Pemakaian::getSumExportObatPerPabrik($from, $to, $v->id_obat, $value->id_pabrik_obat);
                $count           = $index + 1;

                $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, $count);
                $spreadsheet->getActiveSheet()->setCellValue('B' . $cell_ringkas, date_excel($v->tanggal_pemakaian));
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell_ringkas, $v->nomor_transaksi);
                $spreadsheet->getActiveSheet()->setCellValue('D' . $cell_ringkas, $v->nama_obat);
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell_ringkas, $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell_ringkas, $v->satuan_obat);
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $v->harga_modal * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $v->hja_upds * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $v->hja_resep * $sum_total_pakai);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $v->hja_relasi * $sum_total_pakai);
                $sum  = $sum + ($v->harga_modal * $sum_total_pakai);
                $sum2 = $sum2 + ($v->hja_upds * $sum_total_pakai);
                $sum3 = $sum3 + ($v->hja_resep * $sum_total_pakai);
                $sum4 = $sum4 + ($v->hja_relasi * $sum_total_pakai);
                $cell_ringkas = $cell_ringkas + 1;
            }

            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell_ringkas, 'Total ');
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell_ringkas, $sum);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell_ringkas, $sum2);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell_ringkas, $sum3);
            $spreadsheet->getActiveSheet()->setCellValue('J' . $cell_ringkas, $sum4);
            $spreadsheet->getActiveSheet()->mergeCells('A' . $cell_ringkas . ':F' . $cell_ringkas);
            $spreadsheet->getActiveSheet()->getStyle('A' . $cell_ringkas . ':F' . $cell_ringkas)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ]);

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

            $spreadsheet->getActiveSheet()->getStyle('G8:J' . $cell_ringkas)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->createSheet();

            $get_index_worksheet = $get_index_worksheet + 1;
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanFaktur(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Faktur Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Faktur Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:E3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:E5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Kreditur');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'DPP');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'PPN');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'JUMLAH');

        $get = PembelianDetail::supplierExport($from, $to, 'kredit');
        $cell  = 8;
        $count = 1;
        foreach ($get as $key => $value) {
            $sum_dpp = PembelianDetail::dppSupplier($from, $to, 'kredit', $value->id_supplier);
            $sum_ppn = PembelianDetail::ppnSupplier($from, $to, 'kredit', $value->id_supplier);

            $nama_supplier = Supplier::where('id_supplier', $value->id_supplier)->firstOrFail()->nama_supplier;
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $nama_supplier);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $sum_dpp);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $sum_ppn);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, '=SUM(C' . $cell . ':D' . $cell . ')');
            $count++;
            $cell++;
        }

        $sum_total = $cell - 1;
        $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'TOTAL');
        $spreadsheet->getActiveSheet()->mergeCells('A' . $cell . ':D' . $cell);
        $spreadsheet->getActiveSheet()->getStyle('A' . $cell)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, '=SUM(E8:E' . $sum_total . ')');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getStyle('C8:E' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanBeliTunai(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Beli Cash Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Beli Cash Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:E3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:E5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Kreditur');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'DPP');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'PPN');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'JUMLAH');

        $get = PembelianDetail::supplierExport($from, $to, 'cash');
        $cell  = 8;
        $count = 1;
        foreach ($get as $key => $value) {
            $sum_dpp = PembelianDetail::dppSupplier($from, $to, 'cash', $value->id_supplier);
            $sum_ppn = PembelianDetail::ppnSupplier($from, $to, 'cash', $value->id_supplier);

            $nama_supplier = Supplier::where('id_supplier', $value->id_supplier)->firstOrFail()->nama_supplier;
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $nama_supplier);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $sum_dpp);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $sum_ppn);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, '=SUM(C' . $cell . ':D' . $cell . ')');
            $count++;
            $cell++;
        }

        $sum_total = $cell - 1;
        $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, 'TOTAL');
        $spreadsheet->getActiveSheet()->mergeCells('A' . $cell . ':D' . $cell);
        $spreadsheet->getActiveSheet()->getStyle('A' . $cell)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, '=SUM(E8:E' . $sum_total . ')');

        $spreadsheet->getActiveSheet()->getStyle('C8:E' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanJatuhTempo(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Jatuh Tempo Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Jatuh Tempo Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:K1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:K3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:K5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'DISTRIBUTOR');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'No. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Tgl. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'No. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Tgl. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'DPP');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'PPN');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'JUMLAH');
        $spreadsheet->getActiveSheet()->setCellValue('J7', 'Jth. Tempo');
        $spreadsheet->getActiveSheet()->setCellValue('K7', 'Hari');
        $spreadsheet->getActiveSheet()->getStyle("A7:K7")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        // $get   = PembelianDetail::export($from,$to,'jatuh-tempo');
        $get_supplier_distinct = PembelianObat::getIdSupplierDistinct($from, $to);
        $cell       = 8;
        $cell_merge = 8;
        $count      = 1;

        foreach ($get_supplier_distinct as $key => $value) {
            $supplier    = Supplier::where('id_supplier', $value->id_supplier)->firstOrFail();
            $export_data = PembelianObat::exportJatuhTempo($from, $to, $value->id_supplier);
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $supplier->nama_supplier);
            foreach ($export_data as $index => $val) {
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $val->nomor_faktur);
                if ($val->tanggal_input == '0000-00-00') {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_terima));
                } else {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_input));
                }
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $val->kode_pembelian);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, date_excel($val->tanggal_terima));
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $val->total_dpp);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $val->total_ppn);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $val->total_semua);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, date_excel($val->tanggal_jatuh_tempo));
                $spreadsheet->getActiveSheet()->setCellValue('K' . $cell, $val->waktu_hutang);
                $spreadsheet->getActiveSheet()->getStyle("C$cell:F$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $spreadsheet->getActiveSheet()->getStyle("J$cell:K$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $cell++;
            }
            $cell--;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell_merge:A$cell");
            $spreadsheet->getActiveSheet()->mergeCells("B$cell_merge:B$cell");
            $spreadsheet->getActiveSheet()->getStyle("A$cell_merge:A$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle("B$cell_merge:B$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $cell++;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell:F$cell");
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, "=SUM(G$cell_merge:G$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, "=SUM(H$cell_merge:H$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, "=SUM(I$cell_merge:I$cell)");
            $cell++;
            $cell_merge = $cell;
            $count++;
        }

        $cell--;


        $spreadsheet->getActiveSheet()->getStyle("G8:I$cell")->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $styleTable = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
        $spreadsheet->getActiveSheet()->getStyle("A7:K$cell")->applyFromArray($styleTable);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanReturBarang(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Retur Barang Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Retur Barang Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:E1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:E3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:E5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Nomor Retur');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'Tanggal Retur');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Nama Obat');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'Stok Retur');
        $get   = ReturBarangDetail::join('retur_barang', 'retur_barang_detail.id_retur_barang', '=', 'retur_barang.id_retur_barang')
                                ->join('obat', 'retur_barang_detail.id_obat', '=', 'obat.id_obat')
                                ->whereBetween('tanggal_retur', [$from,$to])
                                ->get();

        $cell  = 8;
        $count = 1;
        foreach ($get as $key => $value) {
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $value->nomor_retur);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, human_date($value->tanggal_retur));
            $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, $value->nama_obat);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $value->stok_retur . ' ' . $value->satuan_obat);
            $count++;
            $cell++;
        }

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanKonsinyasi(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Konsinyasi Terima Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Konsinyasi Terima Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:K1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:K3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:K5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'DISTRIBUTOR');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'No. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Tgl. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'No. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Tgl. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'DPP');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'PPN');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'JUMLAH');
        $spreadsheet->getActiveSheet()->setCellValue('J7', 'Jth. Tempo');
        $spreadsheet->getActiveSheet()->setCellValue('K7', 'Hari');
        $spreadsheet->getActiveSheet()->getStyle("A7:K7")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        // $get   = PembelianDetail::export($from,$to,'jatuh-tempo');
        $get_supplier_distinct = PembelianObat::getIdSupplierKonsinyasi($from, $to, 'terima');
        $cell       = 8;
        $cell_merge = 8;
        $count      = 1;

        foreach ($get_supplier_distinct as $key => $value) {
            $supplier    = Supplier::where('id_supplier', $value->id_supplier)->firstOrFail();
            $export_data = PembelianObat::exportKonsinyasi($from, $to, $value->id_supplier, 'terima');
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $supplier->nama_supplier);
            foreach ($export_data as $index => $val) {
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $val->nomor_faktur);
                if ($val->tanggal_input == '0000-00-00') {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_terima));
                } else {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_input));
                }
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $val->kode_pembelian);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, date_excel($val->tanggal_terima));
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $val->total_dpp);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $val->total_ppn);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $val->total_semua);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, date_excel($val->tanggal_jatuh_tempo));
                $spreadsheet->getActiveSheet()->setCellValue('K' . $cell, $val->waktu_hutang);
                $spreadsheet->getActiveSheet()->getStyle("C$cell:F$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $spreadsheet->getActiveSheet()->getStyle("J$cell:K$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $cell++;
            }
            $cell--;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell_merge:A$cell");
            $spreadsheet->getActiveSheet()->mergeCells("B$cell_merge:B$cell");
            $spreadsheet->getActiveSheet()->getStyle("A$cell_merge:A$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle("B$cell_merge:B$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $cell++;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell:F$cell");
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, "=SUM(G$cell_merge:G$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, "=SUM(H$cell_merge:H$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, "=SUM(I$cell_merge:I$cell)");
            $cell++;
            $cell_merge = $cell;
            $count++;
        }

        $cell--;


        $spreadsheet->getActiveSheet()->getStyle("G8:I$cell")->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $styleTable = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
        $spreadsheet->getActiveSheet()->getStyle("A7:K$cell")->applyFromArray($styleTable);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }

    public function laporanKonsinyasiJatuhTempo(Request $request)
    {
        ob_end_clean();
        $from    = reverse_date($request->from);
        $to      = reverse_date($request->to);
        $title   = 'Laporan Konsinyasi Jatuh Tempo Dari Tanggal ' . human_date($from) . ' Sampai Tanggal ' . human_date($to);
        $profile = ProfileInstansi::firstOrFail();

        $fileName = $title . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5', 'Laporan Konsinyasi Jatuh Tempo Dari ' . human_date($from) . ' Sampai ' . human_date($to));
        $spreadsheet->getActiveSheet()->mergeCells('A1:K1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:K3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:K5');
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'DISTRIBUTOR');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'No. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Tgl. Faktur');
        $spreadsheet->getActiveSheet()->setCellValue('E7', 'No. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Tgl. Terima');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'DPP');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'PPN');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'JUMLAH');
        $spreadsheet->getActiveSheet()->setCellValue('J7', 'Jth. Tempo');
        $spreadsheet->getActiveSheet()->setCellValue('K7', 'Hari');
        $spreadsheet->getActiveSheet()->getStyle("A7:K7")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        // $get   = PembelianDetail::export($from,$to,'jatuh-tempo');
        $get_supplier_distinct = PembelianObat::getIdSupplierKonsinyasi($from, $to, 'jatuh-tempo');
        $cell       = 8;
        $cell_merge = 8;
        $count      = 1;

        foreach ($get_supplier_distinct as $key => $value) {
            $supplier    = Supplier::where('id_supplier', $value->id_supplier)->firstOrFail();
            $export_data = PembelianObat::exportKonsinyasi($from, $to, $value->id_supplier, 'jatuh-tempo');
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $cell, $supplier->nama_supplier);
            foreach ($export_data as $index => $val) {
                $spreadsheet->getActiveSheet()->setCellValue('C' . $cell, $val->nomor_faktur);
                if ($val->tanggal_input == '0000-00-00') {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_terima));
                } else {
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $cell, date_excel($val->tanggal_input));
                }
                $spreadsheet->getActiveSheet()->setCellValue('E' . $cell, $val->kode_pembelian);
                $spreadsheet->getActiveSheet()->setCellValue('F' . $cell, date_excel($val->tanggal_terima));
                $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, $val->total_dpp);
                $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, $val->total_ppn);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, $val->total_semua);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $cell, date_excel($val->tanggal_jatuh_tempo));
                $spreadsheet->getActiveSheet()->setCellValue('K' . $cell, $val->waktu_hutang);
                $spreadsheet->getActiveSheet()->getStyle("C$cell:F$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $spreadsheet->getActiveSheet()->getStyle("J$cell:K$cell")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $cell++;
            }
            $cell--;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell_merge:A$cell");
            $spreadsheet->getActiveSheet()->mergeCells("B$cell_merge:B$cell");
            $spreadsheet->getActiveSheet()->getStyle("A$cell_merge:A$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $spreadsheet->getActiveSheet()->getStyle("B$cell_merge:B$cell")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);
            $cell++;
            $spreadsheet->getActiveSheet()->mergeCells("A$cell:F$cell");
            $spreadsheet->getActiveSheet()->setCellValue('G' . $cell, "=SUM(G$cell_merge:G$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('H' . $cell, "=SUM(H$cell_merge:H$cell)");
            $spreadsheet->getActiveSheet()->setCellValue('I' . $cell, "=SUM(I$cell_merge:I$cell)");
            $cell++;
            $cell_merge = $cell;
            $count++;
        }

        $cell--;


        $spreadsheet->getActiveSheet()->getStyle("G8:I$cell")->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $styleTable = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
        $spreadsheet->getActiveSheet()->getStyle("A7:K$cell")->applyFromArray($styleTable);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }
}
