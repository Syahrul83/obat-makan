@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Users</h1>
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
					<div class="box-header">
						<a href="{{ url('/admin/data-users') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/admin/data-users/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="form-group">
								<label for="">Nama</label>
								<input type="text" name="nama" class="form-control" value="{{isset($row)?$row->name:''}}" placeholder="Isi Nama" required="required" autofocus="autofocus">
							</div>
							<div class="form-group">
								<label for="">Username</label>
								<input type="text" name="username" class="form-control" value="{{isset($row)?$row->username:''}}" placeholder="Isi Username" required="required" {!!isset($row)?'disabled="disabled"':''!!}>
								{!!isset($row)?'<input type="checkbox" id="sip">Ubah Username':''!!}
							</div>
							<div class="form-group">
								<label for="">Password</label>
								<div class="input-group" id="input-password">
									<input type="password" name="password" class="form-control" {!!isset($row)?'':'required="required"'!!} placeholder="Isi Password">
									<div class="input-group-addon" style="cursor: pointer;">
										<i class="fa fa-eye-slash" aria-hidden="true"></i>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="">Menu User</label>
								@if (isset($row))
								@foreach ($menu_user as $key => $value)
								@php
									$data[$value->menu_child] = $value->menu_child;
								@endphp
								@endforeach
								<select name="menu_user[]" class="form-control menu-user-select2" multiple>
									<optgroup label="Data Master">
										<option value="jam-shift|data-master" {!!isset($data['jam-shift']) ? 'selected="selected"' : ''!!}>Jam Shift</option>
										<option value="data-pasien|data-master" {!!isset($data['data-pasien']) ? 'selected="selected"' : ''!!}>Data Pasien</option>
										<option value="data-dokter|data-master" {!!isset($data['data-dokter']) ? 'selected="selected"' : ''!!}>Data Dokter</option>
										<option value="margin-obat|data-master" {!!isset($data['margin-obat']) ? 'selected="selected"' : ''!!}>Margin Obat</option>
										<option value="data-ppn|data-master" {!!isset($data['data-ppn']) ? 'selected="selected"' : ''!!}>Data PPn</option>
									</optgroup>
									<optgroup label="Obat">
										<option value="data-obat|obat" {!!isset($data['data-obat']) ? 'selected="selected"' : ''!!}>Data Obat</option>
										<option value="data-supplier-obat|obat" {!!isset($data['data-supplier-obat']) ? 'selected="selected"' : ''!!}>Data Supplier Obat</option>
										<option value="data-pabrik-obat|obat" {!!isset($data['data-pabrik-obat']) ? 'selected="selected"' : ''!!}>Data Pabrik Obat</option>
										<option value="data-jenis-obat|obat" {!!isset($data['data-jenis-obat']) ? 'selected="selected"' : ''!!}>Bentuk Sediaan Obat</option>
										<option value="data-golongan-obat|obat" {!!isset($data['data-golongan-obat']) ? 'selected="selected"' : ''!!}>Data Golongan Obat</option>
									</optgroup>
									<optgroup label="Pembelian">
										<option value="data-pembelian|pembelian" {!!isset($data['data-pembelian-obat']) ? 'selected="selected"' : ''!!}>Data Pembelian Obat</option>
										<option value="kartu-stok|pembelian" {!!isset($data['kartu-stok']) ? 'selected="selected"' : ''!!}>Kartu Stok</option>
										<option value="history-beli|pembelian" {!!isset($data['history-beli']) ? 'selected="selected"' : ''!!}>History Beli</option>
									</optgroup>
									<optgroup label="Penjualan">
										<option value="penjualan|penjualan" {!!isset($data['penjualan']) ? 'selected="selected"' : ''!!}>Penjualan UPDS</option>
										<option value="racik-obat|penjualan" {!!isset($data['racik-obat']) ? 'selected="selected"' : ''!!}>Penjualan Resep</option>
										<option value="penjualan-relasi|penjualan" {!!isset($data['penjualan-relasi']) ? 'selected="selected"' : ''!!}>Penjualan Relasi</option>
										<option value="data-penjualan|penjualan" {!!isset($data['data-penjualan']) ? 'selected="selected"' : ''!!}>Data Penjualan UPDS</option>
										<option value="data-penjualan-racik-obat|penjualan" {!!isset($data['data-penjualan-racik-obat']) ? 'selected="selected"' : ''!!}>Data Penjualan Resep</option>
										<option value="data-kredit|penjualan" {!!isset($data['data-kredit']) ? 'selected="selected"' : ''!!}>Data Kredit</option>
										<option value="retur-barang|penjualan" {!!isset($data['retur-barang']) ? 'selected="selected"' : ''!!}>Retur Barang</option>
									</optgroup>
									<optgroup label="">
										<option value="laporan-data" {!!isset($data['laporan-data']) ? 'selected="selected"' : ''!!}>Laporan Data</option>
										<option value="stok-opnem" {!!isset($data['stok-opnem']) ? 'selected="selected"' : ''!!}>Stok Opnem</option>
									</optgroup>
								</select>
								@else
								<select name="menu_user[]" class="form-control menu-user-select2" multiple>
									<optgroup label="Data Master">
										<option value="jam-shift|data-master">Jam Shift</option>
										<option value="data-pasien|data-master">Data Pasien</option>
										<option value="data-dokter|data-master">Data Dokter</option>
										<option value="data-ppn|data-master">Data PPn</option>
									</optgroup>
									<optgroup label="Obat">
										<option value="data-obat|obat">Data Obat</option>
										<option value="margin-obat|obat">Margin Obat</option>
										<option value="data-supplier-obat|obat">Data Supplier Obat</option>
										<option value="data-pabrik-obat|obat">Data Pabrik Obat</option>
										<option value="data-jenis-obat|obat">Bentuk Sediaan Obat</option>
										<option value="data-golongan-obat|obat">Data Golongan Obat</option>
									</optgroup>
									<optgroup label="Pembelian">
										<option value="data-pembelian-obat|pembelian">Data Pembelian Obat</option>
										<option value="kartu-stok|pembelian">Kartu Stok</option>
										<option value="history-beli|pembelian">History Beli</option>
									</optgroup>
									<optgroup label="Penjualan">
										<option value="penjualan|penjualan">Penjualan UPDS</option>
										<option value="racik-obat|penjualan">Penjualan Resep</option>
										<option value="penjualan-relasi|penjualan">Penjualan Relasi</option>
										<option value="data-penjualan|penjualan">Data Penjualan UPDS</option>
										<option value="data-penjualan-racik-obat|penjualan">Data Penjualan Resep</option>
										<option value="data-kredit|penjualan">Data Kredit</option>
										<option value="retur-barang|penjualan">Retur Barang</option>
									</optgroup>
									<optgroup label="">
										<option value="laporan-data">Laporan Data</option>
										<option value="stok-opnem">Stok Opnem</option>
									</optgroup>
								</select>
								@endif
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id" value="{{isset($row)?$row->id_users:''}}">
							<button class="btn btn-primary">
								Simpan <span class="fa fa-save"></span>
							</button>
						</div>
					</form>
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection

@section('js')
<script>
	$(() => {
		$('#sip').click(function(){
			if ($(this).is(':checked')) {
				$('input[name="username"]').removeAttr('disabled');
			}
			else {
				$('input[name="username"]').attr('disabled','disabled');
			}
		});

		$('.menu-user-select2').select2({
			placeholder:' === Pilih Menu User === ',
			allowClear: true
		})

		$("#input-password").on('click', function(event) {
	        event.preventDefault();
	        if($('#input-password input').attr("type") == "text"){
	            $('#input-password input').attr('type', 'password');
	            $('#input-password i').addClass( "fa-eye-slash" );
	            $('#input-password i').removeClass( "fa-eye" );
	        }else if($('#input-password input').attr("type") == "password"){
	            $('#input-password input').attr('type', 'text');
	            $('#input-password i').removeClass( "fa-eye-slash" );
	            $('#input-password i').addClass( "fa-eye" );
	        }
    	})
	})
</script>
@endsection