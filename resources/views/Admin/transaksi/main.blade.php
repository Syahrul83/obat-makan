@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Penjualan</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<div class="col-md-2">
							<input type="text" class="form-control" name="kode_transaksi_cari" placeholder="Cari Kode Penjualan">
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control datepicker" placeholder="dd-mm-yyyy" name="tanggal_transaksi_cari">
						</div>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-transaksi force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Kode Penjualan</th>
								<th>Tanggal Penjualan</th>
								<th>Total</th>
								<th>Bayar</th>
								<th>Kembali</th>
								<th>Input By</th>
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

        var data_transaksi = $('.data-transaksi').DataTable({
            processing:true,
            serverSide:true,
            ajax:{
            	url: "{{ url('datatables/data-transaksi') }}",
            	data:function(d){
					d.kode_transaksi_cari    = $('input[name=kode_transaksi_cari]').val();
					d.tanggal_transaksi_cari = $('input[name=tanggal_transaksi_cari]').val();
            	}
            },
            columns:[
                {data:'id_transaksi',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'kode_transaksi',name:'kode_transaksi'},
                {data:'tanggal_transaksi',name:'tanggal_transaksi'},
                {data:'total',name:'total'},
                {data:'bayar',name:'bayar'},
                {data:'kembali',name:'kembali'},
                {data:'name',name:'name'},
                {data:'action',name:'action',searchable:false,orderable:false}
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
        data_transaksi.on( 'order.dt search.dt', function () {
	        data_transaksi.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();

	    $('input[name=kode_transaksi_cari]').keyup(function(){
		    data_transaksi.draw();
	    })

	    $('input[name=tanggal_transaksi_cari]').change(function(){
	    	data_transaksi.draw();
	    })
	});
</script>
@endsection