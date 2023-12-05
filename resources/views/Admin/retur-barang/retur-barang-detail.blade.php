@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')

<div class="content-wrapper">
	<section class="content-header">
		<h1>Retur Barang Detail</h1>
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
						<a href="{{ url('/admin/retur-barang') }}">
							<button class="btn btn-default">
								Kembali
							</button>
						</a>
					</div>
					<div class="box-body">
						<table class="table table-hover data-retur-barang-detail" id-retur-barang="{{$id}}">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Stok Transaksi</th>
								<th>Stok Retur</th>
								<th>Nominal Retur</th>
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