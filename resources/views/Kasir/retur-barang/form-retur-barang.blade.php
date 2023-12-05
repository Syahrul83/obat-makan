@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Retur Barang</h1>
	</section>

	<section class="content">
		<div class="row">
			@if (session()->has('message'))
			<div class="alert alert-success alert-dismissible">
				{{session('message')}} <button class="close" data-dismiss="alert">X</button>
			</div>
			@elseif (session()->has('log'))
			<div class="alert alert-danger alert-dismissible">
				{{session('log')}} <button class="close" data-dismiss="alert">X</button>
			</div>
			@endif
			<form action="{{url('/kasir/retur-barang/save')}}" method="POST">
				@csrf
				<div class="col-md-12">
					<div class="box box-default">
						<div class="box-header">
							<a href="{{ url('/kasir/retur-barang') }}">
								<button class="btn btn-default" type="button"><span class="fa fa-arrow-left"></span> Kembali</button>
							</a>
						</div>
						<div class="box-body">
							<div class="form-group">
								<label for="">Tanggal Retur</label>
								<input type="text" name="tanggal_retur" class="form-control datepicker" value="{{ reverse_date(date('Y-m-d')) }}" placeholder="dd-mm-yyyy" readonly>
							</div>
							<div class="form-group">
								<label for="">Nomor Retur</label>
								<input type="text" name="nomor_retur" class="form-control" value="{{ $nomor_retur }}" readonly>
							</div>
							<div class="form-group">
								<label for="">Tanggal Transaksi</label>
								<input type="text" name="tanggal_transaksi" class="form-control datepicker" placeholder="dd-mm-yyyy" required="required">
							</div>
							<div class="form-group">
								<label for="">Nomor Transaksi</label>
								<select name="nomor_transaksi" class="form-control select2" required disabled>
									<option value="" selected disabled>=== Pilih Nomor Transaksi ===</option>
								</select>
							</div>
							{{-- <div class="form-group">
								<label for="">Keterangan</label>
								<input type="text" name="keterangan" class="form-control" placeholder="Isi Keterangan" required="required">
							</div> --}}
							<input type="hidden" name="total_harga" id="total-harga" value="0">
						</div>
						<div class="box-footer">
							<button class="btn btn-primary">
								Simpan <span class="fa fa-save"></span>
							</button>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="box box-default">
						<div class="box-header with-border">
							<input type="text" class="form-control" id="search-nama-obat" placeholder="Cari Nama Obat">
						</div>
						<div class="box-body" id="retur-barang-layout">
							{{-- <div class="retur-barang-input" id="retur-barang-input">
								<div class="col-md-4">
									<div class="form-group">
										<label for="">Obat</label>
										<input type="text" class="form-control">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="">Stok Transaksi</label>
										<input type="number" class="form-control" name="stok_transaksi[]" readonly>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="">Stok Retur</label>
										<input type="number" class="form-control" name="stok_retur[]" placeholder="Isi Stok Retur" required="required">
									</div>
								</div>
							</div> --}}

							<table class="table table-hover" id="stok-retur-input">
								<thead>
									<th>No.</th>
									<th>Nama Obat</th>
									<th>Stok Transaksi</th>
									<th>Stok Retur</th>
									<th>Harga Retur</th>
									<th></th>
									<th></th>
									<th>#</th>
								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<th>Total</th>
									<th colspan="3">:</th>
									<th><b id="total-label">Rp 0,00</b></th>
								</tfoot>
							</table>
						</div>
						{{-- <div class="box-footer">
							<button class="btn btn-success" id="tambah-input-retur" type="button">Tambah Input</button>
							<button class="btn btn-danger btn-hide" id="hapus-input-retur" type="button">Hapus Input</button>
						</div> --}}
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
@endsection

