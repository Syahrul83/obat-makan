@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Supplier Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/kasir/data-supplier-obat/tambah') }}">
							<button class="btn btn-primary">Tambah Supplier</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-supplier-obat force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Supplier</th>
								<th>Singkatan Supplier</th>
								<th>Nomor Hp Supplier</th>
								<th>Alamat Supplier</th>
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