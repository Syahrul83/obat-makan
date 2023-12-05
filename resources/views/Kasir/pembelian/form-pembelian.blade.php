@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Form Pemasukan</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-6">
				@if (session()->has('message'))
				<div class="alert alert-success alert-dismissible">
					{{session('message')}} <button class="close" data-dismiss="alert">X</button>
				</div>
				@endif
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/kasir/data-pembelian') }}">
							<button class="btn btn-default" type="button"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<form action="{{url('/kasir/data-pembelian/save')}}" id="form-simpan" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Kode Pembelian</label>
									<input type="text" name="kode_pembelian" class="form-control" readonly="readonly">
								</div>
								<div class="form-group">
									<label for="">Supplier</label>
									<select name="supplier" class="form-control select2" id="supplier-masuk" required="required" autofocus>
										<option value="" selected="" disabled="">=== Pilih Supplier ===</option>
										@foreach ($supplier as $element)
										<option value="{{ $element->id_supplier }}">{{ $element->nama_supplier }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="">Nomor Faktur</label>
									<input type="text" name="nomor_faktur" class="form-control" id="nomor-faktur" required>
								</div>
								<div class="form-group">
									<label for="">Jenis Beli</label>
									<select name="jenis_beli" class="form-control select2 jenis-beli" id="jenis-beli" required="required">
										<option value="" selected="" disabled="">=== Pilih Jenis Beli ===</option>
										<option value="cash">Cash</option>
										<option value="kredit">Kredit</option>
										<option value="konsinyasi">Konsinyasi</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Tanggal Terima</label>
									{{-- <input type="date" class="form-control" id="tanggal-terima" name="tanggal_terima" value="{{isset($row)?$row->tanggal_terima:''}}" required="required"> --}}
									<input type="text" class="form-control datepicker" id="tanggal-terima" name="tanggal_terima" value="{{isset($row) ? $row->tanggal_terima : ''}}" placeholder="dd-mm-yyyy" required="required">
								</div>
								<label for="" id="tanggal-terima-label" style="display:block;"></label>
								
								<label for="">Waktu Hutang</label>
								<div class="input-group" style="margin-bottom:10px;">
									<input type="number" class="form-control" id="waktu-hutang" name="waktu_hutang" placeholder="Isi Waktu Hutang">
									<span class="input-group-addon">Hari</span>
								</div>
								<div class="form-group">
									<label for="">Tanggal Jatuh Tempo</label>
									<input type="text" class="form-control" id="tanggal-jatuh-tempo" name="tanggal_jatuh_tempo"  placeholder="dd-mm-yyyy" readonly="readonly">
								</div>
								<label for="" id="tanggal-jatuh-tempo-label" style="display: block;"></label>
								<label for="">Jenis PPn</label>
								<div class="form-group">
									<select name="jenis_ppn" id="jenis-ppn" class="form-control select2" required="required">
										<option value="" selected="" disabled="">=== Pilih Jenis PPn ===</option>
										<option value="include-ppn">Include PPn</option>
										<option value="exclude-ppn">Exclude PPn</option>
										<option value="no-ppn">No PPn</option>
									</select>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button class="btn btn-primary">Simpan <span class="fa fa-save"></span></button>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<form class="form-beli">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Obat</label>
									<select name="obat_beli" class="form-control select2" id="obat-beli" required="required">
										<option value="" selected="" disabled="">=== Pilih Obat ===</option>
										@foreach ($obat as $element)
										<option value="{{ $element->id_obat }}">{{ $element->nama_obat }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="">Jumlah</label>
									<input type="number" name="jumlah" class="form-control" id="jumlah" required="required" placeholder="Isi Jumlah Masuk">
								</div>
								<div class="form-group">
									<label for="">Satuan Obat</label>
									<input type="text" name="satuan_obat" class="form-control" id="satuan-obat" disabled="disabled">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">Hna</label>
									<input type="text" class="form-control" name="harga_obat" id="harga-modal" value="0">
									<label for="" id="hna">Rp. 0,00</label>
								</div>
								<label for="">Disc 1</label>
								<div class="input-group" style="margin-bottom:10px;">
									<input type="text" class="form-control" name="disc_1" id="disc-1">
									<span class="input-group-addon">%</span>
								</div>
								<label for="">Disc 2</label>
								<div class="input-group" style="margin-bottom:10px;">
									<input type="text" class="form-control" name="disc_2" id="disc-2">
									<span class="input-group-addon">%</span>
								</div>
								<label for="">Disc 3</label>
								<div class="input-group" style="margin-bottom:10px;">
									<input type="text" class="form-control" name="disc_3" id="disc-3">
									<span class="input-group-addon">%</span>
								</div>
								<div class="form-group">
									<button class="btn btn-primary" id="input-masuk">Input</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-body">
						<table class="table table-hover force-fullwidth beli-obat">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Jumlah</th>
								<th>Harga</th>
								{{-- <th>PPn</th> --}}
								<th>Disc 1</th>
								<th>Disc 2</th>
								<th>Disc 3</th>
								<th>Total</th>
								<th>#</th>
							</thead>
							<tbody>

							</tbody>
							<tfoot>
								<tr>
									<td><b>DPP : </b></td>
									<td><b id="dpp-total">Rp. 0,00</b></td>
									<td><b>PPN : </b></td>
									<td><b id="ppn-total">Rp. 0,00</b></td>
									<td><b>Diskon : </b></td>
									<td><b id="diskon-total">Rp. 0,00</b></td>
									<td><b>Total Semua : </b></td>
									<td><b id="total-semua">Rp. 0,00</b></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div> 
	</section>
</div>
@endsection

@section('js')
<script>
	$(function(){
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

		$('#supplier-masuk').select2('open')

	    $('#form-simpan').on('keydown','input,select,textarea',function(e){
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

	    $('.form-beli').on('keydown','input,select,textarea',function(e){
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

		$('#tanggal-terima').change(function(){
			let val                 = $(this).val()
			let waktu_hutang        = $('#waktu-hutang').val();
			let tanggal_jatuh_tempo = $('#tanggal-jatuh-tempo');

			$('#tanggal-terima-label').html(`<b>${convert_date(val)}</b>`)

			if (waktu_hutang != '' && val != '') {
				if (waktu_hutang != 0) {
					let result = new Date(reverse_date(val));
					result.setDate(result.getDate() + parseInt(waktu_hutang));

					let month = result.getMonth()+1;
					month     = `0${month}`.slice(-2);

					let date_ = result.getDate();
					date_     = `0${date_}`.slice(-2);

					let new_date = `${date_}-${month}-${result.getFullYear()}`
					// let val_date = `${result.getFullYear()}-${month}-${date_}`

					$('#tanggal-jatuh-tempo').val(new_date)
					$('#tanggal-jatuh-tempo-label').html(`<b>${convert_date(new_date)}</b>`)
				}
			}

			$('#waktu-hutang').focus()
		})

	    $('#supplier-masuk').change(function(e){
	    	let val = $(this).val()
	    	$.ajax({
	    		url: "{{ url('/ajax/get-singkatan-supplier') }}",
	    		data: {id_supplier: val},
	    	})
	    	.done(function(done) {
	    		$('input[name="nomor_faktur"]').val(done);
	    	})
	    	.fail(function(error) {
	    		console.log(error);
	    	});

		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
		    	$('#nomor-faktur').focus()
		    }, 1);
	    })

	    $('#nomor-faktur').change((e) => {
	    	$('#jenis-beli').select2('open')
	    })

		$('.jenis-beli').change(function() {
			let val = $(this).val();
			$.ajax({
				url: "{{ url('/ajax/get-kode-pembelian') }}/"+val,
			})
			.done(function(done) {
				$('input[name="kode_pembelian"]').val(done)
			})
			.fail(function(error) {
				console.log(error);
			});
			
			if (val == 'cash') {
				$('#waktu-hutang').val(0)
				$('#waktu-hutang').attr('readonly')
				$('#tanggal-jatuh-tempo').val('')
			}
			else {
				$('#waktu-hutang').val('')
				$('#waktu-hutang').removeAttr('readonly')
				$('#tanggal-jatuh-tempo').val('')
			}
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
           		$("#tanggal-terima").datepicker().focus();
		    }, 1);
		});

		$('#waktu-hutang').keyup(function(e){
			let val                 = $(this).val();
			let tanggal_terima      = $('#tanggal-terima').val();
			let tanggal_jatuh_tempo = $('#tanggal-jatuh-tempo');
			if (val != '' && tanggal_terima != '') {

				let result = new Date(reverse_date(tanggal_terima));
				result.setDate(result.getDate() + parseInt(val));

				let month = result.getMonth()+1;
				month     = `0${month}`.slice(-2);

				let date_ = result.getDate();
				date_     = `0${date_}`.slice(-2);

				let new_date = `${date_}-${month}-${result.getFullYear()}`
				// let val_date = `${result.getFullYear()}-${month}-${date_}`

				$('#tanggal-jatuh-tempo').val(new_date)
				$('#tanggal-jatuh-tempo-label').html(`<b>${convert_date(new_date)}</b>`)
			}
		})

		$('#waktu-hutang').change(function(){
			$('#jenis-ppn').focus()
		})

		$('#obat-beli').change(function(){
			let val       = $(this).val();
			let jenis_ppn = $('#jenis-ppn').val();
	        $.ajax({
	            url: "{{ url('ajax/get-info-obat/') }}"+'/'+val,
	        })
	        .done(function(done) {
	        	if (jenis_ppn == 'include-ppn') {
		            $('#harga-modal').val(done.harga_modal_ppn)
		            $('#hna').html(rupiah_format(done.harga_modal_ppn))
	        	}
	        	else {
		            $('#harga-modal').val(done.hna)
		            $('#hna').html(rupiah_format(done.hna))
	        	}
	            $('#satuan-obat').val(done.satuan_obat)
	        })
	        .fail(function() {
	            console.log("error")
	        })
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
           		$("#jumlah").focus();
		    }, 1);
		})

		$('#jumlah').keydown(function(e){
			if (e.key === 'Enter') {
				$('#harga-modal').focus()
			}
		})

		$('#disc-3').keydown(function(e){
			if (e.key === 'Enter') {
				$('#input-masuk').focus()
			}
		})

		$('#jenis-ppn').change(function(e){
			// let val = $(this).val()
			
			// $('#ppn').removeAttr('required')

			// if (val == 'include-ppn') {
			// 	if (!$('#input-ppn').hasClass('form-hide')) {
			// 		$('#input-ppn').addClass('form-hide')		
			// 	}
			// 	// $('#ppn > option[value="10"]').attr('selected','selected')
			// }
			// else if (val == 'exclude-ppn') {
			// 	if ($('#input-ppn').hasClass('form-hide')) {
			// 		$('#input-ppn').removeClass('form-hide')
			// 	}
			// 	$('#ppn').attr('required')
			// }
			// else if (val == 'no-ppn') {} {
			// 	if (!$('#input-ppn').hasClass('form-hide')) {
			// 		$('#input-ppn').addClass('form-hide')		
			// 	}
			// }
		    setTimeout(function() {
		        $('.select2-container-active').removeClass('select2-container-active');
		        $(':focus').blur();
           		$("#obat-beli").select2('open');
		    }, 1);
		})

		$('#harga-modal').keyup(function(){
			let val = $(this).val()

	        $('#hna').html(rupiah_format(val))
		})

		$('.form-beli').submit(function(e){
			e.preventDefault();
			let data      = $(this).serializeArray()
			let jenis_ppn = $('#jenis-ppn').val()
			data.push({name:'jenis_ppn',value:jenis_ppn})
        	console.log(data)
	        $.ajaxSetup({
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            }
	        });

			$.ajax({
				url: "{{ url('ajax/input-obat-beli') }}",
				type: 'POST',
				data: data,
			})
			.done(function(done) {
	        	$('#obat-beli').select2('destroy')
	        	$('#obat-beli').prop('selectedIndex',0)
	        	$('#obat-beli').select2()
	        	$('#obat-beli').select2('open')
	        	$('#ppn').prop('selectedIndex',0)
	        	$('#jumlah').val('')
	        	$('#satuan-obat').val('')
	        	$('#harga-modal').val('')
	        	$('#hna').html(rupiah_format(0))
	        	$('#disc-1').val('')
	        	$('#disc-2').val('')
	        	$('#disc-3').val('')

				// var get_ppn   = $('select[name="ppn"]').val()
				// var harga_ppn = 0

	   //      	if (get_ppn == '10') {
	   //      		harga_ppn = parseInt(done.total_semua) + parseInt((done.total_semua * 10) / 100)
	   //      	}
	   //      	else {
	   //      		harga_ppn = parseInt(done.total_semua)
	   //      	}
				$('.beli-obat').append(done.data_beli.data_html)
				$('#dpp-total').html(`<b>${rupiah_format(done.dpp)}</b>`)
				$('#ppn-total').html(`<b>${rupiah_format(done.ppn)}</b>`)
				$('#diskon-total').html(`<b>${rupiah_format(done.discount)}</b>`)
				$('#total-semua').html(`<b>${rupiah_format(done.total_semua)}</b>`)
				$('td.number-beli').each((i,v) => {
			        $(v).text(i+1)
			    })
			})
			.fail(function(error) {
				console.log(error)
			})
			.always(function() {
				console.log('oke')
			})
		})

	    $('table').on('click','.delete-beli',function(){
	        let get_id = $(this).attr('id-delete');

	        $(this).html(`
	          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
	          Loading...
	          `)

	        $.ajax({
				url: "{{ url('ajax/delete-beli/') }}"+'/'+get_id,
	            type: 'GET',
	        })
	        .done(function(done) {

				// var get_ppn   = $('select[name="ppn"]').val()
				// var harga_ppn = 0

	        	// if (get_ppn == '10') {
	        	// 	harga_ppn = parseInt(done) + parseInt((done * 10) / 100)
	        	// }
	        	// else {
	        	// 	harga_ppn = parseInt(done)
	        	// }
	            $('#total-semua').html(`<b>${rupiah_format(done.total_semua)}</b>`)
	            $('#ppn-total').html(`<b>${rupiah_format(done.ppn)}</b>`)
	            $('#dpp-total').html(`<b>${rupiah_format(done.dpp)}</b>`)
	            $('#diskon-total').html(`<b>${rupiah_format(done.discount)}</b>`)
				$('td.number-beli').each((i,v) => {
			        $(v).text(i+1)
			    })
	        })
	        .fail(function() {
	            console.log("error");
	        });
	        
	        $(this).closest('tr').remove()
	    });
	});
</script>
@endsection
