<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ObatModel as Obat;
use App\Models\ObatDetailModel as ObatDetail;
use App\Models\JenisObatModel as JenisObat;
use App\Models\SupplierModel as Supplier;
use App\Models\KomposisiObatModel as KomposisiObat;
use App\Models\GolonganObatModel as GolonganObat;
use App\Models\PabrikObatModel as PabrikObat;
use App\Models\MarginObatModel as MarginObat;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use App\Models\PersenPpnModel as PersenPpn;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ObatController extends Controller
{
    public function index()
    {
        $title = 'Data Obat';
        $link = 'obat';
        $page = 'data-obat';
        return view('Admin.data-obat.main', compact('title', 'page', 'link'));
    }

    public function tambah()
    {
        $title = 'Data Obat';
        $link = 'obat';
        $page = 'data-obat';
        $pabrik_obat = PabrikObat::where('status_delete', 0)->get();
        $supplier_obat = Supplier::where('status_delete', 0)->get();
        $jenis_obat = JenisObat::where('status_delete', 0)->get();
        $golongan_obat = GolonganObat::where('status_delete', 0)->get();
        $margin_obat = MarginObat::firstOrFail();
        $nilai_ppn = PersenPpn::firstOrFail();
        return view(
            'Admin.data-obat.form-obat',
            compact(
                'title',
                'page',
                'link',
                'jenis_obat',
                'supplier_obat',
                'pabrik_obat',
                'golongan_obat',
                'margin_obat',
                'nilai_ppn'
            )
        );
    }

    public function edit($id)
    {
        $title = 'Form Data Obat';
        $link = 'obat';
        $page = 'data-obat';
        $pabrik_obat = PabrikObat::where('status_delete', 0)->get();
        $supplier_obat = Supplier::where('status_delete', 0)->get();
        $jenis_obat = JenisObat::where('status_delete', 0)->get();
        $golongan_obat = GolonganObat::where('status_delete', 0)->get();
        $obat_detail = new ObatDetail();
        $komposisi_obat = KomposisiObat::where('id_obat', $id)->get();
        $row = Obat::where('id_obat', $id)->firstOrFail();
        $margin_obat = MarginObat::firstOrFail();
        $nilai_ppn = PersenPpn::firstOrFail();
        return view(
            'Admin.data-obat.form-obat',
            compact(
                'title',
                'link',
                'page',
                'jenis_obat',
                'supplier_obat',
                'row',
                'obat_detail',
                'id',
                'komposisi_obat',
                'pabrik_obat',
                'golongan_obat',
                'margin_obat',
                'nilai_ppn'
            )
        );
    }

    public function delete($id)
    {
        Obat::where('id_obat', $id)->update(['status_delete' => 1]);
        return redirect('/admin/data-obat')->with(
            'message',
            'Berhasil Hapus Obat'
        );
    }

    public function save(Request $request)
    {
        $nama_obat = $request->nama_obat;
        $supplier_obat = $request->supplier_obat;
        $pabrik_obat = $request->pabrik_obat;
        $golongan_obat = $request->golongan_obat;
        $dosis_satuan = $request->dosis_satuan;
        $jenis_obat = $request->jenis_obat;
        $tanggal_expired = reverse_date($request->tanggal_expired);
        $harga_modal = replace_comma_to_dot($request->harga_modal);
        $harga_modal_ppn = replace_comma_to_dot($request->harga_modal_ppn);
        $hja_upds = replace_comma_to_dot($request->hja_upds);
        $hja_resep = replace_comma_to_dot($request->hja_resep);
        $hja_relasi = replace_comma_to_dot($request->hja_relasi);
        $kunci_hja_upds = $request->kunci_hja_upds ?? 0;
        $kunci_hja_resep = $request->kunci_hja_resep ?? 0;
        $kunci_hja_relasi = $request->kunci_hja_relasi ?? 0;
        $stok_obat = $request->stok_obat;
        $satuan_obat = $request->satuan_obat;
        $komposisi_obat = $request->komposisi_obat;
        $takaran_komposisi = $request->takaran_komposisi;
        $tanggal_input = date('Y-m-d');
        $id = $request->id;

        $first = JenisObat::where('id_jenis_obat', $jenis_obat)->firstOrFail();
        $kode_obat = makeAcronym($first->nama_jenis_obat);

        $data_obat = [
            'kode_obat' => generateCode(
                'OMF-' . $kode_obat,
                '-',
                Obat::lastNumCode(),
                11
            ),
            'nama_obat' => $nama_obat,
            'dosis_satuan' => $dosis_satuan,
            'id_jenis_obat' => $jenis_obat,
            'id_pabrik_obat' => $pabrik_obat,
            'id_golongan_obat' => $golongan_obat,
            'tanggal_expired' => $tanggal_expired,
            'harga_modal' => $harga_modal,
            'harga_modal_ppn' => $harga_modal_ppn,
            'hja_upds' => $hja_upds,
            'hja_resep' => $hja_resep,
            'hja_relasi' => $hja_relasi,
            'kunci_hja_upds' => $kunci_hja_upds,
            'kunci_hja_resep' => $kunci_hja_resep,
            'kunci_hja_relasi' => $kunci_hja_relasi,
            'stok_obat' => $stok_obat,
            'satuan_obat' => $satuan_obat,
            'tanggal_input' => $tanggal_input,
        ];

        if ($id == '') {
            $data_obat['status_delete'] = 0;
            $id_obat = Obat::insertGetId($data_obat);

            foreach ($supplier_obat as $key => $value) {
                $data_obat_detail[] = [
                    'id_obat' => $id_obat,
                    'id_supplier' => $value,
                ];
            }
            ObatDetail::insert($data_obat_detail);

            foreach ($komposisi_obat as $key => $value) {
                $data_komposisi_obat[] = [
                    'id_obat' => $id_obat,
                    'nama_komposisi' => $komposisi_obat[$key],
                    'takaran_komposisi' => $takaran_komposisi[$key],
                ];
            }
            KomposisiObat::insert($data_komposisi_obat);

            $message = 'Berhasil Input Data Obat';
        } else {
            unset($data_obat['kode_obat']);
            Obat::where('id_obat', $id)->update($data_obat);
            ObatDetail::where('id_obat', $id)->delete();
            KomposisiObat::where('id_obat', $id)->delete();

            foreach ($supplier_obat as $key => $value) {
                $data_obat_detail[] = [
                    'id_obat' => $id,
                    'id_supplier' => $value,
                ];
            }
            ObatDetail::insert($data_obat_detail);

            foreach ($komposisi_obat as $key => $value) {
                $data_komposisi_obat[] = [
                    'id_obat' => $id,
                    'nama_komposisi' => $komposisi_obat[$key],
                    'takaran_komposisi' => $takaran_komposisi[$key],
                ];
            }
            KomposisiObat::insert($data_komposisi_obat);

            $message = 'Berhasil Update Data Obat';
        }

        return redirect('/admin/data-obat')->with('message', $message);
    }

    public function obatDetail($id)
    {
        $title = 'Data Obat';
        $link = 'obat';
        $page = 'data-obat';
        return view(
            'Admin.data-obat.obat-detail',
            compact('title', 'page', 'link', 'id')
        );
    }

    public function deleteObatDetail($id, $id_detail)
    {
        ObatDetail::where('id_obat_detail', $id_detail)->delete();
        return redirect('/admin/data-obat/lihat-supplier/' . $id)->with(
            'message',
            'Berhasil Hapus Data'
        );
    }

    public function komposisiObat($id)
    {
        $title = 'Data Komposisi Obat';
        $link = 'obat';
        $page = 'data-obat';
        return view(
            'Admin.data-obat.komposisi-obat',
            compact('title', 'link', 'page', 'id')
        );
    }

    public function deleteKomposisiObat($id, $id_detail)
    {
        KomposisiObat::where('id_komposisi_obat', $id_detail)->delete();
        return redirect('/admin/data-obat/komposisi-obat/' . $id)->with(
            'message',
            'Berhasil Hapus Data'
        );
    }

    public function rekapObat(Request $request)
    {
        ob_end_clean();
        $from = reverse_date($request->from);
        $to = reverse_date($request->to);

        $title = 'Rekap Obat ' . human_date($from) . ' - ' . human_date($to);
        $fileName = $title . '.xlsx';

        $profile = ProfileInstansi::firstOrFail();
        $spreadsheet = new Spreadsheet();

        $spreadsheet
            ->getActiveSheet()
            ->setCellValue('A1', $profile->nama_instansi);
        $spreadsheet
            ->getActiveSheet()
            ->setCellValue('A3', $profile->alamat_instansi);
        $spreadsheet
            ->getActiveSheet()
            ->setCellValue(
                'A4',
                'Tanggal : ' . human_date($from) . ' - ' . human_date($to)
            );
        $spreadsheet->getActiveSheet()->setCellValue('A5', $title);
        $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:G3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:G5');
        $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1:A5')
            ->applyFromArray([
                'alignment' => [
                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

        $spreadsheet->getActiveSheet()->setCellValue('A7', 'No.');
        $spreadsheet->getActiveSheet()->setCellValue('B7', 'Pabrik Obat');
        $spreadsheet->getActiveSheet()->setCellValue('C7', 'Nama Obat');
        $spreadsheet->getActiveSheet()->setCellValue('D7', 'Golongan Obat');
        $spreadsheet
            ->getActiveSheet()
            ->setCellValue('E7', 'Bentuk Sediaan Obat');
        $spreadsheet->getActiveSheet()->setCellValue('F7', 'Tanggal Expired');
        $spreadsheet->getActiveSheet()->setCellValue('G7', 'Stok Obat');
        $spreadsheet->getActiveSheet()->setCellValue('H7', 'Dosis Satuan');
        $spreadsheet->getActiveSheet()->setCellValue('I7', 'Hna');
        $spreadsheet->getActiveSheet()->setCellValue('J7', 'Hna+PPn');
        $spreadsheet->getActiveSheet()->setCellValue('K7', 'Hja UPDS');
        $spreadsheet->getActiveSheet()->setCellValue('L7', 'Hja Resep');
        $spreadsheet->getActiveSheet()->setCellValue('M7', 'Hja Relasi');

        //     $get = Obat::join(
        //         'jenis_obat',
        //         'obat.id_jenis_obat',
        //         '=',
        //         'jenis_obat.id_jenis_obat'
        //     )
        //         ->join(
        //             'golongan_obat',
        //             'obat.id_golongan_obat',
        //             '=',
        //             'golongan_obat.id_golongan_obat'
        //         )
        //         ->join(
        //             'pabrik_obat',
        //             'obat.id_pabrik_obat',
        //             '=',
        //             'pabrik_obat.id_pabrik_obat'
        //         )
        //         ->where('obat.status_delete', 0)
        //         ->whereBetween('obat.tanggal_input', [$from, $to])
        //         ->get();

        $get = Obat::join(
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
            ->where('obat.status_delete', 0);

        if ($request->rekap != 1) {
            $get->whereBetween('obat.tanggal_input', [$from, $to]);
        }

        $get = $get->get();

        $cell = 8;
        $count = 1;
        foreach ($get as $key => $value) {
            $spreadsheet->getActiveSheet()->setCellValue('A' . $cell, $count);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('B' . $cell, $value->nama_pabrik);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('C' . $cell, $value->nama_obat);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('D' . $cell, $value->nama_golongan);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('E' . $cell, $value->nama_jenis_obat);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue(
                    'F' . $cell,
                    human_date($value->tanggal_expired)
                );
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue(
                    'G' . $cell,
                    $value->stok_obat . ' ' . $value->satuan_obat
                );
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('H' . $cell, $value->dosis_satuan);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('I' . $cell, $value->harga_modal);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('J' . $cell, $value->harga_modal_ppn);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('K' . $cell, $value->hja_upds);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('L' . $cell, $value->hja_resep);
            $spreadsheet
                ->getActiveSheet()
                ->setCellValue('M' . $cell, $value->hja_relasi);
            $count++;
            $cell++;
        }

        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('A')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('B')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('C')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('D')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('E')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('F')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('G')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('H')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('I')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('J')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('K')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('L')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('M')
            ->setAutoSize(true);

        $spreadsheet
            ->getActiveSheet()
            ->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );
        $spreadsheet
            ->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            );

        $writer = new Xlsx($spreadsheet);
        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
    }
}
