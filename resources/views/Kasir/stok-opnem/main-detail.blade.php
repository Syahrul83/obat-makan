@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Stok Opnem Detail</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<a href="{{ url('/kasir/stok-opnem') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<div class="box-body">
						<table class="table table-hover table-bordered stok-opnem-detail force-fullwidth">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Satuan</th>
								<th>Hna</th>
								<th>Stok Komputer</th>
								<th>Stok Fisik</th>
								<th>Stok Selisih</th>
								<th>Nilai</th>
								<th>Tanggal Exp</th>
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
        var	stok_opnem_detail = $('.stok-opnem-detail').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ url('/datatables/data-stok-opnem/detail/'.$id) }}",
            columns:[
                {data:'id_stok_opnem_detail',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'nama_obat',name:'nama_obat'},
                {data:'satuan_obat',name:'satuan_obat'},
                {data:'harga_modal',name:'harga_modal'},
                {data:'stok_komputer',name:'stok_komputer'},
                {data:'stok_fisik',name:'stok_fisik'},
                {data:'stok_selisih',name:'stok_selisih'},
                {data:'sub_nilai',name:'sub_nilai'},
                {data:'tanggal_expired',name:'tanggal_expired'}
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
       	stok_opnem_detail.on( 'order.dt search.dt', function () {
	       	stok_opnem_detail.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();
	});
</script>
@endsection