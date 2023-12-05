<?php

namespace App\Http\Controllers\Admin;

use App\Models\Debitur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DebiturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data Debitur';
        $link  = 'data-master';
        $page  = 'data-debitur';
        return view('Admin.debitur.main', compact('title', 'link', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Form Tambah Data Debitur';
        $link  = 'data-master';
        $page  = 'data-debitur';

        return view('Admin.debitur.form-tambah-debitur', compact('title', 'link', 'page'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (empty($request->id)) {
            Debitur::create($request->all());
            $message = 'Berhasil Input Data Debitur';
        } else {
            Debitur::where('id', $request->id)->update($request->except(['_token']));
            $message = 'Berhasil Update Data Debitur';
        }
        return redirect('/admin/data-debitur')->with('message', $message);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Form Data Debitur';
        $link  = 'data-master';
        $page  = 'data-debitur';
        $row   = Debitur::where('id', $id)->firstOrFail();
        return view('admin.debitur.form-debitur', compact('title', 'link', 'page', 'row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Debitur::where('id', $id)->update(['status_delete' => 1]);
        return redirect('/admin/data-debitur')->with('message', 'Berhasil Hapus Pasien');
    }
}
