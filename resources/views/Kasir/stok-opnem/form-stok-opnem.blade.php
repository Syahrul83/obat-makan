@extends('layout.app-kasir')

@section('content')
{{-- @include('Admin.navbar') --}}
{{-- <div class="content-wrapper"> --}}
	<section class="content-header"></section>
	<section class="content">
		<div class="row">
			<form action="{{ url('/kasir/stok-opnem/save') }}" id="stok-opnem-save" method="POST">
				{{csrf_field()}}
				<div class="col-md-12">
					<div class="box box-default">
						<div class="box-header with-border">
							<a href="{{ url('/kasir/stok-opnem') }}">
								<button class="btn btn-default" type="button"><span class="fa fa-arrow-left"></span> Kembali</button>
							</a>
							@if (session()->has('message'))
							<div class="alert alert-success alert-dismissible">
								{{session('message')}} <button class="close" data-dismiss="alert">X</button>
							</div>
							@endif
						</div>
						<div class="box-body">
							<div class="form-group">
								<label for="">Tanggal Stok Opnem</label>
								<input type="text" name="tanggal_stok_opnem" class="form-control datepicker" placeholder="dd-mm-yyyy" value="{{ isset($tanggal_stok_opnem) ? reverse_date($tanggal_stok_opnem) : '' }}" required="required" autofocus>
							</div>
							<div class="form-group">
								<label for="">Keterangan</label>
								<input type="text" name="keterangan" value="{{ isset($keterangan) ? $keterangan : '' }}" class="form-control">
							</div>
						</div>
						<input type="hidden" name="id" value="{{ isset($id) ? $id : '' }}">
					</div>
				</div>
			</form>
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<input type="text" class="form-control" id="search-nama-obat" placeholder="Cari Nama Obat">
					</div>
					<div class="box-body">
						<table class="table table-hover table-bordered" id="stok-opnem-input">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Satuan</th>
								<th>Harga Hna</th>
								<th>Stok Komputer</th>
								<th>Stok Fisik</th>
								<th>Stok Selisih</th>
								<th>Nilai</th>
								<th>Tanggal Exp</th>
							</thead>
							<tbody>
								@php
									$data  = 160;
									$hasil = ceil($count_obat / 160);
									$no    = 0;
								@endphp
								@for ($i = 1; $i <= $hasil; $i++)
								@php
									$hitung = ($i>1) ? ($i * $data) - $data : 0;
								@endphp
								<form id-form="{{ $i }}">
								@foreach ($paging_obat->obatPaging($hitung,$data) as $key => $value)
								@php
									$nomor = $no+$key+1;
								@endphp
								<tr>
									<td>{{ $nomor }}</td>
									<td>{{ $value->nama_obat }}<input type="hidden" name="id_obat[]" value="{{ $value->id_obat }}"></td>
									<td>{{ $value->satuan_obat }}</td>
									<td>{{ format_rupiah($value->harga_modal) }}<input type="hidden" name="harga_modal[]" id-harga-modal="{{ $nomor }}" value="{{ $value->harga_modal }}"></td>
									<td>{{ $value->stok_obat }}<input type="hidden" name="stok_komputer[]" id-stok-komputer="{{ $nomor }}" value="{{ $value->stok_obat }}"></td>
									<td><input type="number" class="form-control" name="stok_fisik[]" id-stok-fisik="{{ $nomor }}" id-obat="{{ $value->id_obat }}"></td>
									<td><input type="text" class="form-control" name="stok_selisih[]" id-stok-selisih="{{ $nomor }}" id-obat="{{ $value->id_obat }}" readonly="readonly"></td>
									<td><input type="text" class="form-control" id-tampil-nilai="{{ $nomor }}" readonly="readonly"><input type="hidden" id-obat="{{ $value->id_obat }}" name="sub_nilai[]" id-sub-nilai="{{ $nomor }}"></td>
									<td>{{ human_date($value->tanggal_expired) }}</td>
								</tr>
								@endforeach
								@php
									$no = $nomor;
								@endphp
								<tr align="right">
									<td colspan="5"><button class="btn btn-info btn-act" name="btn_act" value="input-obat" id-form="{{ $i }}">Input Obat</button></td>
								</tr>
								</form>
								@endfor
							</tbody>
						</table>
					</div>
					<div class="box-footer">
						<button class="btn btn-primary" form="stok-opnem-save" value="input-selesai">Selesai <span class="fa fa-save"></span></button>
					</div>
				</div>
			</div>
		</div>
	</section>
{{-- </div> --}}
@endsection

