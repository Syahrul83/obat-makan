@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Golongan Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/admin/data-golongan-obat') }}">
							<button class="btn btn-default" type="button"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/admin/data-golongan-obat/save')}}" method="POST" enctype="multipart/form-data">
						{{csrf_field()}}
						<div class="box-body">
							@if (session()->has('message'))
							<div class="alert alert-success alert-dismissible">
								{{session('message')}} <button class="close" type="button" data-dismiss="alert">X</button>
							</div>
							@endif
							<div class="form-group">
								<label for="">Nama Golongan</label>
								<input type="text" name="nama_golongan" class="form-control" value="{{isset($row) ? $row->nama_golongan : ''}}" required="required" placeholder="Isi Nama Golongan Obat">
							</div>
						</div>
						<div class="box-footer">
							<button class="btn btn-primary">Simpan <span class="fa fa-save"></span></button>
							<input type="hidden" name="id_golongan_obat" value="{{isset($row)?$row->id_golongan_obat:''}}">
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('js')

@endsection