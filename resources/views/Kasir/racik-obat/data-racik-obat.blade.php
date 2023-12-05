@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Racik</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/kasir/data-penjualan-racik-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<div class="box-body">
						<h4><b>Kode Penjualan : {{ $nomor_transaksi }}</b></h4>
						<table class="table table-hover table-bordered data-racik-obat" id-racik-data="{{$id}}">
							<thead>
								<th>No.</th>
								<th>Nama Racik</th>
								<th>Jenis Racik</th>
								<th>Jumlah</th>
								<th>Ongkos Racik</th>
								<th>Harga Total</th>
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