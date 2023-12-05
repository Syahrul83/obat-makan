@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Dokter</h1>
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
						<a href="{{ url('/kasir/data-dokter') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/data-dokter/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama Dokter</label>
								<input type="text" name="nama_dokter" class="form-control" value="{{isset($row)?$row->nama_dokter:''}}" placeholder="Isi Nama Dokter" required="required" autofocus="autofocus">
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id_dokter" value="{{isset($row)?$row->id_dokter:''}}">
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