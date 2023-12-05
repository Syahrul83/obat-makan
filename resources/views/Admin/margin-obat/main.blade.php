@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Margin Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						@if ($check == 0)
							<a href="{{ url('/admin/margin-obat/tambah') }}">
								<button class="btn btn-primary">Tambah Data</button>
							</a>
						@endif
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover margin-obat force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Margin UPDS</th>
								<th>Margin Resep</th>
								<th>Margin Relasi</th>
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
        var	margin_obat = $('.margin-obat').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-margin-obat') }}",
            columns:[
                {data:'id_margin_obat',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'margin_upds',name:'margin_upds'},
                {data:'margin_resep',name:'margin_resep'},
                {data:'margin_relasi',name:'margin_relasi'},
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
       	margin_obat.on( 'order.dt search.dt', function () {
	       	margin_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection