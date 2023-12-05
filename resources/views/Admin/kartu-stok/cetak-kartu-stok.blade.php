<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kartu Stok Print</title>
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<style>
		.btn-print {
			margin:10px 0 10px 0;
		}
		@media print {
			@page {
			  size:A4 portrait;
			}
			.btn-print {
				display:none!important;
			}
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<a href="{{ url('/admin/kartu-stok') }}">
			<button class="btn btn-default btn-print">
				Kembali
			</button>
		</a>
		<h5><b>Kartu Stok</b></h5>
		<h5><b>Tanggal : {{ human_date($tanggal_dari) }} s/d {{ human_date($tanggal_sampai) }}</b></h5>
		<h5><b>Nama Obat : {{ $nama_obat }}</b></h5>
		<br>
		<br>
		<table class="table table-hover table-bordered">
			<thead>
				<th>No.</th>
				<th>Tanggal</th>
				<th>Nomor</th>
				<th>Layanan</th>
				<th>Beli</th>
				<th>Jual</th>
				<th>Saldo</th>
				<th>Keterangan</th>
			</thead>
			<tbody>
				@foreach ($get_kartu_stok as $key => $value)
				<tr>
					<td>{{ $key+1 }}</td>
					<td>{{ human_date($value->tanggal_pakai) }}</td>
					<td>{{ $value->nomor_stok }}</td>
					<td>{{ $value->layanan }}</td>
					<td>{{ $value->beli }}</td>
					<td>{{ $value->jual }}</td>
					<td>{{ $value->saldo }}</td>
					<td>{{ $value->keterangan }}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="4" align="center"></td>
					<td><b>{{ $sum_beli }}</b></td>
					<td><b>{{ $sum_jual }}</b></td>
					<td><b>{{ $sum_saldo }}</b></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>

<script>
	window.print();
</script>