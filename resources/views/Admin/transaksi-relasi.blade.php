@extends('layout.app-kasir')

@section('content')
<div class="modal fade" id="modal-default">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Data Obat</h4>
			</div>
			<div class="modal-body">
				<table class="table table-hover table-bordered data-obat force-fullwidth" id="data-obat-modal">
					<thead>
						<th>No.</th>
						<th>Nama Obat</th>
						<th>Jenis Obat</th>
						<th>Tanggal Expired</th>
						<th>Stok Obat</th>
						<th>Satuan Stok</th>
						<th>Hja</th>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-kredit">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Data Kredit</h4>
			</div>
			<div class="modal-body">
				<div class="open" id="kredit-table">
					<table class="table table-hover table-bordered data-kredit force-fullwidth">
						<thead>
							<th>No.</th>
							<th>Nama Pelanggan</th>
							<th>Nomor Telepon</th>
							<th>Alamat Pelanggan</th>
							<th>Status</th>
							<th>#</th>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
				<div class="form-hide" id="kredit-det-table">
					<form id="form-kredit">
						<div class="mb-10">
							<button class="btn btn-default" id="back" type="button">
								Kembali
							</button>
							<button class="btn btn-primary" id="bayar-semua" type="button">
								Bayar Semua
							</button>
							<button class="btn btn-success" type="submit">
								Bayar
							</button>
						</div>
						<table class="table table-hover table-bordered data-detail-kredit force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Tanggal Jatuh Tempo</th>
								<th>Nama Obat</th>
								<th>Banyak Hutang</th>
								<th>Diskon</th>
								<th>Sub Total</th>
								<th>#</th>
							</thead>
							<tbody>

							</tbody>
						</table>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<div class="content-wrapper kasir-wrapper" style="padding-top:0;">
	<section class="content content-padding">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="box box-default">
					<div class="box-header with-border">
						<h4 align="center"><b>Penjualan Relasi</b></h4>
					</div>
				</div>
			</div>
			<div class="col-md-8 section-obat">
				<div class="box box-default">
					<div class="box-body">
						<div class="col-md-4">
						<!-- <div class="form-group">
								<label for="">Debitur</label>
								<select name="kreditur" class="form-control select2">
									<option value="" selected="selected" >=== Pilih Debitur ===</option>
									@foreach ($debitur as $element)
									<option value="{{$element->id}}">{{$element->name.' | '. $element->margin.'%'}}</option>
									@endforeach
								</select>
							</div> -->
							<div class="form-group">
								<label for="">Obat</label>
								<select name="obat" class="form-control select2 obat-kasir">
									<option value="" selected="selected" disabled="disabled">=== Pilih Obat ===</option>
									@foreach ($obat as $element)
									<option value="{{$element->id_obat}}">{{$element->nama_obat.' | '.cek_stok($element->stok_obat,$element->satuan_obat)}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label for="">Jumlah Obat</label>
								<input type="number" name="jumlah_obat" class="form-control jumlah-obat-kasir" placeholder="Isi Jumlah Obat">
							</div>
							<div class="form-group">
								<label for="">Satuan Obat</label>
								<input type="text" name="satuan_obat" class="form-control" disabled="disabled">
							</div>
							<input type="hidden" id="input-diskon-persen" checked="checked" value="persen">
							<label for="">Diskon</label>
							<div class="input-group" style="margin-bottom:10px;">
								<input type="number" name="diskon_obat" class="form-control" id="diskon-persen-input" placeholder="Isi Diskon Bila Perlu" value="0">
								<span class="input-group-addon">%</span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="">Hja</label>
								<input type="number" class="form-control" id="harga-jual" disabled="disabled">
								<label for="" id="harga-jual-label">Rp. 0,00</label>
							</div>
							<div class="form-group">
								<label for="">Pabrik</label>
								<input type="text" class="form-control" id="pabrik" disabled="disabled">
							</div>
						</div>
						<div class="col-md-4">
							<label for="">Komposisi</label>
							<div id="komposisi-obat">
								<div class="form-group">
									<input type="text" class="form-control" disabled="disabled">
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="col-md-12">
							<button class="btn btn-success" id="input-kasir" btn-attr="kasir-relasi">Input</button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 section-transaksi" id="input-hidden">
				<form action="{{url('/admin/penjualan/save')}}" id="upds-post" method="POST">
					@csrf
					<div class="box box-default">
						<div class="box-header with-border">
							<h4 class="total"><b>Total Bayar : Rp. 0,00</b></h4>
							<h4 class="total-diskon"><b>Total Diskon : Rp. 0,00</b></h4>
							<h4 class="total-semua"><b>Grand Total : Rp. 0,00</b></h4>
							<input type="hidden" name="kredit" value="on">
						</div>
						<div class="box-body">
							<div class="col-md-12">
								<input type="hidden" name="total_harga" value="">
								<input type="hidden" name="jenis_kasir" value="relasi">
								<div id="input-kredit">
									<div class="form-group">
										<input type="radio" class="checkbox-pelanggan" id="pilih-pelanggan" checked="checked"> Pilih Pelanggan &nbsp;
										<input type="radio" class="checkbox-pelanggan" id="input-pelanggan"> Input Pelanggan
									</div>
									<div class="open" id="input-pilih-pelanggan">
										<div class="form-group">
											<label for="">Pelanggan</label>
											<select name="pelanggan_input" class="form-control pelanggan-input select2">
												<option value="" selected="selected" disabled="disabled">=== Pilih Pelanggan ===</option>
												@foreach ($pelanggan as $element)
												<option value="{{$element->id_kredit}}">{{$element->nama_pelanggan}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-hide" id="input-pelanggan-aja">
										<div class="form-group">
											<label for="">Nama Pelanggan</label>
											<input type="text" name="nama_pelanggan_input" class="form-control" placeholder="Isi Nama Pelanggan">
										</div>
										<div class="form-group">
											<label for="">Nomor Telepon</label>
											<input type="number" name="nomor_telepon" class="form-control" placeholder="Isi Nomor Telepon Pelanggan">
										</div>
										<div class="form-group">
											<label for="">Alamat Pelanggan</label>
											<input type="text" name="alamat_pelanggan" class="form-control" placeholder="Isi Alamat Pelanggan">
										</div>
									</div>
									<div class="form-group">
										<label for="">Tanggal Jatuh Tempo</label>
										<input type="text" name="tanggal_jatuh_tempo" id="tanggal-jatuh-tempo" class="form-control datepicker" value="{{reverse_date(tambah_hari(date('Y-m-d')))}}">
									</div>
								</div>
								<div class="form-group">
									<a href="#" class="btn btn-success" attr-submit="none" id="submit">Submit</a>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-8 col-md-offset-2">
				<div class="box box-default">
					<div class="box-body">
						<table class="table table-hover table-bordered transaksi-obat">
							<thead align="center">
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Jumlah</th>
								<th>Harga</th>
								<th>Diskon</th>
								<th>Total</th>
								<th>#</th>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="content-padding force-to-bottom">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="box box-default">
					<div class="box-body">
							<div class="col-md-12 col-md-offset-1 btn-helper">
								<button class="btn btn-success" style="margin-right:7px;height:70px;" data-toggle="modal" data-target="#modal-default">[ALT+A] Lihat Obat</button>
								<a href="{{url('/admin/panel')}}"><button class="btn btn-success" style="margin-right:7px;height:70px;">[ALT+D] Panel</button></a>
								<button class="btn btn-success" id="focus-input-obat" style="margin-right:7px;height:70px;">[ALT+Z] Focus Input Obat</button>
								<button class="btn btn-success" id="focus-input-bayar" style="margin-right:7px;height:70px;">[ALT+X] Focus Input Bayar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('js')
<script>
	$(() => {
		function reset_token() {
	        $.ajaxSetup({
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

			$.ajax({
				url: "{{ url('/reset-token') }}",
				type: 'POST'
			})
			.done(function(done) {
				$('meta[name="csrf-token"]').attr('content',done)
				$('input[name="_token"]').val(done)
			})
			.fail(function(error) {
				console.log(error);
			})
		}

		setInterval(reset_token,5000);

		$('.obat-kasir').change(function(e){
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
    			$('.jumlah-obat-kasir').focus()
		    }, 1);
		})

		$('.jumlah-obat-kasir').keydown(function(e){
			if (e.keyCode == 13) {
				$('#diskon-persen-input').focus();
			}
		})

		$('.pelanggan-input').change(function(e){
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
    			$('#tanggal-jatuh-tempo').focus()
		    }, 1);
		})

		$('#diskon-persen-input').keydown(function(e){
			if (e.keyCode == 13) {
				$('#input-kasir').focus();
			}
		})

		$('#submit').click(function(){
			if ($(this).attr('attr-submit') != 'submit') {
				$(this).attr('disabled','disabled')
				$(this).attr('attr-submit','submit')
				$('#upds-post').submit();
			}
		})
	})
</script>
@endsection