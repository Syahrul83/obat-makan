@extends('layout.app-kasir')

@section('content')
@include('Admin.navbar')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Ubah Profile</h1>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<form action="{{url('/admin/ubah-profile/save')}}" method="POST">
						{{csrf_field()}}
						<div class="box-body">
							@if (session()->has('message'))
							<div class="alert alert-success alert-dismissible">
								{{session('message')}} <button class="close" data-dismiss="alert">X</button>
							</div>
							@endif
							<div class="form-group">
								<label for="">Nama</label>
								<input type="text" name="nama" class="form-control" value="{{Auth::user()->name}}" required="required" placeholder="Isi Nama">
							</div>
							<div class="form-group">
								<label for="">Username</label>
								<input type="text" name="username" class="form-control" value="{{Auth::user()->username}}" required="required" disabled="disabled" placeholder="Isi Username">
								<input type="checkbox" id="sip"> Ubah Username
							</div>
							<div class="form-group">
								<label for="">Password</label>
								<input type="password" name="password" class="form-control" placeholder="Isi Password">
							</div>
							<div class="form-group">
								<label for="">Nama Instansi</label>
								<input type="text" class="form-control" name="nama_instansi" value="{{isset($profile_instansi) ? $profile_instansi->nama_instansi:''}}" placeholder="Isi Nama Instansi" required="required">
							</div>
							<div class="form-group">
								<label for="">Alamat Instansi</label>
								<input type="text" class="form-control" name="alamat_instansi" value="{{isset($profile_instansi) ? $profile_instansi->alamat_instansi:''}}" placeholder="Isi Alamat Instansi" required="required">
							</div>
							<div class="form-group">
								<label for="">Nomor Telepon Instansi</label>
								<input type="text" class="form-control" name="nomor_telepon_instansi" value="{{isset($profile_instansi) ? $profile_instansi->nomor_telepon_instansi:''}}" placeholder="Isi Nomor Telepon Instansi" required="required">
							</div>
						</div>
						<div class="box-footer">
							<input type="hidden" name="id_profile_instansi" value="{{isset($profile_instansi) ? $profile_instansi->id_profile_instansi:''}}">
							<button class="btn btn-primary">
								Ubah
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('js')
<script>
	$(function(){
		$('#sip').click(function(){
			if ($(this).is(':checked')) {
				$('input[name="username"]').removeAttr('disabled');
			}
			else {
				$('input[name="username"]').attr('disabled','disabled');
			}
		});
	});
</script>
@endsection