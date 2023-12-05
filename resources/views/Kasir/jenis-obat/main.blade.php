@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Bentuk Sediaan Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/kasir/data-jenis-obat/tambah') }}">
							<button class="btn btn-primary">Tambah Data</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-jenis-obat force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Bentuk Sediaan Obat</th>
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
        var	jenis_obat = $('.data-jenis-obat').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-jenis-obat') }}",
            columns:[
                {data:'id_jenis_obat',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_jenis_obat',name:'nama_jenis_obat'},
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
       	jenis_obat.on( 'order.dt search.dt', function () {
	       	jenis_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection