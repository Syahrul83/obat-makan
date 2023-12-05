@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Data Pembelian Obat</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						{{-- <div class="col-md-12"> --}}
							<a href="{{ url('/kasir/data-pembelian/tambah') }}">
								<button class="btn btn-primary">Tambah Data</button>
							</a>
						{{-- </div> --}}
					</div>
					<div class="box-body">
						@if (session()->has('message'))
						<div class="alert alert-success alert-dismissible">
							{{session('message')}} <button class="close" data-dismiss="alert">X</button>
						</div>
						@endif
						<div class="col-md-12 col-md-offset-1">
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="kode_pembelian_cari" placeholder="Cari Kode Pembelian">
							</div>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="nomor_faktur_cari" placeholder="Cari Nomor Faktur">
							</div>
							{{-- <div class="col-md-2">
								<select name="jenis_beli_cari" class="form-control input-sm select2">
					        		<option value="" selected disabled>-- Pilih Jenis Beli --</option>
									<option value="cash">Cash</option>
									<option value="kredit">Kredit</option>
									<option value="konsinyasi">Konsinyasi</option>
								</select>
							</div> --}}
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" name="supplier_cari" placeholder="Cari Supplier">
							</div>
							<div class="col-md-2">
								<input type="text" name="tanggal_dari_cari" class="form-control input-sm datepicker" placeholder="Tanggal Dari">
							</div>
							<div class="col-md-2">
								<input type="text" name="tanggal_sampai_cari" class="form-control input-sm datepicker" placeholder="Tanggal Sampai">
							</div>
						</div>
						<table class="table table-hover force-fullwidth" id="data-pembelian-obat">
							<thead>
								<th>No.</th>
								<th>Kode Pembelian</th>
								<th>Nomor Faktur</th>
								<th>Supplier</th>
								<th>Tanggal Terima</th>
								<th>Waktu Hutang</th>
								<th>Tanggal Jatuh Tempo</th>
								<th>Jenis Beli</th>
								<th>Total Semua</th>
								<th>Input By</th>
								<th>Tanggal Input</th>
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
        var pembelian_obat = $('#data-pembelian-obat').DataTable({
            processing:true,
            serverSide:true,
            ajax:{
            	url : "{{ url('/datatables/data-pembelian-obat') }}",
            	data:function(d){
					d.kode_pembelian_cari = $('input[name=kode_pembelian_cari]').val();
					d.nomor_faktur_cari   = $('input[name=nomor_faktur_cari]').val();
					d.supplier_cari       = $('input[name=supplier_cari]').val();
					d.tanggal_dari_cari   = $('input[name=tanggal_dari_cari]').val();
					d.tanggal_sampai_cari = $('input[name=tanggal_sampai_cari]').val();
            	}
            },
            columns:[
                {data:'id_pembelian_obat',searchable:false,render:function(data,type,row,meta){
                    return meta.row + meta.settings._iDisplayStart+1;
                }},
                {data:'kode_pembelian',name:'kode_pembelian'},
                {data:'nomor_faktur',name:'nomor_faktur'},
                {data:'nama_supplier',name:'nama_supplier'},
                {data:'tanggal_terima',name:'tanggal_terima'},
                {data:'waktu_hutang',name:'waktu_hutang'},
                {data:'tanggal_jatuh_tempo',name:'tanggal_jatuh_tempo'},
                {data:'jenis_beli',name:'jenis_beli'},
                {data:'total_semua',name:'total_semua'},
                {data:'name',name:'name'},
                {data:'tanggal_input',name:'tanggal_input'},
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
        pembelian_obat.on( 'order.dt search.dt', function () {
	        pembelian_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	        	cell.innerHTML = i+1;
	        });
        }).draw();

	    $('input[name=kode_pembelian_cari]').keyup(function(){
		    pembelian_obat.draw();
	    })

	    $('input[name=nomor_faktur_cari]').keyup(function(){
	    	pembelian_obat.draw();
	    })

	    $('input[name="supplier_cari"]').keyup(function(){
	    	pembelian_obat.draw();
	    })

	    $('input[name="tanggal_dari_cari"]').change(function(){
	    	pembelian_obat.draw();
	    })

	    $('input[name="tanggal_sampai_cari"]').change(function(){
	    	pembelian_obat.draw();
	    })
	});
</script>
@endsection