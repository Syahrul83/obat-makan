@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Transaksi</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<form action="{{ url('/kasir/kartu-stok/cetak') }}">
							<div class="col-md-2">
								<input type="text" class="form-control datepicker" placeholder="dd-mm-yyyy" name="tanggal_dari">
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control datepicker" placeholder="dd-mm-yyyy" name="tanggal_sampai">
							</div>
							<div class="col-md-2">
								<select name="obat_cari" class="form-control select2">
									<option value="" selected="" disabled="">=== Pilih Obat ===</option>
									@foreach ($obat as $element)
									<option value="{{ $element->id_obat }}">{{ $element->nama_obat }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2">
								<button class="btn btn-success cari" type="button">Cari</button>
								<button class="btn btn-primary">Cetak</button>
							</div>
						</form>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover kartu-stok force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Tanggal</th>
								<th>Nomor</th>
								<th>Layanan</th>
								<th>Beli</th>
								<th>Jual</th>
								<th>Retur Barang</th>
								<th>Saldo</th>
								<th>Keterangan</th>
							</thead>
							<tbody>
								
							</tbody>
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

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

        var kartu_stok = $('.kartu-stok').DataTable({
            processing:true,
            serverSide:true,
            ajax:{
            	url: "{{ url('datatables/kartu-stok') }}",
            	data:function(d){
					d.tanggal_dari   = $('input[name="tanggal_dari"]').val();
					d.tanggal_sampai = $('input[name="tanggal_sampai"]').val();
					d.obat_cari      = $('select[name="obat_cari"]').val();
            	}
            },
            columns:[
                {data:'id_kartu_stok',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'tanggal_pakai',name:'tanggal_pakai'},
                {data:'nomor_stok',name:'nomor_stok'},
                {data:'layanan',name:'layanan'},
                {data:'beli',name:'beli'},
                {data:'jual',name:'jual'},
                {data:'retur_barang',name:'retur_barang'},
                {data:'saldo',name:'saldo'},
                {data:'keterangan',name:'keterangan'},
            ],
            scrollCollapse: true,
            columnDefs: [ {
            sortable: true,
            "class": "index",
            }],
            scrollX:true,
            order: [[ 0, 'desc' ]],
            responsive:true,
            fixedColumns: true
        });
        kartu_stok.on( 'order.dt search.dt', function () {
	        kartu_stok.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();

	    $('.cari').click(function(){
			let tanggal_dari   = $('input[name="tanggal_dari"]').val();
			let tanggal_sampai = $('input[name="tanggal_sampai"]').val();
			let obat_cari      = $('select[name="obat_cari"]').val();

			if (tanggal_dari != '' && tanggal_sampai != '' && obat_cari != '') {
				kartu_stok.draw()
			}
	    });
	});
</script>
@endsection