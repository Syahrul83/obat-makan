@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Data PPn</h1>
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
						<a href="{{ url('/kasir/data-ppn') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/data-ppn/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<label for="">PPn</label>
							<div class="input-group col-md-6">
								<input type="number" name="ppn" class="form-control" value="{{isset($row)?$row->ppn:''}}" required="required" autofocus="autofocus">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id_persen_ppn" value="{{isset($row)?$row->id_persen_ppn:''}}">
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