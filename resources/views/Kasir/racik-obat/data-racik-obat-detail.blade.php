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
						<a href="{{ url('/kasir/data-racik-obat/'.$id) }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<div class="box-body">
						<h4><b>Kode Penjualan : {{ $nomor_transaksi }}</b></h4>
						<table class="table table-hover table-bordered data-racik-obat-detail" id-racik-data-detail="{{$id_detail}}">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Jenis Obat</th>
								<th>Jumlah</th>
								<th>Embalase</th>
								<th>Sub Total</th>
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