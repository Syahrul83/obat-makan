@extends('layout.app-kasir')

@section('content')
@include('Kasir.navbar')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Form Obat</h1>
    </section>

    <section class="content">
        <div class="row">
            @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible">
                {{session('message')}} <button class="close" data-dismiss="alert">X</button>
            </div>
            @elseif (session()->has('log'))
            <div class="alert alert-danger alert-dismissible">
                {{session('log')}} <button class="close" data-dismiss="alert">X</button>
            </div>
            @endif
            <form action="{{url('/kasir/data-obat/save')}}" method="POST">
                @csrf
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-header">
                            <a href="{{ url('/kasir/data-obat') }}">
                                <button class="btn btn-default" type="button"><span class="fa fa-arrow-left"></span> Kembali</button>
                            </a>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Nama Obat</label>
                                <input type="text" name="nama_obat" class="form-control" placeholder="Isi Nama Obat" value="{{isset($row)?$row->nama_obat:''}}" required="required" {!!isset($row)?'autofocus="autofocus"':''!!}>
                            </div>
                            <div class="form-group">
                                <label for="">Dosis Satuan</label>
                                <input type="text" name="dosis_satuan" class="form-control" placeholder="Isi Dosis Satuan" value="{{ isset($row) ? $row->dosis_satuan : '' }}">
                            </div>
                            <div class="form-group">
                                <label for="">Pabrik Obat</label>
                                <select name="pabrik_obat" class="form-control select2" required="required">
                                    <option value="" selected="selected" disabled="disabled">=== Pilih Pabrik Obat ===</option>
                                    @foreach ($pabrik_obat as $element)
                                    <option value="{{$element->id_pabrik_obat}}" @if(isset($row)){!!$row->id_pabrik_obat == $element->id_pabrik_obat ? 'selected="selected"':''!!}@endif>{{$element->nama_pabrik}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Supplier Obat</label>
                                <select name="supplier_obat[]" class="form-control select2" required="" multiple="">
                                    @foreach ($supplier_obat as $element)
                                    <option value="{{$element->id_supplier}}" @if(isset($obat_detail)){!!$obat_detail->cekSupplier($id,$element->id_supplier) == 'true' ? 'selected="selected"':''!!}@endif>{{$element->nama_supplier}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Bentuk Sediaan Obat</label>
                                <select name="jenis_obat" class="form-control select2" required="required">
                                    <option value="" selected="selected" disabled="disabled">=== Pilih Bentuk Sediaan Obat ===</option>
                                    @foreach ($jenis_obat as $element)
                                    <option value="{{$element->id_jenis_obat}}" @if(isset($row)){!!$row->id_jenis_obat == $element->id_jenis_obat ? 'selected="selected"':''!!}@endif>{{$element->nama_jenis_obat}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Golongan Obat</label>
                                <select name="golongan_obat" class="form-control select2" required="required">
                                    <option value="" selected="selected" disabled="disabled">=== Pilih Golongan Obat ===</option>
                                    @foreach ($golongan_obat as $element)
                                    <option value="{{$element->id_golongan_obat}}" @if(isset($row)){!!$row->id_golongan_obat == $element->id_golongan_obat ? 'selected="selected"':''!!}@endif>{{$element->nama_golongan}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Tanggal Expired</label>
                                <input type="text" name="tanggal_expired" class="form-control datepicker" value="{{isset($row)?reverse_date($row->tanggal_expired):''}}" placeholder="dd-mm-yyyy" required="required">
                            </div>
                            <div class="form-group">
                                <label for="">Hna</label>
                                <input type="text" name="harga_modal" class="form-control" placeholder="Isi Hna" value="{{isset($row)?$row->harga_modal:0}}" required="required">
                                <label for="" id="hna-label">{{isset($row) ? format_rupiah($row->harga_modal) : 'Rp. 0,00'}}</label>
                            </div>
                            <input type="hidden" name="nilai_ppn" value="{{ $nilai_ppn->ppn }}">
                            <div class="form-group">
                                <label for="">Hna+PPn</label>
                                <input type="text" name="harga_modal_ppn" class="form-control harga-modal-ppn" value="{{isset($row)?$row->harga_modal_ppn:0}}">
                                <label for="" id="hna-ppn">{{isset($row) ? format_rupiah($row->harga_modal_ppn) : 'Rp. 0,00'}}</label>
                            </div>
                            <label for="">Margin UPDS</label>
                            <div class="input-group" style="margin-bottom:10px;">
                                <input type="number" name="margin_upds" class="form-control" value="{{$margin_obat->margin_upds}}" readonly="readonly">
                                <span class="input-group-addon">%</span>
                            </div>
                            <div class="form-group">
                                <label for="">Hja UPDS</label>
                                <input type="text" name="hja_upds" class="form-control hja-upds" value="{{isset($row)?$row->hja_upds:0}}" @if (isset($row)) {!!$row->kunci_hja_upds != 0 ? 'readonly="readonly"' : ''!!} @endif>
                                <input type="checkbox" class="kunci-hja-upds" name="kunci_hja_upds" @if (isset($row)) {!!$row->kunci_hja_upds != 0 ? 'value="1" checked="checked"' : 'value="0"'!!} @else value="0" @endif> Kunci Hja UPDS
                                <br>
                                <label for="" id="hja-upds-label">{{isset($row) ? format_rupiah($row->hja_upds) : 'Rp. 0,00'}}</label>
                            </div>
                            <label for="">Margin Resep</label>
                            <div class="input-group" style="margin-bottom:10px;">
                                <input type="number" name="margin_resep" class="form-control" value="{{$margin_obat->margin_resep}}" readonly="readonly">
                                <span class="input-group-addon">%</span>
                            </div>
                            <div class="form-group">
                                <label for="">Hja Resep</label>
                                <input type="text" name="hja_resep" class="form-control hja-resep" value="{{isset($row)?$row->hja_resep:0}}" @if (isset($row)) {!!$row->kunci_hja_resep != 0 ? 'readonly="readonly"' : ''!!} @endif>
                                <input type="checkbox" class="kunci-hja-resep" name="kunci_hja_resep" @if (isset($row)) {!!$row->kunci_hja_resep != 0 ? 'value="1" checked="checked"' : 'value="0"'!!} @else value="0" @endif> Kunci Hja Resep
                                <br>
                                <label for="" id="hja-resep-label">{{isset($row) ? format_rupiah($row->hja_resep) : 'Rp. 0,00'}}</label>
                            </div>
                            <label for="">Margin Relasi</label>
                            <div class="input-group" style="margin-bottom:10px;">
                                <input type="text" name="margin_relasi" class="form-control" value="{{$margin_obat->margin_relasi}}" readonly="readonly">
                                <span class="input-group-addon">%</span>
                            </div>
                            <div class="form-group">
                                <label for="">Hja Relasi</label>
                                <input type="text" name="hja_relasi" class="form-control hja-relasi" value="{{isset($row)?$row->hja_relasi:0}}" @if (isset($row)) {!!$row->kunci_hja_relasi != 0 ? 'readonly="readonly"' : ''!!} @endif>
                                <input type="checkbox" class="kunci-hja-relasi" name="kunci_hja_relasi" @if (isset($row)) {!!$row->kunci_hja_relasi != 0 ? 'value="1" checked="checked"' : 'value="0"'!!} @else value="0" @endif> Kunci Hja Relasi
                                <br>
                                <label for="" id="hja-relasi-label">{{isset($row) ? format_rupiah($row->hja_relasi) : 'Rp. 0,00'}}</label>
                            </div>
                            <div class="form-group">
                                <label for="">Stok Obat</label>
                                <input type="number" name="stok_obat" class="form-control" placeholder="Isi Stok Obat" value="{{isset($row)?$row->stok_obat:''}}" required="required">
                            </div>
                            <div class="form-group">
                                <label for="">Satuan Obat</label>
                                <input type="text" name="satuan_obat" class="form-control" placeholder="Isi Satuan Obat" value="{{isset($row) ? $row->satuan_obat : ''}}">
                            </div>
                        </div>
                        <div class="box-footer">
                            <input type="hidden" name="id" value="{{isset($row)?$row->id_obat:''}}">
                            <button class="btn btn-primary">
                                Simpan <span class="fa fa-save"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-body" id="komposisi-obat-layout">
                            @if (isset($komposisi_obat) && count($komposisi_obat) > 0)
                            @foreach ($komposisi_obat as $element)
                            <div class="takaran-komposisi-input" id="takaran-komposisi-input">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Komposisi Obat</label>
                                        <input type="text" class="form-control" name="komposisi_obat[]" placeholder="Isi Komposisi Obat" value="{{ $element->nama_komposisi }}" required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Takaran Komposisi</label>
                                        <input type="text" class="form-control" name="takaran_komposisi[]" placeholder="Isi Takaran Komposisi" value="{{ $element->takaran_komposisi }}" required="required">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="takaran-komposisi-input" id="takaran-komposisi-input">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Komposisi Obat</label>
                                        <input type="text" class="form-control" name="komposisi_obat[]" placeholder="Isi Komposisi Obat" required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Takaran Komposisi</label>
                                        <input type="text" class="form-control" name="takaran_komposisi[]" placeholder="Isi Takaran Komposisi" required="required">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-success" id="tambah-komposisi" type="button">Tambah Komposisi</button>
                            @if (isset($komposisi_obat) && count($komposisi_obat) > 0)
                            <button class="btn btn-danger" id="hapus-komposisi" type="button">Hapus Komposisi</button>
                            @else
                            <button class="btn btn-danger btn-hide" id="hapus-komposisi" type="button">Hapus Komposisi</button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    $(() => {
        $('select[name="supplier_obat[]"]').select2({
            placeholder:"=== Pilih Supplier ==="
        })

        $('input[name="harga_modal"]').keyup(function() {
            let val           = $(this).val()
            let nilai_ppn     = $('input[name="nilai_ppn"]').val()
            console.log(nilai_ppn)
            let margin_upds   = $('input[name="margin_upds"]').val()
            let margin_resep  = $('input[name="margin_resep"]').val()
            let margin_relasi = $('input[name="margin_relasi"]').val()
            $('#hna-label').html(rupiah_format(val))

            if (val != '') {
                let harga_modal_ppn = parseFloat(val) + parseFloat(((val * nilai_ppn) / 100))
                let hja_upds        = parseFloat(harga_modal_ppn) + ((parseFloat(harga_modal_ppn) * parseFloat(margin_upds))/100)
                let hja_resep       = parseFloat(harga_modal_ppn) + ((parseFloat(harga_modal_ppn) * parseFloat(margin_resep))/100)
                let hja_relasi      = parseFloat(harga_modal_ppn) + ((parseFloat(harga_modal_ppn) * parseFloat(margin_relasi))/100)

                $('.harga-modal-ppn').val(harga_modal_ppn)
                $('#hna-ppn').html(rupiah_format(harga_modal_ppn))
                // $('.hja-upds').val(hja_upds)
                // $('#hja-upds-label').html(rupiah_format(hja_upds))
                // $('.hja-resep').val(hja_resep)
                // $('#hja-resep-label').html(rupiah_format(hja_resep))
                // $('.hja-relasi').val(hja_relasi)
                // $('#hja-relasi-label').html(rupiah_format(hja_relasi))

                if ($('.hja-upds').attr('readonly') != 'readonly') {
                    $('.hja-upds').val(hja_upds)
                    $('#hja-upds-label').html(rupiah_format(hja_upds))
                }

                if ($('.hja-resep').attr('readonly') != 'readonly') {
                    $('.hja-resep').val(hja_resep)
                    $('#hja-resep-label').html(rupiah_format(hja_resep))
                }

                if ($('.hja-relasi').attr('readonly') != 'readonly') {
                    $('.hja-relasi').val(hja_relasi)
                    $('#hja-relasi-label').html(rupiah_format(hja_relasi))
                }
            }

            // if (val != '' && keuntungan != '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let harga_jual_obat = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(keuntungan))/100)
            //  let round_up_harga_jual = Math.ceil(harga_jual_obat / 1000) * 1000

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.harga-jual-obat').val(round_up_harga_jual)
            // }
            // else if(val != '' && margin_upds == '' && margin_resep != '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let hja_resep = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(margin_resep))/100)

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.hja-resep').val(hja_resep)
            // }
            // else if(val != '' && margin_upds != '' && margin_resep == '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let hja_upds = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(margin_upds))/100)

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.hja-upds').val(hja_upds)    
            // }
        })

        $('input[name="harga_modal_ppn"]').keyup(function() {
            let val           = $(this).val()
            let margin_upds   = $('input[name="margin_upds"]').val()
            let margin_resep  = $('input[name="margin_resep"]').val()
            let margin_relasi = $('input[name="margin_relasi"]').val()

            if (val != '') {
                let hna        = (100/110) * parseFloat(val)
                let hja_upds   = parseFloat(val) + ((parseFloat(val) * parseFloat(margin_upds))/100)
                let hja_resep  = parseFloat(val) + ((parseFloat(val) * parseFloat(margin_resep))/100)
                let hja_relasi = parseFloat(val) + ((parseFloat(val) * parseFloat(margin_relasi))/100)

                $('input[name="harga_modal"]').val(hna)
                $('#hna-label').html(rupiah_format(hna))
                $('.harga-modal-ppn').val(val)
                $('#hna-ppn').html(rupiah_format(val))
                if ($('.hja-upds').attr('readonly') != 'readonly') {
                    $('.hja-upds').val(hja_upds)
                    $('#hja-upds-label').html(rupiah_format(hja_upds))
                }

                if ($('.hja-resep').attr('readonly') != 'readonly') {
                    $('.hja-resep').val(hja_resep)
                    $('#hja-resep-label').html(rupiah_format(hja_resep))
                }

                if ($('.hja-relasi').attr('readonly') != 'readonly') {
                    $('.hja-relasi').val(hja_relasi)
                    $('#hja-relasi-label').html(rupiah_format(hja_relasi))
                }
            }

            // if (val != '' && keuntungan != '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let harga_jual_obat = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(keuntungan))/100)
            //  let round_up_harga_jual = Math.ceil(harga_jual_obat / 1000) * 1000

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.harga-jual-obat').val(round_up_harga_jual)
            // }
            // else if(val != '' && margin_upds == '' && margin_resep != '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let hja_resep = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(margin_resep))/100)

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.hja-resep').val(hja_resep)
            // }
            // else if(val != '' && margin_upds != '' && margin_resep == '') {
            //  let harga_modal_ppn = parseInt(val) + parseInt(((val * 10) / 100))
            //  let hja_upds = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(margin_upds))/100)

            //  $('.harga-modal-ppn').val(harga_modal_ppn)
            //  $('.hja-upds').val(hja_upds)    
            // }
        })

        // $('input[name="margin_upds"]').keyup(function() {
        //  let val             = $(this).val()
        //  let harga_modal     = $('input[name="harga_modal"]').val()
        //  let harga_modal_ppn = $('.harga-modal-ppn').val()

        //  if (val != '' && harga_modal != '') {
        //      let harga_modal_ppn = parseInt(harga_modal) + parseInt(((harga_modal * 10) / 100))
        //      let hja_upds        = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(val))/100)

        //      $('.hja-upds').val(hja_upds)
        //      $('#hja-upds-label').html(rupiah_format(hja_upds))
        //  }
        // })

        // $('input[name="margin_resep"]').keyup(function() {
        //  let val             = $(this).val()
        //  let harga_modal     = $('input[name="harga_modal"]').val()
        //  let harga_modal_ppn = $('.harga-modal-ppn').val()

        //  if (val != '' && harga_modal != '') {
        //      let harga_modal_ppn = parseInt(harga_modal) + parseInt(((harga_modal * 10) / 100))
        //      let hja_resep        = parseInt(harga_modal_ppn) + ((parseInt(harga_modal_ppn) * parseInt(val))/100)

        //      $('.hja-resep').val(hja_resep)
        //      $('#hja-resep-label').html(rupiah_format(hja_resep))
        //  }
        // })

        $('.hja-resep').keyup(function() {
            let val = $(this).val()
            console.log(val)
            $('#hja-resep-label').html(rupiah_format(val))
        })

        $('.hja-upds').keyup(function() {
            let val = $(this).val()
            $('#hja-upds-label').html(rupiah_format(val))
        })

        $('.hja-relasi').keyup(function() {
            let val = $(this).val()
            $('#hja-relasi-label').html(rupiah_format(val))
        })

        $('.kunci-hja-upds').click(function(){
            $(this).val() != 1 ? $('.hja-upds').attr('readonly','readonly') : $('.hja-upds').removeAttr('readonly')
            $(this).val($(this).val() != 1 ? 1 : 0)
        })

        $('.kunci-hja-resep').click(function(){
            $(this).val() != 1 ? $('.hja-resep').attr('readonly','readonly') : $('.hja-resep').removeAttr('readonly')
            $(this).val($(this).val() != 1 ? 1 : 0)
        })

        $('.kunci-hja-relasi').click(function(){
            $(this).val() != 1 ? $('.hja-relasi').attr('readonly','readonly') : $('.hja-relasi').removeAttr('readonly')
            $(this).val($(this).val() != 1 ? 1 : 0)
        })
    })
</script>
@endsection