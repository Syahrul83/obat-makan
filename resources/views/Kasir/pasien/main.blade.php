@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Pasien</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-pasien force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Pasien</th>
								<th>Nomor Telepon</th>
								<th>Alamat</th>
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
        var pasien = $('.data-pasien').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-pasien') }}",
            columns:[
                {data:'id_pasien',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_pasien',name:'nama_pasien'},
                {data:'nomor_telepon_pasien',name:'nomor_telepon_pasien'},
                {data:'alamat_pasien',name:'alamat_pasien'},
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
        pasien.on( 'order.dt search.dt', function () {
	        pasien.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection