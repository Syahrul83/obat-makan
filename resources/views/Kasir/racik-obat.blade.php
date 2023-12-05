@extends('layout.app-kasir')

@section('content')
<div class="modal fade" id="modal-input-racik">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Input Racik Obat</h4>
			</div>
			<form method="POST" class="form-post-racik">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-4" style="border-right:1px solid lightgrey">
								<div id="racik-section">
									<div class="form-group">
										<label for="">Nama Racikan</label>
										<input type="text" name="nama_racik" class="form-control" placeholder="Isi Nama Racikan" required="required">
									</div>
									<div class="form-group">
										<label for="">Jumlah</label>
										<input type="number" name="jumlah_racik" class="form-control" placeholder="Isi Jumlah" required="required">
									</div>
									<div class="form-group">
										<label for="">Ongkos Racik</label>
										<input type="number" class="form-control" id="ongkos-racik" name="ongkos_racik" required="required" placeholder="Isi Ongkos Racik">
										<label for="" id="ongkos-racik-label">Rp. 0,00</label>
									</div>
									<div class="form-group">
										<label for="">Keterangan Racik</label>
										<select name="keterangan_racik" class="form-control keterangan-racik select2" required>
											<option value="" selected disabled>=== Pilih Keterangan Racik ===</option>
											<option value="PCS">PCS</option>
											<option value="BKS">BKS</option>
											<option value="POT">POT</option>
											<option value="BTL">BTL</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-8">
								<div id="col-input-obat">
									<div id="form-input-obat" class="form-input-obat" style="border-bottom:1px solid lightgrey; margin-bottom:2px;">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Obat</label>
													<select name="obat[]" class="form-control select2 obat-racik" id="obat-racik" ajax-id="1">
														<option value="" selected="" disabled="">=== Pilih Obat ===</option>
														@foreach ($obat as $element)
														<option value="{{ $element->id_obat }}">{{ $element->nama_obat.' | '.cek_stok($element->stok_obat,$element->satuan_obat) }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Satuan Obat</label>
													<input type="text" name="satuan_obat[]" id="satuan-obat" class="form-control satuan-obat" readonly="readonly" satuan-id="1">
												</div>
											</div>
											<div id="form-non-dtd">
												<div class="col-md-6">
													<div class="form-group">
														<label for="">Jumlah</label>
														<input type="number" name="jumlah[]" id="jumlah" class="form-control jumlah" id-jumlah="1">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="">Hja</label>
														<input type="text" name="harga_umum[]" class="form-control harga-umum" id="harga-umum" disabled="" get-ajax-id="1">
														<label for="" class="harga-umum-racik-label" id="harga-umum-racik-label" id-label-umum-racik="1">Rp. 0,00</label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="">Harga Total</label>
														<input type="text" name="harga_total[]" id="harga-total" class="form-control harga-total" get-id-jumlah="1" readonly="">
														<label for="" class="harga-total-racik-label" id="harga-total-racik-label" id-label-total-racik="1">Rp. 0,00</label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="">Embalase</label>
														<input type="number" name="embalase[]" class="form-control embalase" id="embalase" value="0" id-embalase="1">
														<label for="" class="embalase-racik-label" id="embalase-racik-label" id-label-embalase-racik="1">Rp. 0,00</label>
													</div>
												</div>
												<input type="hidden" name="margin_resep[]" id="margin-resep-racik" class="margin-resep-racik" get-id-margin-resep="1">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12" id="btn-act-racik-obat" style="margin-top:5px;">
									<button class="btn btn-success" type="button" id="btn-tambah-racik-obat">Tambah Obat</button>
									<button class="btn btn-danger form-hide" type="button" id="btn-hapus-racik-obat">Hapus Obat</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="type_racik" value="input-racik">
					<input type="hidden" name="jenis_racik" value="non-dtd">
					<button class="btn btn-default" id="close-modal" type="button" data-dismiss="modal">Close</button>
					<button class="btn btn-primary" id="simpan-racikan">Simpan Racikan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-input-tanpa-racik">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Input Tanpa Racik Obat</h4>
			</div>
			<form method="POST" class="form-post-tanpa-racik">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">
								<div id="col-input-tanpa-racik">
									<div id="form-input-tanpa-racik" class="form-input-tanpa-racik" style="border-bottom:1px solid lightgrey; margin-bottom:2px;">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Obat</label>
													<select name="obat[]" class="form-control select2 obat-tanpa-racik" id="obat-tanpa-racik" ajax-id="1">
														<option value="" selected="" disabled="">=== Pilih Obat ===</option>
														@foreach ($obat as $element)
														<option value="{{ $element->id_obat }}">{{ $element->nama_obat.' | '.$element->stok_obat.' '.$element->satuan_obat }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Satuan Obat</label>
													<input type="text" name="satuan_obat[]" id="satuan-obat-tanpa-racik" class="form-control satuan-obat-tanpa-racik" readonly="readonly" satuan-id="1">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Jumlah</label>
													<input type="number" name="jumlah[]" id="jumlah-tanpa-racik" class="form-control jumlah-tanpa-racik" id-jumlah="1">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Hja</label>
													<input type="text" name="harga_umum[]" class="form-control harga-umum-tanpa-racik" id="harga-umum-tanpa-racik" disabled="" get-ajax-id="1">
													<label for="" class="harga-umum-tanpa-racik-label" id="harga-umum-tanpa-racik-label" id-label-umum-tanpa-racik="1">Rp. 0,00</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Harga Total</label>
													<input type="text" name="harga_total[]" id="harga-total-tanpa-racik" class="form-control harga-total-tanpa-racik" get-id-jumlah="1" readonly="">
													<label for="" class="harga-total-tanpa-racik-label" id="harga-total-tanpa-racik-label" id-label-total-tanpa-racik="1">Rp. 0,00</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="">Embalase</label>
													<input type="number" name="embalase[]" class="form-control embalase-tanpa-racik" id="embalase-tanpa-racik" value="0" id-embalase="1">
													<label for="" class="embalase-tanpa-racik-label" id="embalase-tanpa-racik-label" id-label-embalase-tanpa-racik="1">Rp. 0,00</label>
												</div>
											</div>
											<input type="hidden" name="margin_resep[]" id="margin-resep-tanpa-racik" class="margin-resep-tanpa-racik" get-id-margin-resep="1">
										</div>
									</div>
								</div>
								<div class="col-md-12" id="btn-act-tanpa-racik" style="margin-top:5px;">
									<button class="btn btn-success" type="button" id="btn-tambah-tanpa-racik">Tambah Obat</button>
									<button class="btn btn-danger form-hide" type="button" id="btn-hapus-tanpa-racik">Hapus Obat</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="type_racik" value="input-tanpa-racik">
					<button class="btn btn-default" id="close-tanpa-racik" type="button" data-dismiss="modal">Close</button>
					<button class="btn btn-primary" id="simpan-resep">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-detail-racik">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Detail Racik Obat</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<table class="table table-hover table-bordered detail-racik-obat">
						<thead>
							<th>No.</th>
							<th>Nama Resep</th>
							<th>Jumlah</th>
							<th>Harga Total</th>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" id="close-modal-detail" data-dismiss="modal">Close</button>
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
						<h4 align="center"><b>Penjualan Resep</b></h4>
					</div>
				</div>
			</div>
			<div class="col-md-8 col-md-offset-2">
				<div id="trx-obat-racik">
					<div class="box box-default">
						<div class="box-header with-border">
							<a href="{{ url('/kasir/panel') }}">
								<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
							</a>
							<button class="btn btn-primary" data-toggle="modal" data-target="#modal-input-racik">Input Racik</button>
							<button class="btn btn-primary" data-toggle="modal" data-target="#modal-input-tanpa-racik">Input Obat</button>
						</div>
						<div class="box-body">
							<table class="table table-hover table-bordered transaksi-racik-obat">
								<thead align="center">
									<th>No.</th>
									<th>Nama Resep</th>
									<th>Jumlah</th>
									<th>Harga</th>
									<th>#</th>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>
				{{-- <div id="trx-obat-tanpa-racik" class="form-hide">
					<div class="box box-default">
						<div class="box-header with-border">
							<button class="btn btn-primary" data-toggle="modal" data-target="#modal-input-tanpa-racik">Input Obat</button>
						</div>
						<div class="box-body">
							<table class="table table-hover table-bordered transaksi-tanpa-racik">
								<thead>
									<th>No.</th>
									<th>Nama Obat</th>
									<th>Jumlah</th>
									<th>Sub Total</th>
									<th>#</th>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div> --}}
			</div>
		</div>
	</section>
	<form action="{{ url('/kasir/racik-obat/bayar') }}" method="POST" id="form-post-keterangan">
		{{csrf_field()}}
		<section class="content content-padding force-to-bottom">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="box box-default">
						<div class="box-body">
							<div class="col-md-6">
								<button class="btn btn-primary" id="input-pasien-act" type="button" style="margin-bottom:2%;">Input Pasien</button>
								<button class="btn btn-primary form-hide" id="pilih-pasien-act" type="button" style="margin-bottom:2%;">Pilih Pasien</button>
								<div id="select-pasien">
									<div class="form-group">
										<label for="">Pasien</label>
										<select name="pasien" class="form-control select2" required="required">
											<option value="" selected="" disabled="">=== Pilih Pasien ===</option>
											@foreach ($pasien as $element)
											<option value="{{ $element->id_pasien }}">{{ $element->nama_pasien }}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="">Alamat Pasien</label>
										<input type="text" class="form-control" id="alamat-pasien-readonly" readonly>
									</div>
									<div class="form-group">
										<label for="">Nomor Telepon Pasien</label>
										<input type="text" class="form-control" id="nomor-telepon-pasien-readonly" readonly>
									</div>
								</div>
								<div class="form-hide" id="input-pasien">
									<div class="form-group">
										<label for="">Nama Pasien</label>
										<input type="text" class="form-control" name="nama_pasien" placeholder="Isi Nama Pasien" disabled="">
									</div>
									<div class="form-group">
										<label for="">Nomor Telepon</label>
										<input type="text" class="form-control" name="nomor_telepon_pasien" disabled="">
									</div>
									<div class="form-group">
										<label for="">Alamat</label>
										<input type="text" class="form-control" name="alamat_pasien" disabled="">
									</div>
								</div>
								<hr>
								<button class="btn btn-primary" id="input-dokter-act" type="button" style="margin-bottom:2%;">Input Dokter</button>
								<button class="btn btn-primary form-hide" id="pilih-dokter-act" type="button" style="margin-bottom:2%;">Pilih Dokter</button>
								<div id="select-dokter">
									<div class="form-group">
										<label for="">Dokter</label>
										<select name="dokter" class="form-control select2" required="">
											<option value="" selected="" disabled="">=== Pilih Dokter ===</option>
											@foreach ($dokter as $element)
											<option value="{{ $element->id_dokter }}">{{ $element->nama_dokter }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-hide" id="input-dokter">
									<div class="form-group">
										<label for="">Nama Dokter</label>
										<input type="text" class="form-control" name="nama_dokter" placeholder="Isi Nama Dokter" disabled="">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" id="total-semua-label">Harga Total : Rp. 0,00</label>
								<input type="hidden" id="total-semua" value="0" name="total_semua_racik">
								<div class="form-group">
									<label for="">Total Bayar</label>
									<input type="number" class="form-control" name="total_racik" id="total-racik" readonly="">
									<label for="" id="total-racik-label">Rp. 0,00</label>
								</div>
								<label for="">Diskon</label>
								<div class="input-group" style="margin-bottom:10px;">
									<input type="number" name="diskon_resep" class="form-control" id="diskon-resep" value="0" min="0" required="required">
									<span class="input-group-addon">%</span>
								</div>
								<div class="form-group">
									<label for="">Bayar</label>
									<input type="number" class="form-control" name="bayar" id="bayar-racik">
									<label for="" id="bayar-racik-label">Rp. 0,00</label>
								</div>
								<div class="form-group">
									<label for="">Kembalian</label>
									<input type="number" class="form-control" name="kembalian" id="kembalian-racik" readonly="readonly">
									<label for="" id="kembalian-racik-label">Rp. 0,00</label>
								</div>
							</div>
						</div>
						<div class="box-footer with-border">
							<input type="hidden" name="kode_racik" value="">
							<a href="#" class="btn btn-success" attr-submit="none" id="bayar-resep" disabled="">Bayar Resep</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</form>
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

		$('#ongkos-racik').keyup(function(){
			let val = $(this).val()
			$('#ongkos-racik-label').html(rupiah_format(val))
		})

		$(document).on('keyup','.jumlah',function(e){
			if ($(this).val() != '') {
				if (e.key === 'Enter') {
					let attr = $(this).attr('id-jumlah')
					console.log($(`.embalase[id-embalase="${attr}"]`))
					$(`.embalase[id-embalase="${attr}"]`).focus()
				}
			}
		})

		$(document).on('keyup','.jumlah-tanpa-racik',function(e){
			if ($(this).val() != '') {
				if (e.key === 'Enter') {
					let attr = $(this).attr('id-jumlah')
					console.log($(`.embalase-tanpa-racik[id-embalase="${attr}"]`))
					$(`.embalase-tanpa-racik[id-embalase="${attr}"]`).focus()
				}
			}
		})

		$(document).on('keyup','.embalase',function(e){
			if ($(this).val() != '') {
				if (e.key === 'Enter') {
					let attr = parseInt($(this).attr('id-embalase'))-1
					if ($(`.obat-racik[ajax-id="${attr}"]`).length) {
						$(`.obat-racik[ajax-id="${attr}"]`).focus()
					}
					else {
						$('#simpan-racikan').focus()
					}
				}
			}
		})

		$(document).on('keyup','.embalase-tanpa-racik',function(e){
			if ($(this).val() != '') {
				if (e.key === 'Enter') {
					let attr = parseInt($(this).attr('id-embalase'))-1
					if ($(`.obat-tanpa-racik[ajax-id="${attr}"]`).length) {
						$(`.obat-tanpa-racik[ajax-id="${attr}"]`).focus()
					}
					else {
						$('#simpan-resep').focus()
					}
				}
			}
		})

	    $('.form-post-racik').on('keydown','input,select,textarea',function(e){
	        var self = $(this),
	            form = self.parents('form:eq(0)'),
	            focusable,
	            next
	            ;
	        if (e.keyCode == 13) {
	            focusable = form.find('input,a,select,button,textarea').filter(':visible');
	            next = focusable.eq(focusable.index(this)+1);
	            console.log(next);
	            if (next.length) {
	                next.focus();
	            }
	            else {
	                next.submit();
	            }
	            return false;
	        }
	    });

	    $('.form-post-tanpa-racik').on('keydown','input,select,textarea',function(e){
	        var self = $(this),
	            form = self.parents('form:eq(0)'),
	            focusable,
	            next
	            ;
	        if (e.keyCode == 13) {
	            focusable = form.find('input,a,select,button,textarea').filter(':visible');
	            next = focusable.eq(focusable.index(this)+1);
	            console.log(next);
	            if (next.length) {
	                next.focus();
	            }
	            else {
	                next.submit();
	            }
	            return false;
	        }
	    });

	    $('#select-pasien').on('keydown','input,select,textarea',function(e){
	        var self = $(this),
	            form = self.parents('form:eq(0)'),
	            focusable,
	            next
	            ;
	        if (e.keyCode == 13) {
	            focusable = form.find('input,a,select,button,textarea').filter(':visible');
	            next = focusable.eq(focusable.index(this)+1);
	            console.log(next);
	            if (next.length) {
	                next.focus();
	            }
	            else {
	                next.submit();
	            }
	            return false;
	        }
	    });

	    $('#input-pasien').on('keydown','input,select,textarea',function(e){
	        var self = $(this),
	            form = self.parents('form:eq(0)'),
	            focusable,
	            next
	            ;
	        if (e.keyCode == 13) {
	            focusable = form.find('input,a,select,textarea').filter(':visible');
	            next = focusable.eq(focusable.index(this)+1);
	            console.log(next);
	            if (next.length) {
	                next.focus();
	            }
	            else {
	                next.submit();
	            }
	            return false;
	        }
	    });

	    $('input[name="nama_dokter"]').keydown(function(e){
	    	if (e.key === 'Enter') {
	    		$('#diskon-resep').focus();
	    	}
	    })

	    $('#diskon-resep').keydown(function(e){
	    	if (e.key === 'Enter') {
	    		$('#bayar-racik').focus();
	    	}
	    })

	    $('#bayar-racik').keydown(function(e){
	    	if (e.key === 'Enter') {
	    		$('#bayar-resep').focus();
	    	}
	    })

	    $('.keterangan-racik').change(function(e){
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
	    		$('.obat-racik').focus()
		    }, 1);
	    })

	    $('select[name="pasien"]').change(function(e){
	    	let val = $(this).val()
	    	$.ajax({
	    		url: `${base_url}/ajax/get-info-pasien-resep`,
	    		data: {id_pasien: val},
	    	})
	    	.done(function(done) {
	    		$('#alamat-pasien-readonly').val(done.alamat_pasien);
	    		$('#nomor-telepon-pasien-readonly').val(done.nomor_telepon_pasien);
	    	})
	    	.fail(function(error) {
	    		console.log(error);
	    	});
	    	
	    	if ($('#input-dokter').hasClass('form-hide')) {
	    		$('select[name="dokter"]').select2('open')
	    	}
	    	else {
			    setTimeout(function() {
			        $('.select2-container-active').removeClass('select2-container-active');
			        $(':focus').blur();
	    			$('input[name="nama_dokter"]').focus()
			    }, 1);
	    	}
	    })

	    $('select[name="dokter"]').change(function(e){
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
	    		$('#diskon-resep').focus()
		    }, 1);
	    })

		$('#bayar-resep').click(function(){
			if ($(this).attr('attr-submit') != 'submit') {
				$(this).attr('disabled','disabled')
				$(this).attr('attr-submit','submit')
				$('#form-post-keterangan').submit();
			}
		})
	})
</script>
@endsection