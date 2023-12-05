<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StokOpnemModel as StokOpnem;
use App\Models\StokOpnemDetailModel as StokOpnemDetail;
use App\Models\ObatModel as Obat;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
// use Illuminate\Support\Collection;

class StokOpnemController extends Controller
{
    public function index() 
    {
        $title = 'Data Stok Opnem | Admin';
        $page  = 'stok-opnem';
        return view('Admin.stok-opnem.main',compact('title','page'));
    }

    public function tambah() 
    {
        $title       = 'Form Stok Opnem | Admin';
        $page        = 'stok-opnem';
        $paging_obat = new Obat;
        // $uuid     = generateUuid();
        $count_obat  = Obat::where('status_delete',0)->count();
        return view('Admin.stok-opnem.form-stok-opnem',compact('title','page','paging_obat','count_obat'));
    }

    public function lanjutInput($id)
    {
        $title = 'Form Stok Opnem | Admin';
        $page  = 'stok-opnem';
        $obat  = Obat::whereNotExists(function($query)use($id){
            $query->from('stok_opnem_detail')
                ->where('id_stok_opnem',$id)
                ->whereColumn('stok_opnem_detail.id_obat','obat.id_obat');
        })->where('status_delete',0)->get();

        $tanggal_stok_opnem = StokOpnem::where('id_stok_opnem',$id)->firstOrFail()->tanggal_stok_opnem;
        $keterangan         = StokOpnem::where('id_stok_opnem',$id)->firstOrFail()->keterangan;

        return view('Admin.stok-opnem.form-stok-opnem',compact('title','page','obat','tanggal_stok_opnem','keterangan','id'));
    }

    public function delete($id) 
    {
        StokOpnem::where('id_stok_opnem',$id)->delete();
        return redirect('/admin/stok-opnem')->with('message','Berhasil Hapus Stok Opnem');
    }

    public function save(Request $request) 
    {
        $tanggal_stok_opnem = reverse_date($request->tanggal_stok_opnem);
        // $keterangan         = $request->keterangan;
        // $btn_act            = $request->btn_act;
        $id                 = $request->id;
        // $uuid               = $request->uuid;
        // dd($tanggal_stok_opnem);
        // $obat          = $request->id_obat;
        // $stok_komputer = $request->stok_komputer;
        // $stok_fisik    = $request->stok_fisik;
        // $stok_selisih  = $request->stok_selisih;
        // $sub_nilai     = $request->sub_nilai;

        // $total_nilai = 0;

        // if ($btn_act == 'input-obat') {
        //     $data_stok_opnem = [
        //         'tanggal_stok_opnem' => $tanggal_stok_opnem,
        //         'keterangan'         => $keterangan,
        //         'status_input'       => 0,
        //         'created_at'         => date('Y-m-d H:i:s'),
        //         'updated_at'         => date('Y-m-d H:i:s')
        //     ];

        //     if ($id == '') {
        //         $id_stok_opnem = StokOpnem::insertGetId($data_stok_opnem);
        //         $id = $id_stok_opnem;
        //     }

        //     foreach ($obat as $key => $value) {
        //         if (isset($stok_fisik[$key])) {
        //             $data_detail_stok_opnem[] = [
        //                 'id_stok_opnem' => $id,
        //                 'id_obat'       => $obat[$key],
        //                 'stok_komputer' => $stok_komputer[$key],
        //                 'stok_fisik'    => $stok_fisik[$key],
        //                 'stok_selisih'  => $stok_selisih[$key],
        //                 'sub_nilai'     => $sub_nilai[$key]
        //             ];   
        //             // $total_nilai = $total_nilai+$sub_nilai[$key];
        //         }
        //     }

        //     StokOpnemDetail::insert($data_detail_stok_opnem);

        //     $get_detail_stok = StokOpnemDetail::join('obat','stok_opnem_detail.id_obat','=','obat.id_obat')
        //                                     ->where('id_stok_opnem',$id)->get();

        //     // $total_nilai = StokOpnemDetail::where('id_stok_opnem',$id)->sum('sub_nilai');

        //     session()->put($id,$uuid);

        //     $message = 'Berhasil Input Stok Opnem';
            
        //     return redirect('/admin/stok-opnem/lanjut-input/'.$id)->with('message',$message);
        // }
        // else {
        //     $data_stok_opnem = [
        //         'tanggal_stok_opnem' => $tanggal_stok_opnem,
        //         'keterangan'         => $keterangan,
        //         'status_input'       => 1,
        //         'created_at'         => date('Y-m-d H:i:s'),
        //         'updated_at'         => date('Y-m-d H:i:s')
        //     ];

        //     if ($id == '') {
        //         $id_stok_opnem = StokOpnem::insertGetId($data_stok_opnem);
        //         $id = $id_stok_opnem;
        //     }
        //     else {
        //         StokOpnem::where('id_stok_opnem',$id)->update($data_stok_opnem);
        //     }

        //     foreach ($obat as $key => $value) {
        //         if (isset($stok_fisik[$key])) {
        //             $data_detail_stok_opnem[] = [
        //                 'id_stok_opnem' => $id,
        //                 'id_obat'       => $obat[$key],
        //                 'stok_komputer' => $stok_komputer[$key],
        //                 'stok_fisik'    => $stok_fisik[$key],
        //                 'stok_selisih'  => $stok_selisih[$key],
        //                 'sub_nilai'     => $sub_nilai[$key]
        //             ];
        //         }
        //     }

        //     StokOpnemDetail::insert($data_detail_stok_opnem);

        //     $get_detail_stok = StokOpnemDetail::join('obat','stok_opnem_detail.id_obat','=','obat.id_obat')
        //                                     ->where('id_stok_opnem',$id)->get();

        //     $total_nilai = StokOpnemDetail::where('id_stok_opnem',$id)->sum('sub_nilai');
            
        //     $message = 'Berhasil Input Stok Opnem';

        //     return view('Admin.stok-opnem.stok-opnem-print',compact('tanggal_stok_opnem','total_nilai','get_detail_stok'));
        // }
        // StokOpnem::where('id_stok_opnem',$id)->update(['status_input' => 1]);
        $get_detail_stok = StokOpnemDetail::join('obat','stok_opnem_detail.id_obat','=','obat.id_obat')
                                        ->where('id_stok_opnem',$id)->get();

        $total_nilai = StokOpnemDetail::where('id_stok_opnem',$id)->sum('sub_nilai');
        
        // $message = 'Berhasil Input Stok Opnem';

        return view('Admin.stok-opnem.stok-opnem-print',compact('tanggal_stok_opnem','total_nilai','get_detail_stok'));
    }
    
