@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
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
						<a href="{{ url('/kasir/data-pasien') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/data-pasien/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama Pasien</label>
								<input type="text" name="nama_pasien" class="form-control" value="{{isset($row)?$row->nama_pasien:''}}" placeholder="Isi Nama Pasien" required="required">
							</div>
							<div class="form-group">
								<label for="">Nomor Telepon Pasien</label>
								<input type="number" name="nomor_telepon_pasien" class="form-control" value="{{isset($row)?$row->nomor_telepon_pasien:''}}" placeholder="Isi Nomor Telepon Pasien" required="required">
							</div>
							<div class="form-group">
								<label for="">Alamat Pasien</label>
								<textarea name="alamat_pasien" class="form-control" placeholder="Isi Alamat Pasien" cols="30" rows="10" required="required">{{isset($row)?$row->alamat_pasien:''}}</textarea>
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id" value="{{isset($row)?$row->id_pasien:''}}">
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