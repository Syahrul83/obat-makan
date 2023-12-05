@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')

<div class="content-wrapper">
	<section class="content-header">
		<h1>Retur Barang</h1>
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
						<a href="{{ url('/admin/retur-barang/tambah') }}">
							<button class="btn btn-primary">
								Tambah Data
							</button>
						</a>
					</div>
					<div class="box-body">
						<table class="table table-hover data-retur-barang">
							<thead>
								<th>No.</th>
								<th>Nomor Retur</th>
								<th>Nomor Transaksi</th>
								<th>Tanggal Retur</th>
								<th>Total Nominal Retur</th>
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