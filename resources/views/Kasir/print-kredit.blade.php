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
				  size:A4 portrait;
				}	
				.btn-print {
					display:none!important;
				}
			}
		</style>
		<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	</head>
	<body style="background:white;" onload="window.print()">
		<div class="wrapper container-fluid print">
			<a href="{{ url()->previous() }}">
				<button class="btn btn-default btn-print">
					Kembali
				</button>
			</a>
		  <!-- Main content -->
		  <section class="invoice">
			 <div class="col-xs-12">
		        <h2 class="page-header">
		          <img src="{{asset('/apotek_bunda_farma.jpeg')}}" height="90" alt=""> {{ $profile_instansi->nama_instansi }}
		          <br>
		          <div class="float-right">
		          <h5>{{ human_date($kredit_faktur->tanggal_faktur) }}</h5>
		          <h5>{{ $kredit_faktur->jam_transaksi }}</h5>
				  <h5>Input By : {{ $kredit_faktur->name }}</h5>
		          </div>
		          <address style="font-size:15px; font-weight: 100; margin-bottom:0; margin-top:10px">
		          	{{ $profile_instansi->alamat_instansi }}
		          </address>
		          <address style="font-size:15px; font-weight: 100; margin-bottom:0; margin-top:10px">
		          	Telp : {{ $profile_instansi->nomor_telepon_instansi }}
		          </address>
		        </h2>
				<p style="margin:0;font-size:18px;"><b>Nomor Faktur Kredit : {{ $kredit_faktur->nomor_faktur }}</b></p>
				<br>
		      </div>
			<hr>
			<table border="1" width="100%">
				<thead>
					<td align="center"><b>No.</b></td>
					<td align="center" width="15%"><b>Tanggal Jatuh Tempo</b></td>
					<td align="center" width="25%"><b>Obat</b></td>
					<td align="center" width="10%"><b>Satuan Obat</b></td>
					<td width="10%" align="center"><b>Banyak Obat</b></td>
					<td align="center"><b>Harga</b></td>
					<td align="center"><b>Diskon</b></td>
					<td align="center"><b>Subtotal</b></td>
				</thead>
				<tbody>
					@foreach ($data as $key => $value)
					@php
						if ($value->jenis_diskon == 'persen') {
							$calculate_discount = get_discount($value->hja_relasi * $value->banyak_obat, $value->diskon);
							$diskon             = format_rupiah($calculate_discount);
						}
						else {
							$calculate_discount = $value->diskon;
							$diskon             = format_rupiah($value->diskon);
						}
						$real_price = format_rupiah(calculate_real_price($value->banyak_obat,$calculate_discount,$value->sub_total));
					@endphp
					<tr>
						<td align="center">{{$key+1}}</td>
						<td align="center">{{human_date($value->tanggal_jatuh_tempo)}}</td>
						<td>{{$value->nama_obat}}</td>
						<td align="center">{{ $value->satuan_obat }}</td>
						<td align="center">{{$value->banyak_obat}}</td>
						<td>{{$real_price}}</td>
						<td>{{$diskon}}</td>
						<td>{{format_rupiah($value->sub_total)}}</td>
					</tr>
					@endforeach
					<tr>
						<td colspan="6" align="center">Total</td>
						<td align="center">:</td>
						<td><b>{{ format_rupiah($total_kredit) }}</b></td>
					</tr>
				</tbody>
			</table>
			{{-- <table>
				<tr>
					<th>Nama Pelanggan</th>
					<th>:</th>
					<td>{{ $nama_pelanggan }}</td>
				</tr>
				<tr>
					<th>Total Kredit</th>
					<th>:</th>
					<td>{{ format_rupiah($total_kredit) }}</td>
				</tr>
				<tr>
					<th>Transaksi Oleh</th>
					<th>:</th>
					<td>{{ Auth::user()->name }}</td>
				</tr>
			</table> --}}
			</section>
			<div style="display: flex;">
				<div class="col-md-6">
					<h6 style="margin-top:5%;" align="center">Yang Menyerahkan</h6>
					<br>
					<br>
					<br>
					<h6 style="margin-top:5%;" align="center">{{ Auth::user()->name }}</h6>
				</div>
				<div class="col-md-6">
					<h6 style="margin-top:5%;" align="center">Yang Menerima</h6>
					<br>
					<br>
					<br>
					<h6 style="margin-top:5%;" align="center">{{ $nama_pelanggan }}</h6>
				</div>
			</div>
		</div>
	</body>
</html>