@section('js')
<script>
	$(() => {
		$('.datepicker').datepicker('show')

		$('.btn-act').click(function(e){
			e.preventDefault()
			let id_form            = $(this).attr('id-form')
			let tanggal_stok_opnem = $('input[name="tanggal_stok_opnem"]').val()
			let keterangan         = $('input[name="keterangan"]').val()
			let id_stok_opnem      = $('input[name="id"]').val()
			let get_form		   = $(`form[id-form="${id_form}"]`).serialize()
			let data_form 		   = `${get_form}&tanggal_stok_opnem=${tanggal_stok_opnem}&keterangan=${keterangan}&id_stok_opnem=${id_stok_opnem}`

			if (tanggal_stok_opnem == '') {
				alert('Isi Dulu Tanggak Stok Opnem')
				$('input[name="tanggal_stok_opnem"]').focus()
			}
			else {
				$('.btn-act').attr('disabled','disabled')
				$(this).html('Loading ...')
				$.ajax({
					url: `${base_url}/kasir/stok-opnem/input-sebagian`,
					type: 'POST',
					data: data_form,
				})
				.done(function(done) {
					$('.btn-act').removeAttr('disabled')
					$(`.btn-act[id-form="${id_form}"]`).removeClass('btn-info')
					$(`.btn-act[id-form="${id_form}"]`).addClass('btn-success')
					$(`.btn-act[id-form="${id_form}"]`).html('Sudah Terinput')
					$('input[name="id"]').val(done.id_stok_opnem)
					alert(done.message)
				})
				.fail(function(error) {
					console.log(error);
				});
			}
		})

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

		$('input[name="stok_fisik[]"]').keyup(function(){
			let val           = $(this).val();
			let getAttr       = $(this).attr('id-stok-fisik');
			// let getUuid 	  = $(this).attr('uuid');
			// let getIdObat 	  = $(this).attr('id-obat');
			let harga_modal   = $(`input[name="harga_modal[]"][id-harga-modal="${getAttr}"]`).val();
			let stok_komputer = $(`input[name="stok_komputer[]"][id-stok-komputer="${getAttr}"]`).val();
			let stok_selisih  = $(`input[name="stok_selisih[]"][id-stok-selisih="${getAttr}"]`);
			let tampil_nilai  = $(`input[id-tampil-nilai="${getAttr}"]`);
			let sub_nilai     = $(`input[name="sub_nilai[]"][id-sub-nilai="${getAttr}"]`);
			// let key_local_storage = `${getUuid}-${getIdObat}`

			if (val == '') {
				stok_selisih.val('')
				sub_nilai.val('')
				tampil_nilai.val('')
			}
			
			else if (val != null || val != '') {
				let selisih   = parseInt(stok_komputer) - parseInt(val)
				let total_sub = parseInt(val) * parseInt(harga_modal)

				sub_nilai.val(total_sub)
				tampil_nilai.val(rupiah_format(total_sub))
				stok_selisih.val(selisih)
			}
			// localStorage.setItem('')
		})

		$('#search-nama-obat').keyup(function(){	
		  var input, filter, table, tr, td, i, txtValue;
			input  = document.getElementById("search-nama-obat");
			filter = input.value.toUpperCase();
			table  = document.getElementById("stok-opnem-input");
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
	});
</script>
@endsection