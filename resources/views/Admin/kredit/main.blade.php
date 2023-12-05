@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Kredit</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-kredit-panel">
							<thead>
								<th>No.</th>
								<th>Nama Pelanggan</th>
								<th>Nomor Telepon</th>
								<th>Alamat Pelanggan</th>
								<th>Status</th>
								<th>#</th>
							</thead>
						</table>
						<tbody>
							
						</tbody>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection