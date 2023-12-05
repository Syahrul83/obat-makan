@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Supplier</h1>
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
						<a href="{{ url('/admin/data-supplier-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/admin/data-supplier-obat/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama Supplier</label>
								<input type="text" name="nama_supplier" class="form-control" value="{{isset($row)?$row->nama_supplier:''}}" placeholder="Isi Nama Supplier" required="required" autofocus="autofocus">
							</div>
							<div class="form-group">
								<label for="">Singkatan Supplier</label>
								<input type="text" name="singkatan_supplier" class="form-control" value="{{isset($row)?$row->singkatan_supplier:''}}">
							</div>
							<div class="form-group">
								<label for="">Nomor Hp</label>
								<input type="number" name="nomor_hp" class="form-control" value="{{isset($row)?$row->nomor_telepon:''}}" placeholder="Isi Nomor Hp" required="required">
							</div>
							<div class="form-group">
								<label for="">Alamat Supplier</label>
								<textarea name="alamat_supplier" class="form-control" cols="30" rows="10" placeholder="Isi Alamat Supplier" required="required">{{isset($row)?$row->alamat_supplier:''}}</textarea>
							</div>
						</div>
						<div class="box-footer">
							<button class="btn btn-primary">
								<input type="hidden" name="id" value="{{isset($row)?$row->id_supplier:''}}">
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