@section('js')
<script>
	$(() => {
	    $('#tambah-input-retur').click(() => {
	        $('#hapus-input-retur').removeClass('btn-hide');
	        $('#obat-retur').select2('destroy')
	        $('#retur-barang-input').clone().appendTo('#retur-barang-layout').find('input').val('');
	        $('.obat-retur').select2()
	    })

	    $('#hapus-input-retur').click(function() {
	        $('#retur-barang-layout #retur-barang-input').last().remove()
	        if ($('.retur-barang-input').length == 1) {
	            $(this).addClass('btn-hide')
	        }
	    })

	    $('input[name="tanggal_transaksi"]').change(function() {
	    	let val = $(this).val()
	    	$.ajax({
	    		url: `${base_url}/ajax/get-nomor-transaksi`,
	    		// type: 'default GET (Other values: POST)',
	    		// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
	    		data: {tanggal_transaksi: val},
	    	})
	    	.done(function(done) {
	    		$('select[name="nomor_transaksi"]').removeAttr('disabled')
	    		$('select[name="nomor_transaksi"]').html(done)
	    		// $('#retur-barang-input').html(done)
	    	})
	    	.fail(function() {
	    		console.log("error");
	    	})
	    	.always(function() {
	    		console.log("complete");
	    	});

	    	$('select[name="nomor_transaksi"]').change(function(){
				$('#total-label').html(rupiah_format(0))
				$('#total-harga').val(0)
	    		let val = $(this).val()
	    		$.ajax({
	    			url: `${base_url}/ajax/get-obat-by-transaksi`,
	    			data: {kode_transaksi: val},
	    		})
	    		.done(function(done) {
	    			$('#stok-retur-input > tbody').html(done)
	    		})
	    		.fail(function(fail) {
	    			console.log(fail)
	    		});
	    		
	    	})
	    	
	    })

	    $(document).on('keyup','input[name="stok_retur[]"]',function(){
	        let get_val 		= $(this).val(),
	        	get_id          = $(this).attr('id-stok-retur'),
	        	get_grand_total = $('input[name="total_harga"]').val()
				get_harga 		= $(`input[id-harga-satuan="${get_id}"]`).val(),
				get_total 		= $(`input[name="harga_retur[]"][id-harga="${get_id}"]`).val()
				get_hja_obat 	= $(`input[id-harga-satuan="${get_id}"]`).attr('hja-obat')

				get_val = get_val == '' ? 0 : get_val
			if (get_total == 0 || get_total == '' || get_total == null) {
				let kalkulasi = 0
				if (get_hja_obat != 'relasi') {
					kalkulasi = round_up_thousand(parseInt(get_val) * parseInt(get_harga),1000)
				}
				else {
					kalkulasi = parseInt(get_val) * parseInt(get_harga)
				}
				get_grand_total = parseInt(get_grand_total) + parseInt(kalkulasi)
				$(`input[id-harga-terbilang="${get_id}"]`).val(rupiah_format(kalkulasi))
				$(`input[name="total_harga"]`).val(get_grand_total)
				$(`input[name="harga_retur[]"][id-harga="${get_id}"]`).val(kalkulasi)
				$('#total-label').html(rupiah_format(get_grand_total))
			}
			else {
				if (get_hja_obat != 'relasi') {
					let kalkulasi = round_up_thousand(parseInt(get_val) * parseInt(get_harga),1000)
				}
				else {
					let kalkulasi = parseInt(get_val) * parseInt(get_harga)
				}
				// let kalkulasi   = round_up_thousand(parseInt(get_val) * parseInt(get_harga),1000)
				get_grand_total = (parseInt(get_grand_total) - parseInt(get_total)) + parseInt(kalkulasi)
				$(`input[id-harga-terbilang="${get_id}"]`).val(rupiah_format(kalkulasi))
				$(`input[name="total_harga"]`).val(get_grand_total)
				$(`input[name="harga_retur[]"][id-harga="${get_id}"]`).val(kalkulasi)
				$('#total-label').html(rupiah_format(get_grand_total))
			}
	    })

	    $('table').on('click','.delete-retur',function(){
	        var get_id          = $(this).attr('id-delete'),
				get_total 		= $(`input[name="harga_retur[]"][id-harga="${get_id}"]`).val(),
				get_grand_total = $('#total-harga').val()

			// console.log({get_total,get_grand_total})
			get_total       = get_total == '' ? 0 : get_total
			get_grand_total = parseInt(get_grand_total) - parseInt(get_total)
			$('#total-harga').val(get_grand_total)
			$('#total-label').html(rupiah_format(get_grand_total))
	        $(this).closest('tr').remove();
	    });

		$('#search-nama-obat').keyup(function(){	
		  var input, filter, table, tr, td, i, txtValue;
			input  = document.getElementById("search-nama-obat");
			filter = input.value.toUpperCase();
			table  = document.getElementById("stok-retur-input");
			tr     = table.getElementsByTagName("tr");
		  for (i = 0; i < tr.length; i++) {
		    td = tr[i].getElementsByTagName("td")[1];
		    if (td) {
		      txtValue = td.textContent || td.innerText;
		      if (txtValue.toUpperCase().indexOf(filter) > -1) {
		        tr[i].style.display = "";
		      } else {
		        tr[i].style.display = "none";
		      }
		    }       
		  }
		});

		$(document).on('keyup','.harga-fleksibel',function(){
			let val                    = $(this).val()
			let attr                   = $(this).attr('id-harga-fleksibel-input')
			let harga_fleksibel_hidden = $(`input[id-harga-fleksibel="${attr}"]`).val()
			let harga_retur            = $(`input[name="harga_retur[]"][id-harga="${attr}"]`).val()
			let harga_retur_backup     = $(`input[name="harga_retur_backup[]"][id-harga="${attr}"]`).val()
			let harga_total            = $('#total-harga').val()
			let total_label            = $('#total-label')
			if (harga_total != 0) {
				if (harga_retur != 0 || harga_retur != '') {

					if (val != '') {
						harga_total = parseInt(harga_total) - parseInt(harga_retur)
						console.log(harga_total)
						if (harga_retur != 0) {
							$(`input[name="harga_retur_backup[]"][id-harga="${attr}"]`).val(harga_retur)
						}
						$(`input[name="harga_retur[]"][id-harga="${attr}"]`).val(0)

						if (harga_fleksibel_hidden == '' || harga_fleksibel_hidden == 0) {
							harga_total = parseInt(harga_total) + parseInt(val)
							$('#total-harga').val(harga_total)
							total_label.html(rupiah_format(harga_total))
							$(`input[id-harga-fleksibel="${attr}"]`).val(val)
						}
						else {
							harga_total = parseInt(harga_total) - parseInt(harga_fleksibel_hidden)
							harga_total = parseInt(harga_total) + parseInt(val)
							$('#total-harga').val(harga_total)
							total_label.html(rupiah_format(harga_total))
							$(`input[id-harga-fleksibel="${attr}"]`).val(val)
							$(`label[id-harga-fleksibel-label="${attr}"]`).html(rupiah_format(val))
						}

					}
					else {
						harga_total = parseInt(harga_total) - parseInt(harga_fleksibel_hidden)
						harga_total = parseInt(harga_total) + parseInt(harga_retur_backup)
						$(`input[name="harga_retur[]"][id-harga="${attr}"]`).val(harga_retur_backup)
						$(`input[name="harga_retur_backup[]"][id-harga="${attr}"]`).val(0)
						$(`input[id-harga-fleksibel="${attr}"]`).val(0)
						$(`label[id-harga-fleksibel-label="${attr}"]`).html(rupiah_format(val))
						$('#total-harga').val(harga_total)
						$('#total-label').html(rupiah_format(harga_total))
					}
				}
			}
		})
	})
</script>
@endsection