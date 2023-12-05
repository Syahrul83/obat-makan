@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Obat</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<div class="col-md-2">
							<a href="{{ url('/kasir/data-obat/tambah') }}">
								<button class="btn btn-primary">Tambah Obat</button>
							</a>
						</div>
						<form action="{{ url('/kasir/data-obat/rekap-obat') }}">
							@csrf
							<div class="col-md-6">
								<div class="col-md-4">
									<input type="text" name="from" class="form-control datepicker" placeholder="dd-mm-yyyy" required>
								</div>
								<div class="col-md-4">
									<input type="text" name="to" class="form-control datepicker" placeholder="dd-mm-yyyy" required>
								</div>
								<button class="btn btn-success">Rekap Data Obat</button>
							</div>
						</form>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-obat-panel">
							<thead>
								<th>No.</th>
								<th>Pabrik Obat</th>
								<th>Nama Obat</th>
								<th>Golongan Obat</th>
								<th>Bentuk Sediaan Obat</th>
								<th>Tanggal Expired</th>
								<th>Stok Obat</th>
								<th>Satuan Obat</th>
								<th>Dosis Satuan</th>
								<th>Hna</th>
								<th>Hna+PPn</th>
								<th>Hja UPDS</th>
								<th>Hja Resep</th>
								<th>Hja Relasi</th>
								<th>#</th>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection