@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Jam Shift</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/admin/jam-shift/tambah') }}">
							<button class="btn btn-primary">Tambah Data</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover jam-shift force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Dari Jam</th>
								<th>Sampai Jam</th>
								<th>Ket Shift</th>
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
        var	jam_shift = $('.jam-shift').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-jam-shift') }}",
            columns:[
                {data:'id_jam_shift',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'jam_awal',name:'jam_awal'},
                {data:'jam_akhir',name:'jam_akhir'},
                {data:'ket_shift',name:'ket_shift'},
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
       	jam_shift.on( 'order.dt search.dt', function () {
	       	jam_shift.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection