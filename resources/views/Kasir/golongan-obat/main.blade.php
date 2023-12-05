@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Golongan Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/kasir/data-golongan-obat/tambah') }}">
							<button class="btn btn-primary">Tambah Obat</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover force-fullwidth data-golongan-obat">
							<thead>
								<th>No.</th>
								<th>Nama Golongan</th>
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
        var golongan_obat = $('.data-golongan-obat').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-golongan-obat') }}",
            columns:[
                {data:'id_golongan_obat',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_golongan',name:'nama_golongan'},
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
        golongan_obat.on( 'order.dt search.dt', function () {
	        golongan_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
</script>
@endsection