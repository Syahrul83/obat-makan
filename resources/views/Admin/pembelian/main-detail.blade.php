@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Pembelian Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/admin/data-pembelian') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-pembelian-obat-detail force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Jumlah</th>
								<th>Satuan</th>
								<th>Harga Obat</th>
								<th>Disc 1</th>
								<th>Disc 2</th>
								<th>Disc 3</th>
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
        var pembelian_obat_detail = $('.data-pembelian-obat-detail').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-pembelian-obat/detail',$id) }}",
            columns:[
                {data:'id_pembelian_detail',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_obat',name:'nama_obat'},
                {data:'jumlah',name:'jumlah'},
                {data:'satuan_obat',name:'satuan_obat'},
                {data:'harga_obat',name:'harga_obat'},
                {data:'disc_1',name:'disc_1'},
                {data:'disc_2',name:'disc_2'},
                {data:'disc_3',name:'disc_3'},
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
        pembelian_obat_detail.on( 'order.dt search.dt', function () {
	        pembelian_obat_detail.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection