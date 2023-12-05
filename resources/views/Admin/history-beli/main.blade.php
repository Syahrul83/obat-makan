@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Transaksi</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
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
							<button class="btn btn-success cari">Cari</button>
						</div>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover history-beli force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nomor Terima</th>
								<th>Tgl Faktur</th>
								<th>Kreditur</th>
								<th>Jumlah</th>
								<th>Satuan</th>
								<th>Hrg PPn</th>
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

        var history_beli = $('.history-beli').DataTable({
            processing:true,
            serverSide:true,
            ajax:{
            	url: "{{ url('datatables/history-beli') }}",
            	data:function(d){
					// d.kode_transaksi_cari    = $('input[name=kode_transaksi_cari]').val();
					// d.tanggal_transaksi_cari = $('input[name=tanggal_transaksi_cari]').val();
					d.tanggal_dari   = $('input[name="tanggal_dari"]').val();
					d.tanggal_sampai = $('input[name="tanggal_sampai"]').val();
					d.obat_cari      = $('select[name="obat_cari"]').val();
            	}
            },
            columns:[
                {data:'id_pembelian_obat',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'kode_pembelian',name:'kode_pembelian'},
                {data:'tanggal_terima',name:'tanggal_terima'},
                {data:'nama_supplier',name:'nama_supplier'},
                {data:'jumlah',name:'jumlah'},
                {data:'satuan_obat',name:'satuan_obat'},
                {data:'sub_total',name:'sub_total'},
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
        history_beli.on( 'order.dt search.dt', function () {
	        history_beli.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();

	    // $('input[name=tanggal_dari]').change(function(){
		   //  data_transaksi.draw();
	    // })

	    // $('input[name=tanggal_sampai]').change(function(){
	    // 	data_transaksi.draw();
	    // })

	    // $('select[name=obat_cari]').change(function(){
	    // 	data_transaksi.draw();
	    // })

	    $('.cari').click(function(){
			let tanggal_dari   = $('input[name="tanggal_dari"]').val();
			let tanggal_sampai = $('input[name="tanggal_sampai"]').val();
			let obat_cari      = $('select[name="obat_cari"]').val();

			if (tanggal_dari != '' && tanggal_sampai != '' && obat_cari != '') {
				history_beli.draw()
			}
	    });
	});
</script>
@endsection