@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Komposisi Obat</h1>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header">
						<a href="{{ url('/kasir/data-obat') }}">
							<button class="btn btn-default"><span class="fa fa-arrow-left"></span> Kembali</button>
						</a>
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<table class="table table-hover data-komposisi-obat-kasir" id-obat="{{$id}}">
							<thead>
								<th>No.</th>
								<th>Nama Obat</th>
								<th>Nama Komposisi</th>
								<th>Takaran Komposisi</th>
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
	$(() => {
	    var id_obat_komposisi__ = $('.data-komposisi-obat-kasir').attr('id-obat');
	    var komposisi_obat__ = $('.data-komposisi-obat-kasir').DataTable({
	        processing:true,
	        serverSide:true,
	        ajax:base_url + `/datatables/data-obat/komposisi-obat/${id_obat_komposisi__}`,
	        columns:[
	            {data:'id_komposisi_obat',searchable:false,render:function(data,type,row,meta){
	                return meta.row + meta.settings._iDisplayStart+1;
	            }},
	            {data:'nama_obat',name:'nama_obat'},
	            {data:'nama_komposisi',name:'nama_komposisi'},
	            {data:'takaran_komposisi',name:'takaran_komposisi'}
	        ],
	        scrollCollapse: true,
	        columnDefs: [ {
	        sortable: true,
	        "class": "index",
	        }],
	        order: [[ 0, 'desc' ]],
	        responsive:true,
	        fixedColumns: true
	    });
	    komposisi_obat__.on( 'order.dt search.dt', function () {
	        komposisi_obat__.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            cell.innerHTML = i+1;
	        });
	    }).draw();
	})
</script>
@endsection