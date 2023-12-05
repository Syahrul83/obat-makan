@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Laporan Data</h1>
	</section>

	<section class="content">
		<div class="row">
			{{-- <div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Transaksi UPDS</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/transaksi')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Transaksi Resep</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/transaksi-racik-obat')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pembelian</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/pembelian')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pemakaian Obat</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/pemakaian-obat')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pemakaian Per Dokter</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/pemakaian-per-dokter')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pemakaian Per Supplier</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/pemakaian-per-supplier')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pemakaian Per Pabrik</h4>
					</div>
					<div class="box-body">
						<form action="{{url('/kasir/laporan-data/pemakaian-per-pabrik')}}">
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="date" name="from" class="form-control" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="date" name="to" class="form-control" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success" style="margin-top:18%;">
									Export
								</button>
							</div>
						</form>
					</div>
				</div>	
			</div> --}}
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Penjualan</h4>
					</div>
					<div class="box-body">
						<form action="{{ url('/kasir/laporan-penjualan') }}">
							@csrf
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="text" name="from" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="text" name="to" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-penjualan-harian" style="margin-bottom:2%;">
									Laporan Penjualan Harian
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-upds" style="margin-bottom:2%;">
									Laporan Transaksi UPDS
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-resep" style="margin-bottom:2%;">
									Laporan Transaksi Resep
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-pemakaian-obat" style="margin-bottom:2%;">
									Laporan Pemakaian Obat
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-pemakaian-dokter" style="margin-bottom:2%;">
									Laporan Pemakaian Obat Per Dokter
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-pemakaian-supplier" style="margin-bottom:2%;">
									Laporan Pemakaian Obat Per Supplier
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-pemakaian-pabrik" style="margin-bottom:2%;">
									Laporan Pemakaian Obat Per Pabrik
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4>Laporan Pembelian</h4>
					</div>
					<div class="box-body">
						<form action="{{ url('/kasir/laporan-data/pembelian') }}">
							@csrf
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Dari</label>
									<input type="text" name="from" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['from'])?'value="'.$_GET['from'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Ke</label>
									<input type="text" name="to" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required" {!!isset($_GET['to'])?'value="'.$_GET['to'].'"':''!!}>
								</div>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-faktur" style="margin-bottom:2%;">
									Laporan Faktur
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-beli-tunai" style="margin-bottom:2%;">
									Laporan Beli Tunai
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-jatuh-tempo" style="margin-bottom:2%;">
									Laporan Jatuh Tempo
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-retur-barang" style="margin-bottom:2%;">
									Laporan Retur Barang
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-konsinyasi" style="margin-bottom:2%;">
									Laporan Konsinyasi
								</button>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success" name="btn_act" value="laporan-konsinyasi-jatuh-tempo" style="margin-bottom:2%;">
									Laporan Konsinyasi Jatuh Tempo
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection