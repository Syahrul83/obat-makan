@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Debitur</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
				<div class="box-header">
						<a href="{{ url('/admin/data-debitur/tambah') }}">
							<button class="btn btn-primary">Tambah Data</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-debitur force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Debitur</th>
								<th>Margin</th>
								<th>Bilangan</th>
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
        var debitur = $('.data-debitur').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-debitur') }}",
            columns:[
                {data:'id',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'name',name:'name'},
                {data:'margin',name:'margin'},
                {data:'bilangan',name:'bilangan'},
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
        debitur.on( 'order.dt search.dt', function () {
	        debitur.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection