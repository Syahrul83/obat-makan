@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Form Pasien</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible">
                    {{session('message')}} <button class="close" data-dismiss="alert">X</button>
                </div>
                @endif
                <div class="box box-default">
                    <div class="box-header with-border">
                        <a href="{{ url('/admin/data-debitur') }}">
                            <button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
                        </a>
                    </div>
                    <form action="{{url('/admin/data-debitur/save')}}" method="POST">
                        {{csrf_field()}}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Nama Debitur</label>
                                <input type="text" name="name" class="form-control" value="" placeholder="nama debitur" required="required">
                            </div>
                            <div class="form-group">
                                <label for="">margin</label>
                                <input type="number" name="margin" class="form-control" value="" placeholder="margin" required="required">
                            </div>
                            <div class="form-group">

                                <label for="">bilangan</label>
                                <select class="form-control" id="type" name="bilangan">
                                    @php
                                    $arr = ['NONE','PULUHAN','RATUSAN','RIBUAN'];
                                    @endphp
                                    @foreach($arr as $item)
                                    <option value="{{ $item }}" > {{ strtoupper($item) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="box-footer">
                          
                            <button class="btn btn-primary">
                                Simpan <span class="fa fa-save"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
