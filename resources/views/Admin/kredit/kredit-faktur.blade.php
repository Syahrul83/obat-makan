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
					<div class="box-header">
						<a href="{{ url('/admin/data-kredit') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> 
								Kembali
							</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-kredit-faktur-panel">
							<thead>
								<th>No.</th>
								<th>Nomor Faktur</th>
								<th>Tanggal Kredit</th>
								<th>Status Kredit</th>
								<th>Input By</th>
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