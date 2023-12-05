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
					<div class="box-header">
						<a href="{{ url('/admin/data-penjualan') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> 
								Kembali
							</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<h4><b>Kode Penjualan : {{ $nomor_transaksi }}</b></h4>
						<table class="table table-hover data-transaksi-detail force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Nama Supplier</th>
								<th>Pcs</th>
								<th>Diskon</th>
								<th>Diskon (Rupiah)</th>
								<th>Sub Total</th>
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
        var users = $('.data-transaksi-detail').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-transaksi',$id) }}",
            columns:[
                {data:'id_transaksi_det',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_obat',name:'nama_obat'},
                {data:'nama_supplier',name:'nama_supplier'},
                {data:'jumlah',name:'jumlah'},
                {data:'diskon',name:'diskon'},
                {data:'diskon_rupiah',name:'diskon_rupiah'},
                {data:'sub_total',name:'sub_total'}
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
        users.on( 'order.dt search.dt', function () {
	        users.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection