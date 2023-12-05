@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Margin Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-6 col-md-offset-2">
				@if (session()->has('message'))
				<div class="alert alert-success alert-dismissible">
					{{session('message')}} <button class="close" data-dismiss="alert">X</button>
				</div>
				@endif
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/admin/margin-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/admin/margin-obat/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<label for="">Margin UPDS</label>
							<div class="input-group" style="margin-bottom:10px;">
								<input type="number" name="margin_upds" class="form-control" value="{{isset($row)?$row->margin_upds:''}}" required="required" autofocus="autofocus">
								<span class="input-group-addon">%</span>
							</div>
							<label for="">Margin Resep</label>
							<div class="input-group" style="margin-bottom:10px;">
								<input type="number" name="margin_resep" class="form-control" value="{{isset($row)?$row->margin_resep:''}}" required="required">
								<span class="input-group-addon">%</span>
							</div>
							<label for="">Margin Relasi</label>
							<div class="input-group" style="margin-bottom:10px;">
								<input type="number" name="margin_relasi" class="form-control" value="{{isset($row)?$row->margin_relasi:''}}" required="required">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id" value="{{isset($row)?$row->id_margin_obat:''}}">
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