    public function detail($id) 
    {
        $title = 'Data Detail Stok Opnem | Admin';
        $page  = 'stok-opnem';
        return view('Admin.stok-opnem.main-detail',compact('title','page','id'));
    }

    public function cetak($id)
    {
        $tanggal_stok_opnem = StokOpnem::where('id_stok_opnem',$id)->firstOrFail()->tanggal_stok_opnem;
        $get_detail_stok = StokOpnemDetail::join('obat','stok_opnem_detail.id_obat','=','obat.id_obat')
                                        ->where('id_stok_opnem',$id)->get();
        $total_nilai = StokOpnemDetail::where('id_stok_opnem',$id)->sum('sub_nilai');

        return view('Admin.stok-opnem.stok-opnem-cetak',compact('tanggal_stok_opnem','total_nilai','get_detail_stok'));
    }

    // public function selesaiInput()
    // {
    //     StokOpnem::query()->update(['status_input' => 1]);

    //     return redirect('/admin/stok-opnem')->with('message','Berhasil Ubah Status Input');
    // }

    public function inputSebagian(Request $request)
    {
        $obat               = $request->id_obat;
        $stok_fisik         = $request->stok_fisik;
        $stok_komputer      = $request->stok_komputer;
        $stok_selisih       = $request->stok_selisih;
        $sub_nilai          = $request->sub_nilai;
        $tanggal_stok_opnem = reverse_date($request->tanggal_stok_opnem);
        // dd($request->tanggal_stok_opnem);
        $keterangan         = $request->keterangan;
        $id_stok_opnem      = $request->id_stok_opnem;

        if ($id_stok_opnem == '' || $id_stok_opnem == null) {
            $data_stok_opnem = [
                'tanggal_stok_opnem' => $tanggal_stok_opnem,
                'keterangan'         => $keterangan,
                'status_input'       => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s')
            ];
            $id = StokOpnem::insertGetId($data_stok_opnem);
            foreach ($obat as $key => $value) {
                // if (isset($stok_fisik[$key]) || $stok_fisik[$key] != '' || $stok_fisik[$key] != null) {

                if (isset($stok_fisik[$key]) || $stok_fisik[$key] != '' || $stok_fisik[$key] != null) {
                    $stok_fisik__   = $stok_fisik[$key];
                    $stok_selisih__ = $stok_selisih[$key];
                    $sub_nilai__    = $sub_nilai[$key];
                }
                else {
                    $stok_fisik__   = 0;
                    $stok_selisih__ = $stok_komputer[$key] - $stok_fisik__;
                    $sub_nilai__    = 0;
                }
                
                // }
				$cek_obat = StokOpnemDetail::where('id_stok_opnem',$id)
											->where('id_obat',$obat[$key])
											->count();
											
				if($cek_obat == 0) {
					
					$data_stok_opnem_detail = [
						'id_stok_opnem' => $id,
						'id_obat'       => $obat[$key],
						'stok_komputer' => $stok_komputer[$key],
						'stok_fisik'    => $stok_fisik__,
						'stok_selisih'  => $stok_selisih__,
						'sub_nilai'     => $sub_nilai__
					];
					
					StokOpnemDetail::create($data_stok_opnem_detail);
				}
            }

            $id_stok_opnem = $id;
        }
        else {
            foreach ($obat as $key => $value) {
                if (isset($stok_fisik[$key]) || $stok_fisik[$key] != '' || $stok_fisik[$key] != null) {
                    $stok_fisik__   = $stok_fisik[$key];
                    $stok_selisih__ = $stok_selisih[$key];
                    $sub_nilai__    = $sub_nilai[$key];
                }
                else {
                    $stok_fisik__   = 0;
                    $stok_selisih__ = $stok_komputer[$key] - $stok_fisik__;
                    $sub_nilai__    = 0;
                }
				
				
				$cek_obat = StokOpnemDetail::where('id_stok_opnem',$id_stok_opnem)
											->where('id_obat',$obat[$key])
											->count();
											
				if($cek_obat == 0) {
					
					$data_stok_opnem_detail = [
						'id_stok_opnem' => $id_stok_opnem,
						'id_obat'       => $obat[$key],
						'stok_komputer' => $stok_komputer[$key],
						'stok_fisik'    => $stok_fisik__,
						'stok_selisih'  => $stok_selisih__,
						'sub_nilai'     => $sub_nilai__
					];
					
					StokOpnemDetail::create($data_stok_opnem_detail);
				}
            }

            //StokOpnemDetail::insert($data_stok_opnem_detail);
        }

        return response()->json(['message' => 'Berhasil Input Stok Opnem','id_stok_opnem' => $id_stok_opnem]);
    }

