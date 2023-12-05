@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Kredit</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/kasir/data-kredit/detail/'.$id) }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> 
								Kembali
							</button>
						</a>
						<div class="row">
							<div class="col-md-12">
								<form action="{{ url('/kasir/data-kredit/detail/'.$id.'/cetak-kredit-range',$id_faktur) }}" style="margin-top:1%;">
									@csrf
									<div class="col-md-3">
										<div class="form-group">
											<label for="">Dari</label>
											<input type="text" name="from" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="">Ke</label>
											<input type="text" name="to" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
										</div>
									</div>
									<div class="col-md-3" style="margin-top:2%;">
										<button class="btn btn-success">
											Cetak Kredit
										</button>
									</div>
								</form>
							</div>
							<div class="col-md-6">
								<a href="{{ url('/kasir/data-kredit/detail/'.$id.'/lihat-hutang/'.$id_faktur.'/lunas-semua') }}">
									<button class="btn btn-primary">
										Lunaskan Semua
									</button>
								</a>
							</div>
						</div>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-kredit-detail-panel">
							<thead>
								<th>No.</th>
								<th>Nama Pelanggan</th>
								<th>Tanggal Jatuh Tempo</th>
								<th>Nama Obat</th>
								<th>Nama Supplier</th>
								<th>Banyak Hutang</th>
								<th>Diskon</th>
								<th>Diskon (Rupiah)</th>
								<th>Sub Total</th>
								<th>Status Kredit</th>
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