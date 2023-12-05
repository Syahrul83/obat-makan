<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JamShiftModel as JamShift;

class JamShiftController extends Controller
{
    public function index() 
    {
        $title = 'Data Jam Shift | Admin';
        $link  = 'data-master';
        $page  = 'jam-shift';
        return view('Admin.jam-shift.main',compact('title','link','page'));
    }

    public function tambah() 
    {
        $title = 'Form Jam Shift | Admin';
        $link  = 'data-master';
        $page  = 'jam-shift';
        return view('Admin.jam-shift.form-jam-shift',compact('title','link','page'));
    }

    public function edit($id) 
    {
        $title = 'Form Jam Shift | Admin';
        $link  = 'data-master';
        $page  = 'jam-shift';
        $row   = JamShift::where('id_jam_shift',$id)->firstOrFail();
        return view('Admin.jam-shift.form-jam-shift',compact('title','link','page','row'));
    }

    public function delete($id) 
    {
        JamShift::where('id_jam_shift',$id)->update(['status_delete'=>1]);
        return redirect('/admin/jam-shift')->with('message','Berhasil Hapus Jenis Obat');
    }

    public function save(Request $request) 
    {
        $dari_jam   = $request->dari_jam;
        $sampai_jam = $request->sampai_jam;
        $ket_shift  = $request->ket_shift;
        $id         = $request->id;

        $array = [
            'jam_awal'      => $dari_jam,
            'jam_akhir'     => $sampai_jam,
            'ket_shift'     => $ket_shift,
            'status_delete' => 0
        ];
        if ($id == '') {
            JamShift::create($array);
            $message = 'Berhasil Input Jam Shift';
        }
        else {
            JamShift::where('id_jam_shift',$id)->update($array);
            $message = 'Berhasil Update Jam Shift';
        }

        return redirect('/admin/jam-shift')->with('message',$message);
    }
}