    public function export($id)
    {
        $stok_opnem_row = StokOpnem::where('id_stok_opnem',$id)->firstOrFail();
        $title          = 'Export Stok Opnem Tanggal '.human_date($stok_opnem_row->tanggal_stok_opnem);
        $profile        = ProfileInstansi::firstOrFail();

        $fileName = $title.'.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('A1',$profile->nama_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A3',$profile->alamat_instansi);
        $spreadsheet->getActiveSheet()->setCellValue('A5','Export Stok Opnem Tanggal '.human_date($stok_opnem_row->tanggal_stok_opnem));
        $spreadsheet->getActiveSheet()->setCellValue('A7',$stok_opnem_row->keterangan);
        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:I3');
        $spreadsheet->getActiveSheet()->mergeCells('A5:I5');
        $spreadsheet->getActiveSheet()->mergeCells('A7:I7');
        $spreadsheet->getActiveSheet()->getStyle('A1:A7')->applyFromArray([
            'alignment'=>[
                'horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $spreadsheet->getActiveSheet()->setCellValue('A8','No.');
        $spreadsheet->getActiveSheet()->setCellValue('B8','Nama Obat');
        $spreadsheet->getActiveSheet()->setCellValue('C8','Satuan Obat');
        $spreadsheet->getActiveSheet()->setCellValue('D8','Hna');
        $spreadsheet->getActiveSheet()->setCellValue('E8','S.K');
        $spreadsheet->getActiveSheet()->setCellValue('F8','S.F');
        $spreadsheet->getActiveSheet()->setCellValue('G8','S.S');
        $spreadsheet->getActiveSheet()->setCellValue('H8','Nilai');
        $spreadsheet->getActiveSheet()->setCellValue('I8','Tanggal Exp');

        $get = StokOpnemDetail::join('obat','stok_opnem_detail.id_obat','=','obat.id_obat')
                                        ->where('id_stok_opnem',$id)->get();

        $cell  = 9;
        $count = 1;
        foreach ($get as $key => $value) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$cell,$count);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$cell,$value->nama_obat);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$cell,$value->satuan_obat);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$cell,$value->harga_modal);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$cell,$value->stok_komputer);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$cell,$value->stok_fisik);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$cell,$value->stok_selisih);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$cell,$value->sub_nilai);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$cell,date_excel($value->tanggal_expired));
            $count++;
            $cell++;
        }
        $spreadsheet->getActiveSheet()->setCellValue('A'.$cell,'Jumlah');
        $spreadsheet->getActiveSheet()->setCellValue('H'.$cell,'=(SUM(H9:H'.$cell.'))');

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);


        $spreadsheet->getActiveSheet()->getStyle("D9:D$cell")->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $spreadsheet->getActiveSheet()->getStyle("H9:H$cell")->getNumberFormat()->setFormatCode('"Rp "#,##0.00_-');
        $styleTable = ['borders'=>['allBorders'=>['borderStyle'=>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
        $spreadsheet->getActiveSheet()->getStyle("A8:I$cell")->applyFromArray($styleTable);
        
        $spreadsheet->getActiveSheet()->mergeCells("A$cell:G$cell");
        $spreadsheet->getActiveSheet()->mergeCells("H$cell:I$cell");
        $spreadsheet->getActiveSheet()->getStyle("A$cell:G$cell")->applyFromArray([
            'alignment'=>[
                'horizontal'=>\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        $writer->save('php://output');
    }
}
