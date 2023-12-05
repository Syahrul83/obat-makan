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
			.border-table {
				border: 1px solid black;
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
		<div class="wrapper container-fluid">
			<a href="{{ url('/kasir/data-pembelian/tambah') }}">
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
		          <h5>Tanggal Terima : {{ human_date($pembelian->tanggal_terima) }}</h5>
		          <h5>Input By : {{ auth()->user()->name }}</h5>
		          </div>
		          <address style="font-size:15px; font-weight: 100; margin-bottom:0; margin-top:10px">
		            {{ $profile_instansi->alamat_instansi }}
		          </address>
		          <address style="font-size:15px; font-weight: 100; margin-bottom:0; margin-top:10px">
		            Telp : {{ $profile_instansi->nomor_telepon_instansi }}
		          </address>
		        </h2>
				<p style="margin:0;font-size:18px;"><b>Kode Transaksi : {{ $pembelian->kode_pembelian }}</b></p>
				<p style="margin:0;font-size:18px;"><b>Nomor Faktur : {{ $pembelian->nomor_faktur }}</b></p>
		      </div>
			<hr>
			<p style="margin:0;font-size:18px;"><b>Nama Supplier : {{ $pembelian->nama_supplier }}</b></p>
			@if ($pembelian->tanggal_jatuh_tempo != null)
			<p style="margin:0;font-size:18px;"><b>Tanggal Jatuh Tempo : {{ human_date($pembelian->tanggal_jatuh_tempo) }}</b></p>
			@endif
				<table width="100%">
					<thead>
						<td align="center" class="border-table"><b>No.</b></td>
						<td align="center" class="border-table"><b>Nama Obat</b></td>
						<td align="center" class="border-table"><b>Satuan Obat</b></td>
						<td align="center" class="border-table"><b>Jumlah</b></td>
						<td align="center" class="border-table"><b>Hna</b></td>
						<td align="center" class="border-table"><b>Disc 1</b></td>
						<td align="center" class="border-table"><b>Disc 2</b></td>
						<td align="center" class="border-table"><b>Disc 3</b></td>
						<td align="center" class="border-table"><b>Sub Total</b></td>
					</thead>
					<tbody>
						@php
							$total_diskon = 0;
						@endphp
						@foreach ($pembelian_detail as $key => $value)
						<tr>
							<td align="center" class="border-table">{{$key+1}}</td>
							<td class="border-table" width="27%">{{$value->nama_obat}}</td>
							<td align="center" width="10%" class="border-table">{{$value->satuan_obat}}</td>
							<td align="center" class="border-table" width="10%">{{$value->jumlah}}</td>
							<td class="border-table" width="10%">{{format_rupiah($value->harga_modal)}}</td>
							<td align="center" class="border-table" width="8%">{{$value->disc_1}}%</td>
							<td align="center" class="border-table" width="8%">{{$value->disc_2}}%</td>
							<td align="center" class="border-table" width="8%">{{$value->disc_3}}%</td>
							<td class="border-table" width="15%">{{format_rupiah($value->sub_total)}}</td>
						</tr>
						@php
							$arr_var = [
											'disc_1'     => $value->disc_1, 
											'disc_2'     => $value->disc_2, 
											'disc_3'     => $value->disc_3,
											'harga_obat' => $value->harga_obat,
											'jumlah'     => $value->jumlah
										];

							$total_diskon = $total_diskon + kalkulasi_diskon($arr_var);
						@endphp
						@endforeach
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="center" class="border-table">DPP</td>
							<td align="center" class="border-table">:</td>
							<td class="border-table">{{ format_rupiah($pembelian->total_dpp) }}</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="center" class="border-table">PPn</td>
							<td align="center" class="border-table">:</td>
							<td class="border-table">{{ format_rupiah($pembelian->total_ppn) }}</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="center" class="border-table">Diskon</td>
							<td align="center" class="border-table">:</td>
							<td class="border-table">{{ format_rupiah($total_diskon) }}</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="center" class="border-table">Total Beli</td>
							<td align="center" class="border-table">:</td>
							<td class="border-table">{{ format_rupiah($pembelian->total_semua) }}</td>
						</tr>
					</tbody>
				</table>
			</section>
		</div>
	</body>
</html>