<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Stok Opnem Print</title>
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<style>
		.btn-print {
			margin:10px 0 10px 0;
		}
		@media print {
			@page {
			  size:A4 portrait;
			  margin: 0;
			}
			.btn-print {
				display:none!important;
			}
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<a href="{{ url('/admin/stok-opnem') }}">
			<button class="btn btn-default btn-print">
				Kembali
			</button>
		</a>
		<h6><b>Laporan Stok Opnem</b></h6>
		<h6><b>Tanggal Laporan : {{ human_date($tanggal_stok_opnem) }}</b></h6>
		<table border="1" width="100%">
			<thead>
				<td align="center"><b>No.</b></td>
				<td align="center" width="15%"><b>Nama Obat</b></td>
				<td align="center" width="7%"><b>Satuan</b></td>
				<td align="center"><b>Hna</b></td>
				<td align="center"><b>S.K</b></td>
				<td align="center"><b>S.F</b></td>
				<td align="center"><b>S.S</b></td>
				<td align="center"><b>Nilai</b></td>
				<td align="center" width="10%"><b>Tanggal Exp</b></td>
			</thead>
			<tbody>
				@foreach ($get_detail_stok as $key => $value)
				<tr>
					<td align="center">{{ $key+1 }}</td>
					<td>{{ $value->nama_obat }}</td>
					<td align="center">{{ $value->satuan_obat }}</td>
					<td>{{ format_rupiah($value->harga_modal) }}</td>
					<td align="center">{{ $value->stok_komputer }}</td>
					<td align="center">{{ $value->stok_fisik }}</td>
					<td align="center">{{ $value->stok_selisih }}</td>
					<td>{{ format_rupiah($value->sub_nilai) }}</td>
					<td align="center">{{ date_excel($value->tanggal_expired) }}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="7" align="center"><b><h5>Jumlah</h5></b></td>
					<td colspan="2"><b>{{ format_rupiah($total_nilai) }}</b></td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>

<script>
	window.print();
</script>