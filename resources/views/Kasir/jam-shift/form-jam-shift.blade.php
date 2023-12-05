@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Jam Shift</h1>
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
						<a href="{{ url('/kasir/jam-shift') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/jam-shift/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Dari Jam</label>
								<input type="time" name="dari_jam" class="form-control" value="{{isset($row)?$row->jam_awal:''}}" required="required" autofocus="autofocus">
							</div>
							<div class="form-group">
								<label for="">Sampai Jam</label>
								<input type="time" name="sampai_jam" class="form-control" value="{{isset($row)?$row->jam_akhir:''}}" required="required" autofocus="autofocus">
							</div>
							<div class="form-group">
								<label for="">Ket Shift</label>
								<input type="text" name="ket_shift" class="form-control" placeholder="Isi Ket Shift; Ex: Jam Pagi;" value="{{isset($row)?$row->ket_shift:''}}" required="required" autofocus="autofocus">
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id" value="{{isset($row)?$row->id_jam_shift:''}}">
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