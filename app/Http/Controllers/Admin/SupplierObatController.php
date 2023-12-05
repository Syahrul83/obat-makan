<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SupplierModel as Supplier;

class SupplierObatController extends Controller
{
    public function index() 
    {
        $title = 'Data Supplier | Admin';
        $link  = 'obat';
        $page  = 'data-supplier-obat';
        return view('Admin.supplier-obat.main',compact('title','link','page'));
    }

    public function tambah() 
    {
        $title = 'Form Supplier | Admin';
        $link  = 'obat';
        $page  = 'data-supplier-obat';
        return view('Admin.supplier-obat.form-supplier',compact('title','link','page'));
    }

    public function edit($id) 
    {
        $title = 'Form Supplier | Admin';
        $link  = 'obat';
        $page  = 'data-supplier-obat';
        $row   = Supplier::where('id_supplier',$id)->firstOrFail();
        return view('Admin.supplier-obat.form-supplier',compact('title','link','page','row'));
    }

    public function delete($id) 
    {
        Supplier::where('id_supplier',$id)->update(['status_delete' => 1]);
        return redirect('/admin/data-supplier-obat')->with('message','Berhasil Hapus Supplier');
    }

    public function save(Request $request) 
    {
        $nama_supplier      = $request->nama_supplier;
        $singkatan_supplier = $request->singkatan_supplier;
        $nomor_hp           = $request->nomor_hp;
        $alamat_supplier    = $request->alamat_supplier;
        $id                 = $request->id;

        $array = [
            'nama_supplier'      => $nama_supplier,
            'singkatan_supplier' => $singkatan_supplier,
            'nomor_telepon'      => $nomor_hp,
            'alamat_supplier'    => $alamat_supplier,
            'status_delete'      => 0
        ];

        if ($id == '') {
            Supplier::create($array);
            $message = 'Berhasil Input Supplier';
        }
        else {
            Supplier::where('id_supplier',$id)->update($array);
            $message = 'Berhasil Update Supplier';
        }

        return redirect('/admin/data-supplier-obat')->with('message',$message);
    }
}
