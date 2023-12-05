@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Penjualan Racik Obat</h1>
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
							<input type="text" class="form-control" name="nama_pasien_cari" placeholder="Cari Nama Pasien">
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
						<table class="table table-hover table-bordered data-transaksi-racik-obat">
							<thead>
								<th>No.</th>
								<th>Kode Penjualan</th>
								<th>Tanggal Penjualan</th>
								<th>Nama Pasien</th>
								<th>Nama Dokter</th>
								<th>Diskon</th>
								<th>Diskon (Rupiah)</th>
								<th>Harga Total</th>
								<th>Bayar</th>
								<th>Kembalian</th>
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
	    var data_transaksi_non_upds = $('.data-transaksi-racik-obat').DataTable({
	        processing:true,
	        serverSide:true,
	        ajax:{
	        	url:"{{ url('datatables/data-transaksi-racik-obat/') }}",
	        	data:function(d) {
	        		d.kode_transaksi_cari = $('input[name="kode_transaksi_cari"]').val();
	        		d.nama_pasien_cari = $('input[name="nama_pasien_cari"]').val();
	        		d.tanggal_transaksi_cari = $('input[name="tanggal_transaksi_cari"]').val();
	        	}
	        },
	        columns:[
	            {data:'id_transaksi_racik_obat',searchable:false,render:function(data,type,row,meta){
	                return meta.row + meta.settings._iDisplayStart+1;
	            }},
	            {data:'kode_transaksi',name:'kode_transaksi'},
	            {data:'tanggal_transaksi',name:'tanggal_transaksi'},
	            {data:'nama_pasien',name:'nama_pasien'},
	            {data:'nama_dokter',name:'nama_dokter'},
	            {data:'diskon',name:'diskon'},
	            {data:'diskon_rupiah',name:'diskon_rupiah'},
	            {data:'harga_total',name:'harga_total'},
	            {data:'bayar',name:'bayar'},
	            {data:'kembalian',name:'kembalian'},
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
	    data_transaksi_non_upds.on( 'order.dt search.dt', function () {
	        data_transaksi_non_upds.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            cell.innerHTML = i+1;
	        });
	    }).draw();

	    $('input[name=kode_transaksi_cari]').keyup(function(){
		    data_transaksi_non_upds.draw();
	    })

	    $('input[name=nama_pasien_cari]').keyup(function(){
		    data_transaksi_non_upds.draw();
	    })

	    $('input[name=tanggal_transaksi_cari]').change(function(){
	    	data_transaksi_non_upds.draw();
	    })
	})
</script>
@endsection