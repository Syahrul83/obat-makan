@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Pabrik</h1>
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
					<div class="box-header">
						<a href="{{ url('/kasir/data-pabrik-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/data-pabrik-obat/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama Pabrik</label>
								<input type="text" name="nama_pabrik" class="form-control" value="{{isset($row)?$row->nama_pabrik:''}}" placeholder="Isi Nama Pabrik" required="required" autofocus="autofocus">
							</div>
							<div class="form-group">
								<label for="">Nomor Telepon</label>
								<input type="number" name="nomor_hp" class="form-control" value="{{isset($row)?$row->nomor_telepon_pabrik:''}}" placeholder="Isi Nomor Telepon" required="required">
							</div>
							<div class="form-group">
								<label for="">Alamat Pabrik</label>
								<textarea name="alamat_pabrik" class="form-control" cols="30" rows="10" placeholder="Isi Alamat Pabrik" required="required">{{isset($row)?$row->alamat_pabrik:''}}</textarea>
							</div>
						</div>
						<div class="box-footer">
							<button class="btn btn-primary">
								<input type="hidden" name="id" value="{{isset($row)?$row->id_pabrik_obat:''}}">
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