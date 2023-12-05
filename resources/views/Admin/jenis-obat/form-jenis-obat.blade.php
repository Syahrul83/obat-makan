@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Bentuk Sediaan Obat</h1>
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
						<a href="{{ url('/admin/data-jenis-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/admin/data-jenis-obat/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama Bentuk Kesediaan Obat</label>
								<input type="text" name="nama_jenis_obat" class="form-control" placeholder="Isi Nama Kesediaan Obat" value="{{isset($row)?$row->nama_jenis_obat:''}}" required="required" autofocus="autofocus">
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id" value="{{isset($row)?$row->id_jenis_obat:''}}">
							<button type="submit" class="btn btn-primary">
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