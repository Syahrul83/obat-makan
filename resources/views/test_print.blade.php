<html>
	<title>Pembayaran</title>
	<head>
		<style>
			th {
				font-weight: 100;
			}
			.btn-print {
				margin:10px 0 10px 0;
			}
			@media print {
				@page {
				  size:A4 landscape;
				}
				.btn-print {
					display:none!important;
				}
			}
		</style>
		<link rel="stylesheet" href="{{ asset('css/app.css') }}">
  		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	</head>
	<body style="background:white;" onload="window.print()">
		<div class="wrapper container-fluid">
		<button class="btn btn-default btn-print" onclick="window.history.back();">
			Kembali
		</button>
		  <!-- Main content -->
		<section class="invoice">
				<div class="col-xs-12">
					<h2 class="page-header">
						<img src="{{asset('/horse-standing-on-back-paws.svg')}}" height="35" alt=""> Apotek Mustang Farma
						<br>
						<div class="float-right">
							<small>{{ date('d M Y') }}</small>
						</div>
						<address style="font-size:15px; font-weight: 100; margin-bottom:0; margin-top:10px">
						Jln. Pulau Kalimantan No. 63
						</address>
					</h2>
				</div>
				<hr>
			{{-- Kode Transaksi: {{ $kode }} <br> --}}
			<table class="table table-striped table-bordered">
				<thead>
					<th>No.</th>
					<th>Obat</th>
					<th width="12%">Banyak Obat</th>
					<th>Satuan</th>
					<th>Harga</th>
					<th>Subtotal</th>
				</thead>
				<tbody>
					{{-- @for ($i = 0; $i < count($obat); $i++) --}}
					{{-- <tr>
						<td>{{$i+1}}</td>
						<td>{{$obat_mod->obat($obat[$i])}}</td>
						<td>{{$pcs[$i]}}</td>
						<td>{{$satuan_trx[$i]}}</td>
						<td>{{format_rupiah($harga_obat->harga($obat[$i],$jen_hrg[$i]))}}</td>
						<td>{{format_rupiah($harga[$i])}}</td>
					</tr> --}}
					@for ($i = 0; $i < 10; $i++)
						{{-- expr --}}
					<tr>
						<td>{{$i+1}}</td>
						<td>Bisoprolol</td>
						<td>2</td>
						<td>Box</td>
						<td>{{format_rupiah(200000)}}</td>
						<td>{{format_rupiah(20000000)}}</td>
					</tr>
					@endfor
					{{-- @endfor --}}
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-6">
					<table>
						<tr>
							<th>Total</th>
							<th>:</th>
							<td>{{ format_rupiah(200000000000) }}</td>
						</tr>
						<tr>
							<th>Bayar</th>
							<th>:</th>
							<td>{{ format_rupiah(200000000000) }}</td>
						</tr>
						<tr>
							<th>Kembali</th>
							<th>:</th>
							<td>{{ format_rupiah(0) }}</td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table>
						{{-- @if ($diskon != '') --}}
						<tr>
							<th>Diskon</th>
							<th>:</th>
							<td>{{ '10 %' }}</td>
						</tr>
						{{-- @endif --}}
						{{-- @if ($total_diskon != 0) --}}
						<tr>
							<th>Total Diskon</th>
							<th>:</th>
							<td>{{ format_rupiah(1000000000) }}</td>
						</tr>
						{{-- @endif --}}
						<tr>
							<th>Transaksi Oleh</th>
							<th>:</th>
							<td>Admin</td>
						</tr>
					</table>
				</div>
			</div>
			</section>
		</div>
	</body>
</html>