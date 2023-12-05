@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Stok Opnem</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{url('/admin/stok-opnem/tambah')}}" target="_blank">
							<button class="btn btn-primary">Tambah Data</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover table-bordered stok-opnem force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Tanggal Stok Opnem</th>
								<th>Total Nilai</th>
								<th>Keterangan</th>
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
        var	stok_opnem = $('.stok-opnem').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-stok-opnem') }}",
            columns:[
                {data:'id_stok_opnem',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'tanggal_stok_opnem',name:'tanggal_stok_opnem'},
                {data:'total_nilai',name:'total_nilai'},
                {data:'keterangan',name:'keterangan'},
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
       	stok_opnem.on( 'order.dt search.dt', function () {
	       	stok_opnem.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection