// console.log(window.location.href.split('/'));
var sub_total        = 0;
var total_diskon     = 0;
var total            = 0;
var kalkulasi_diskon = 0;
var kalkulasi_semua  = 0;
var id_kredit        = '';
var uri_kredit       = window.location.href.split('/')
//console.log(uri_kredit)

var total_harga_  = 0;
var total_diskon_ = 0;
var total_semua_  = 0;
// var base_url     = 'http://localhost:8000';
// var base_url    = 'https://apotekbunda.vickypriyadi.net';
// var base_url = 'http://192.168.1.21/apotek/public';
var base_url = window.location.protocol + '//' + window.location.host + '/';
// var base_url = 'http://project_work.web';

function rupiah_format(number){
    // if (Math.sign(string) !== -1) {
    //     reverse = string.toString().split('').reverse().join(''),
    //     ribuan  = reverse.match(/\d{1,3}/g);
    //     ribuan  = 'Rp. '+ribuan.join('.').split('').reverse().join('')+',00';
    //     return ribuan;
    // }
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
}

function round_up_thousand(num,round) {
     return Math.ceil(num / round) * round
}

function convert_date(string) {
    bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September' , 'Oktober', 'November', 'Desember'];
 
    tanggal = string.split("-")[0];
    bulan   = string.split("-")[1];
    tahun   = string.split("-")[2];
 
    return tanggal + " " + bulanIndo[Math.abs(bulan)] + " " + tahun;
}

function reverse_date(string) {
    tanggal = string.split("-")[0];
    bulan   = string.split("-")[1];
    tahun   = string.split("-")[2];

    return tahun+'-'+bulan+'-'+tanggal
}

function ajaxProses(val,pcs,diskon,btn_attr,attr_diskon) {
    $.ajax({
        url: base_url + `/ajax/get-obat-transaksi/${val}/${pcs}/${diskon}/${btn_attr}/${attr_diskon}`
    })
    .done(function(done) {
        var hapus        = $(`button[data-id=${done.uuid}]`).length,
            input_hidden = $(`div[target-id=${done.uuid}]`).length;
        if (hapus == 1 && input_hidden == 1) {
            alert('Obat Sudah Masuk Pada Tabel');
        }
        else {
            if (done.log) {
                total_harga_+=parseInt(done.kalkulasi);
                total_diskon_+=parseInt(done.diskon);
                total_semua_+=parseInt(done.harga);

                $('.total > b').html(`Total Bayar : ${rupiah_format(total_harga_)}`);
                $('.total-diskon > b').html(`Total Diskon : ${rupiah_format(total_diskon_)}`)
                $('.total-semua > b').html(`Total Semua : ${rupiah_format(total_semua_)}`)
                $('input[name="total_harga"]').val(total_semua_);
                $('.transaksi-obat > tbody').append(done.data_table);
                $('#input-hidden > form').append(done.input_hidden);
                $('td.number-kasir').each((i,v) => {
                    $(v).text(i+1)
                })
            }
            else {
                alert(done.message);
            }
        }
    })
    .fail(function() {
        console.log("error");
    });
}

if ($('.checkbox-obat').attr('id') == 'kode-obat') {
    $('#input-pilih-obat').removeClass('open');
    $('#input-pilih-obat').slideUp();
    $('#input-kode-obat').addClass('open');
    $('#input-kode-obat').slideDown(function(){
        $('.checkbox-obat').siblings('input[name="kode_obat"]').focus();
    });
}
else if($('.checkbox-obat').attr('id') == 'pilih-obat') {
    // $('#jenis_obat').focus();
    $('#input-kode-obat').removeClass('open');
    $('#input-kode-obat').slideUp();
    $('#input-pilih-obat').addClass('open');
    $('#input-pilih-obat').slideDown(function(){
        $('.checkbox-obat').siblings('select[name="supplier_obat"]').focus();
    });
}

$(function(){

    $('.datepicker').datepicker({
        format:'dd-mm-yyyy',
        autoclose: true,
        todayBtn: "linked",
        todayHighlight:true
        // showButtonPanel:true,
    })

    $(document).on('keydown','body',function(e){
        if (e.keyCode == 27 && $('.modal').hasClass('in')) {
            $('.modal').modal('hide');
        }
    });

    // DATATABLE //
    var supplier = $('.data-supplier-obat').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-supplier-obat',
        columns:[
            {data:'id_supplier',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_supplier',name:'nama_supplier'},
            {data:'singkatan_supplier',name:'singkatan_supplier'},
            {data:'nomor_telepon',name:'nomor_telepon'},
            {data:'alamat_supplier',name:'alamat_supplier'},
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
    supplier.on( 'order.dt search.dt', function () {
        supplier.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var pabrik_obat = $('.data-pabrik-obat').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-pabrik-obat',
        columns:[
            {data:'id_pabrik_obat',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pabrik',name:'nama_pabrik'},
            {data:'nomor_telepon_pabrik',name:'nomor_telepon_pabrik'},
            {data:'alamat_pabrik',name:'alamat_pabrik'},
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
    pabrik_obat.on( 'order.dt search.dt', function () {
        pabrik_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    $('#data-obat-modal').one("preInit.dt", function () {
        $("#data-obat-modal_filter").append(`<input type="text" name="cari_komposisi_obat" class="form-control input-sm" placeholder="Cari Komposisi Obat">`);
    });
    var obat = $('.data-obat').DataTable({
        processing:true,
        serverSide:true,
        ajax:{
            url:base_url+'/datatables/data-obat',
            data:function(d){
                d.cari_komposisi_obat    = $('input[name=cari_komposisi_obat]').val();
            }
        },
        columns:[
            {data:'id_obat',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_obat',name:'nama_obat'},
            {data:'nama_jenis_obat',name:'nama_jenis_obat'},
            {data:'tanggal_expired',name:'tanggal_expired'},
            {data:'stok_obat',name:'stok_obat'},
            {data:'satuan_obat',name:'satuan_obat'},
            {data:'hja_upds',name:'hja_upds'}
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
    obat.on( 'order.dt search.dt', function () {
        obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();
    $('input[name="cari_komposisi_obat"]').keyup(() => {
        obat.draw()
    })

    var obat_panel = $('.data-obat-panel').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+`/datatables/data-obat`,
        columns:[
            {data:'id_obat',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pabrik',name:'nama_pabrik'},
            {data:'nama_obat',name:'nama_obat'},
            {data:'nama_golongan',name:'nama_golongan'},
            {data:'nama_jenis_obat',name:'nama_jenis_obat'},
            {data:'tanggal_expired',name:'tanggal_expired'},
            {data:'stok_obat',name:'stok_obat'},
            {data:'satuan_obat',name:'satuan_obat'},
            {data:'dosis_satuan',name:'dosis_satuan'},
            {data:'harga_modal',name:'harga_modal'},
            {data:'harga_modal_ppn',name:'harga_modal_ppn'},
            {data:'hja_upds',name:'hja_upds'},
            {data:'hja_resep',name:'hja_resep'},
            {data:'hja_relasi',name:'hja_relasi'},
            {data:'action',name:'action',orderable:false,searchable:false}
        ],
        scrollCollapse: true,
        columnDefs: [ {
        sortable: true,
        "class": "index",
        }],
        order: [[ 0, 'desc' ]],
        scrollX:true,
        responsive:true,
        fixedColumns: true
    });
    obat_panel.on( 'order.dt search.dt', function () {
        obat_panel.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();


    var id_obat_detail = $('.data-obat-detail').attr('id-obat');
    var obat_detail = $('.data-obat-detail').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url + `/datatables/data-obat/detail/${id_obat_detail}`,
        columns:[
            {data:'id_obat_detail',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_supplier',name:'nama_supplier'},
            {data:'action',name:'action',orderable:false,searchable:false}
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
    obat_detail.on( 'order.dt search.dt', function () {
        obat_detail.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var id_obat_komposisi = $('.data-komposisi-obat').attr('id-obat');
    var komposisi_obat_ = $('.data-komposisi-obat').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url + `/datatables/data-obat/komposisi-obat/${id_obat_komposisi}`,
        columns:[
            {data:'id_komposisi_obat',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_obat',name:'nama_obat'},
            {data:'nama_komposisi',name:'nama_komposisi'},
            {data:'takaran_komposisi',name:'takaran_komposisi'},
            {data:'action',name:'action',orderable:false,searchable:false},
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
    komposisi_obat_.on( 'order.dt search.dt', function () {
        komposisi_obat_.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var id_racik_data = $('.data-racik-obat').attr('id-racik-data')
    var data_racik_obat = $('.data-racik-obat').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url + `/datatables/data-racik-obat/${id_racik_data}`,
        columns:[
            {data:'id_racik_obat',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_racik',name:'nama_racik'},
            {data:'jenis_racik',name:'jenis_racik'},
            {data:'jumlah_racik',name:'jumlah_racik'},
            {data:'ongkos_racik',name:'ongkos_racik'},
            {data:'harga_total_racik',name:'harga_total_racik'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    data_racik_obat.on( 'order.dt search.dt', function () {
        data_racik_obat.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var id_racik_detail = $('.data-racik-obat-detail').attr('id-racik-data-detail')
    var data_racik_obat_detail = $('.data-racik-obat-detail').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url + `/datatables/data-racik-obat-detail/${id_racik_detail}`,
        columns:[
            {data:'id_racik_obat_detail',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_obat',name:'nama_obat'},
            {data:'nama_jenis_obat',name:'nama_jenis_obat'},
            {data:'jumlah',name:'jumlah'},
            {data:'embalase',name:'embalase'},
            {data:'sub_total',name:'sub_total'}
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
    data_racik_obat_detail.on( 'order.dt search.dt', function () {
        data_racik_obat_detail.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit = $('.data-kredit').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit',
        columns:[
            {data:'id_kredit',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pelanggan',name:'nama_pelanggan'},
            {data:'nomor_telepon',name:'nomor_telepon'},
            {data:'alamat_pelanggan',name:'alamat_pelanggan'},
            {data:'status_kredit',name:'status_kredit'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    kredit.on( 'order.dt search.dt', function () {
        kredit.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit_panel = $('.data-kredit-panel').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit-panel',
        columns:[
            {data:'id_kredit',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pelanggan',name:'nama_pelanggan'},
            {data:'nomor_telepon',name:'nomor_telepon'},
            {data:'alamat_pelanggan',name:'alamat_pelanggan'},
            {data:'status_kredit',name:'status_kredit'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    kredit_panel.on( 'order.dt search.dt', function () {
        kredit_panel.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit_faktur_panel = $('.data-kredit-faktur-panel').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit-faktur-panel/'+uri_kredit[8],
        columns:[
            {data:'id_kredit_faktur',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nomor_faktur',name:'nomor_faktur'},
            {data:'tanggal_faktur',name:'tanggal_faktur'},
            {data:'status_kredit',name:'status_kredit'},
            {data:'name',name:'name'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    kredit_faktur_panel.on( 'order.dt search.dt', function () {
        kredit_faktur_panel.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit_detail_panel = $('.data-kredit-detail-panel').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit-detail-panel/'+uri_kredit[10],
        columns:[
            {data:'id_kredit_det',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pelanggan',name:'nama_pelanggan'},
            {data:'tanggal_jatuh_tempo',name:'tanggal_jatuh_tempo'},
            {data:'nama_obat',name:'nama_obat'},
            {data:'nama_supplier',name:'nama_supplier'},
            {data:'banyak_obat',name:'banyak_obat'},
            {data:'diskon',name:'diskon'},
            {data:'diskon_rupiah',name:'diskon_rupiah'},
            {data:'sub_total',name:'sub_total'},
            {data:'status_kredit',name:'status_kredit'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    kredit_detail_panel.on( 'order.dt search.dt', function () {
        kredit_detail_panel.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit_panel_kasir = $('.data-kredit-panel-kasir').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit-panel',
        columns:[
            {data:'id_kredit',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pelanggan',name:'nama_pelanggan'},
            {data:'nomor_telepon',name:'nomor_telepon'},
            {data:'alamat_pelanggan',name:'alamat_pelanggan'},
            {data:'status_kredit',name:'status_kredit'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    kredit_panel_kasir.on( 'order.dt search.dt', function () {
        kredit_panel_kasir.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var kredit_detail_panel_kasir = $('.data-kredit-detail-panel-kasir').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-kredit-detail-panel/'+uri_kredit[8],
        columns:[
            {data:'id_kredit_det',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_pelanggan',name:'nama_pelanggan'},
            {data:'tanggal_jatuh_tempo',name:'tanggal_jatuh_tempo'},
            {data:'nama_obat',name:'nama_obat'},
            {data:'banyak_obat',name:'banyak_obat'},
            {data:'diskon',name:'diskon'},
            {data:'diskon_rupiah',name:'diskon_rupiah'},
            {data:'sub_total',name:'sub_total'},
            {data:'status_kredit',name:'status_kredit'}
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
    kredit_detail_panel_kasir.on( 'order.dt search.dt', function () {
        kredit_detail_panel_kasir.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var retur_barang = $('.data-retur-barang').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-retur-barang',
        columns:[
            {data:'id_retur_barang',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nomor_retur',name:'nomor_retur'},
            {data:'nomor_transaksi',name:'nomor_transaksi'},
            {data:'tanggal_retur',name:'tanggal_retur'},
            {data:'total_nominal_retur',name:'total_nominal_retur'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    retur_barang.on( 'order.dt search.dt', function () {
        retur_barang.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    let id_retur_barang = $('.data-retur-barang-detail').attr('id-retur-barang')
    var retur_barang_detail = $('.data-retur-barang-detail').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-retur-barang/detail/'+id_retur_barang,
        columns:[
            {data:'id_retur_barang_detail',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'nama_obat',name:'nama_obat'},
            {data:'stok_transaksi',name:'stok_transaksi'},
            {data:'stok_retur',name:'stok_retur'},
            {data:'nominal_retur',name:'nominal_retur'}
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
    retur_barang_detail.on( 'order.dt search.dt', function () {
        retur_barang_detail.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();

    var data_ppn = $('.data-ppn').DataTable({
        processing:true,
        serverSide:true,
        ajax:base_url+'/datatables/data-ppn',
        columns:[
            {data:'id_persen_ppn',searchable:false,render:function(data,type,row,meta){
                return meta.row + meta.settings._iDisplayStart+1;
            }},
            {data:'ppn',name:'ppn'},
            {data:'action',name:'action',searchable:false,orderable:false}
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
    data_ppn.on( 'order.dt search.dt', function () {
        data_ppn.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    }).draw();
    // ========= //

    // KOMPOSISI DOM //
    $('#tambah-komposisi').click(() => {
        $('#hapus-komposisi').removeClass('btn-hide');
        $('#takaran-komposisi-input').clone().appendTo('#komposisi-obat-layout').find('input').val('');
    })

    $('#hapus-komposisi').click(function() {
        $('#komposisi-obat-layout #takaran-komposisi-input').last().remove()
        if ($('.takaran-komposisi-input').length == 1) {
            $(this).addClass('btn-hide')
        }
    })
    // END KOMPOSISI DOM //

    // GET DETAIL OBAT UPDS //
    $('select[name="obat"]').change(function(e){
        var val       = $(this).val();
        let attr_obat = ''
        if ($('#input-kasir').attr('btn-attr') === undefined || $('#input-kasir').attr('btn-attr') == '' || $('#input-kasir').attr('btn-attr') == 'kasir-upds') {
            attr_obat = 'none'
        }
        else {
            attr_obat = 'relasi'
        }
        $.ajax({
            url:base_url+`/ajax/get-detail-obat/${val}/${attr_obat}`,
            success: function(data) {
                $('input[name="satuan_obat"]').val(data.satuan_obat);
                // $('#harga-modal').val(data.harga_modal);
                $('#harga-jual').val(data.harga_jual_obat);
                $('#harga-jual-label').html(rupiah_format(data.harga_jual_obat))
                $('#pabrik').val(data.pabrik);
                $('#komposisi-obat').html(data.data_komposisi);
            }
        });
    });
    // END GET DETAIL OBAT UPDS //

    // KREDIT ACT //

        // DETAIL KREDIT //
        $(document).on('click','.kredit-button',function(){
            val = $(this).attr('data-id-kredit');
            id_kredit = val;
            $('#kredit-table').removeClass('open');
            $('#kredit-table').slideUp();
            $.ajax({
                url: base_url + `/ajax/get-kredit-faktur/${val}`
            })
            .done(function(done) {
                $('.data-kredit-faktur').DataTable().destroy();
                $('#kredit-faktur-table').addClass('open');
                $('#kredit-faktur-table').slideDown();
                $('#bayar-semua').attr('id-kredit',val);
                $('.data-kredit-faktur > tbody').html(done);
                $('.data-kredit-faktur').DataTable();
            })
            .fail(function(error) {
                console.log("error");
            });
        });
        // END DETAIL KREDIT //

        // DETAIL KREDIT //
        $(document).on('click','.kredit-faktur-btn',function(){
            val = $(this).attr('id-kredit-faktur');
            id_kredit = val;
            $('#kredit-faktur-table').removeClass('open');
            $('#kredit-faktur-table').slideUp();
            $.ajax({
                url: base_url + `/ajax/get-detail-kredit/${val}`
            })
            .done(function(done) {
                $('.data-detail-kredit').DataTable().destroy();
                $('#kredit-det-table').addClass('open');
                $('#kredit-det-table').slideDown();
                $('#bayar-semua').attr('id-kredit',val);
                $('.data-detail-kredit > tbody').html(done);
                $('.data-detail-kredit').DataTable();
            })
            .fail(function(error) {
                console.log("error");
            });
        });
        // END DETAIL KREDIT //

        // KEMBALI KREDIT FAKTUR //
        $('#back').click(function(){
            $('#kredit-det-table').removeClass('open');
            $('#kredit-det-table').slideUp();
            // $('#kredit-table').addClass('open');
            // $('#kredit-table').slideDown();
            $('#kredit-faktur-table').addClass('open');
            $('#kredit-faktur-table').slideDown()
        });
        // END KEMBALI KREDIT FAKTUR //

        // KEMBALI KREDIT FAKTUR //
        $('#back-faktur').click(function(){
            $('#kredit-faktur-table').removeClass('open');
            $('#kredit-faktur-table').slideUp();
            $('#kredit-table').addClass('open');
            $('#kredit-table').slideDown();
        });
        // END KEMBALI KREDIT FAKTUR //

        // BAYAR SEMUA KREDIT //
        $('#bayar-semua').click(function(){
            let val = $(this).attr('id-kredit')
            $.ajax({
                url: base_url + `/ajax/bayar-kredit-semua/${val}`
            })
            .done(function(done) {
                $('#nama-pelanggan-kasir').html(`<h4><b>Nama Pelanggan : ${done.nama_pelanggan}</b></h4>`)
                $('#modal-kredit').modal('hide');
                $('.data-detail-kredit').DataTable().destroy();
                $('.data-detail-kredit > tbody').empty();
                $('#kredit-det-table').removeClass('open');
                $('#kredit-det-table').slideUp();
                $('#kredit-table').slideDown();
                $('#kredit-table').addClass('open');
                $('.total > b').html(`Total Bayar : ${rupiah_format(done.total_harga)}`);
                $('input[name="total_harga"]').val(done.total_harga);
                $('.transaksi-obat > tbody').append(done.table);
                $('#input-hidden > form').append(done.input_hidden);
            })
            .fail(function(error) {
                console.log(error);
            })
            .always(function() {
                console.log("complete");
            });
        });
        // END BAYAR SEMUA KREDIT //

        // INPUT KREDIT //
        $('#form-kredit').submit(function(event){
            event.preventDefault();
            var data = $('input[name="val_kredit[]"]').serialize();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: base_url + `/ajax/bayar-kredit`,
                type: 'POST',
                data: data,
            })
            .done(function(done) {
                $('#nama-pelanggan-kasir').html(`<h4><b>Nama Pelanggan : ${done.nama_pelanggan}</b></h4>`)
                $('#modal-kredit').modal('hide');
                $('.data-detail-kredit').DataTable().destroy();
                $('.data-detail-kredit > tbody').empty();
                $('#kredit-det-table').removeClass('open');
                $('#kredit-det-table').slideUp();
                $('#kredit-table').slideDown();
                $('#kredit-table').addClass('open');
                $('.total > b').html(`Total Bayar : ${rupiah_format(done.total_harga)}`);
                $('input[name="total_harga"]').val(done.total_harga);
                $('.transaksi-obat > tbody').append(done.table);
                $('#input-hidden > form').append(done.input_hidden);
            })
            .fail(function() {
                console.log("error");
            }); 
        });
        // END INPUT KREDIT //

    // END KREDIT ACT //

    // INPUT BAYAR EVENT //
    $('#bayar').on('keyup',function(){
        $('#bayar-label').html(rupiah_format($(this).val()))
        if ($(this).val() != '') {
            var TotalHarga = $('input[name="total_harga"]').val();
            var kembali = $(this).val() - parseInt(TotalHarga);
            if (kembali < 0) {
                $('#kembali').val(0);
                $('#kembali-label').html(rupiah_format(0))
                $('#submit').attr('disabled','disabled');
            } else {
                $('#kembali').val(kembali);
                $('#kembali-label').html(rupiah_format(kembali))
                $('#submit').removeAttr('disabled');
            }
        }
        else {
            $('#kembali').val(0);
            $('#kembali-label').html(rupiah_format(0))
            $('#submit').attr('disabled','disabled');
        }
    });
    // ================ //

    // SELECT2 PLUGIN //
    $('.select2').select2();
    // ============== //

    // EVENT BUKA MODAL ALT+A //
    $('body').keydown(function(e){
        if (e.keyCode == 65 && e.altKey) {
            $('#modal-default').modal('toggle');
        }
        else if(e.keyCode == 68 && e.altKey) {
            level = window.location.href.split('/')[3];
            window.location.href = base_url + `/level/${panel}`;
        }
        else if(e.keyCode == 83 && e.altKey) {
            $('#modal-kredit').modal('toggle');
        }
        else if(e.keyCode == 90 && e.altKey) {
            $('select[name="obat"]').focus()
            $('select[name="obat"]').select2('open')
        }
        else if(e.keyCode == 88 && e.altKey) {
            if ($('#input-tunai').hasClass('open')) {
                $('input[name="bayar"]').focus();
            }
            else {
                if ($('#input-pelanggan-aja').hasClass('open')) {
                    $('input[name="nama_pelanggan_input"]').focus();
                }
                else {
                    $('select[name="pelanggan_input"]').focus();
                    $('select[name="pelanggan_input"]').select2('open');
                }
            }
        }
    });
    // ===================== //

    // EVENT CHECKBOX TIDAK CHECKED GANDA //
    $('.checkbox-obat').change(function(event){
        $('.checkbox-obat').not(this).prop('checked',false);
        if ($(this).attr('id') == 'kode-obat') {
            $('#input-pilih-obat').removeClass('open');
            $('#input-pilih-obat').slideUp();
            $('#input-kode-obat').addClass('open');
            $('#input-kode-obat').slideDown(function(){
                $('.checkbox-obat').siblings('input[name="kode_obat"]').focus();
            });
        }
        else if($(this).attr('id') == 'pilih-obat') {
            // $('#jenis_obat').focus();
            $('#input-kode-obat').removeClass('open');
            $('#input-kode-obat').slideUp();
            $('#input-pilih-obat').addClass('open');
            $('#input-pilih-obat').slideDown(function(){
                $('.checkbox-obat').siblings('select[name="jenis_obat"]').focus();
            });
        }
    });

    $('.checkbox-diskon').change(function(){
        $('.checkbox-diskon').not(this).prop('checked',false);

        if ($(this).attr('id') == 'input-diskon-persen') {
            $('#diskon-persen').removeClass('form-hide');
            $('#diskon-rupiah').addClass('form-hide');
            $('#diskon-persen-input').val(0)
        }
        else if($(this).attr('id') == 'input-diskon-rupiah') {
            $('#diskon-rupiah').removeClass('form-hide');
            $('#diskon-persen').addClass('form-hide');
            $('#diskon-rupiah-input').val(0)
        } 
    });

    $('#diskon-rupiah-input').keyup(function(){
        let val = $(this).val()
        if (val != 0 || val != '') {
            $('#diskon-rupiah-label').html(`<b>${rupiah_format(val)}</b>`)
        }
    });

    $('.checkbox-transaksi').change(function(){
        $('.checkbox-transaksi').not(this).prop('checked',false);
        if ($(this).attr('id') == 'tunai') {
            $('#input-kredit').removeClass('open');
            $('#input-kredit').slideUp();
            $('#input-tunai').addClass('open');
            $('#input-tunai').slideDown();
            $('#submit').attr('disabled','disabled');
        }
        else if($(this).attr('id') == 'kredit') {
            $('#input-tunai').removeClass('open');
            $('#input-tunai').slideUp();
            $('#input-kredit').addClass('open');
            $('#input-kredit').slideDown();
            $('#submit').removeAttr('disabled');
        } 
    });

    $('.checkbox-pelanggan').click(function(){
        $('.checkbox-pelanggan').not(this).prop('checked',false);
        if ($(this).attr('id') == 'pilih-pelanggan') {
            $('#input-pelanggan-aja').removeClass('open');
            $('#input-pelanggan-aja').slideUp();
            $('#input-pilih-pelanggan').addClass('open');
            $('#input-pilih-pelanggan').slideDown();
        }
        else if($(this).attr('id') == 'input-pelanggan') {
            $('#input-pilih-pelanggan').removeClass('open');
            $('#input-pilih-pelanggan').slideUp();
            $('#input-pelanggan-aja').addClass('open');
            $('#input-pelanggan-aja').slideDown();
        }  
    });
    // ============== //

    // PROSES INPUT OBAT TRANSAKSI UPDS //
    $('#input-kasir').click(function() {
        var btn_attr         = $(this).attr('btn-attr'),
            pcs              = $('input[name="jumlah_obat"]'),
            supplier_obat    = $('select[name="supplier_obat"]'),
            diskon           = $('input[name="diskon_obat"]'),
            obat             = $('select[name="obat"]'),
            jenis_obat       = $('select[name="jenis_obat"]'),
            satuan_obat      = $('input[name="satuan_obat"]'),
            harga_jual       = $('#harga-jual'),
            harga_jual_label = $('#harga-jual-label')
            harga_modal      = $('#harga-modal'),
            pabrik           = $('#pabrik'),
            komposisi        = $('#komposisi-obat')
         
        // let diskon_val = diskon.val() == '' ? 0 : diskon.val()
        if (obat.val() == null && pcs.val() == '') {
            alert('Pilih Obat & Masukkan Jumlah');
        }
        else if (obat.val() == null) {
            alert('Pilih Obat');
        }
        else if (pcs.val() == '') {
            alert('Masukkan Jumlah');
        }
        else {
            var attr_diskon = ''
            var diskon_val  = 0

            if($('#input-diskon-persen').is(':checked')) {
                attr_diskon = 'persen'
            }
            else {
                attr_diskon = 'rupiah'
            }

            if (!$('#diskon-persen').hasClass('form-hide')) {
                diskon_val = $('#diskon-persen-input').val()
            }
            else if(!$('#diskon-rupiah').hasClass('form-hide')) {
                diskon_val = $('#diskon-rupiah-input').val()
            }
            // else if ($('#diskon-persen-input').val() == '' && $('#diskon-rupiah-input').val() == '') {} {
            //     diskon_val = 0
            // }

            ajaxProses(obat.val(),pcs.val(),diskon_val,btn_attr,attr_diskon);
            obat.select2('destroy');
            obat.prop('selectedIndex',0);
            obat.select2();
            obat.focus();
            obat.select2('open');
            pcs.val('');
            satuan_obat.val('');
            harga_jual.val('');
            harga_jual_label.html('Rp. 0,00');
            harga_modal.val('');
            pabrik.val('');
            komposisi.html('<div class="form-group"><input type="text" class="form-control" disabled="disabled"></div>');
            diskon.val(0);
            $('#diskon-rupiah-label').html(`<b>${rupiah_format(0)}</b>`)
        }
    });
    // ============================= //

    $('table').on('click','.delete',function(){
        var get_id          = $(this).attr('data-id'),
            get_harga       = $(`div[target-id="${get_id}"] > input[name="harga_total"]`).val(),
            get_diskon      = $(`div[target-id="${get_id}"] > input[name="diskon_total"]`).val(),
            get_harga_semua = $(`div[target-id="${get_id}"] > input[name="harga_trx[]"]`).val()

        total_harga_  = total_harga_ - parseInt(get_harga);
        total_diskon_ = total_diskon_ - parseInt(get_diskon);
        total_semua_  = total_semua_ - parseInt(get_harga_semua);

        $('.total > b').html(`Total Bayar : ${rupiah_format(total_harga_)}`)
        $('.total-diskon > b').html(`Total Diskon : ${rupiah_format(total_diskon_)}`)
        $('.total-semua > b').html(`Total Semua : ${rupiah_format(total_semua_)}`)
        $('input[name="total_harga"]').val(total_semua_);

        $(`div[target-id="${get_id}"]`).remove();
        if ($('#nama-pelanggan-kasir').length != 0) {
            $('#nama-pelanggan-kasir').remove()
        }
        $(this).closest('tr').remove();
        $('td.number-kasir').each((i,v) => {
            $(v).text(i+1)
        })
    });

    // UBAH PCS //
    // $(document).on('change','.mantul',function(){
    //     var val           = $(this).val();
    //         get_id        = $(this).attr('data-id'),
    //         harga_awal    = $('div[target-id="'+get_id+'"] > input[name="harga_trx[]"]').val(),
    //         total_element = $('input[name="total_harga"]');
    //         jenis_harga   = $('.jen_hrg[data-id="'+get_id+'"]').val();

    //     $.ajax({
    //         url: window.location.origin+'/ajax/ubah-stok/'+get_id+'/'+val+'/'+jenis_harga,
    //     })
    //     .done(function(done) {
    //         harga_total = (total_element.val() - harga_awal) + done;
    //         $('.sub-total[data-id="'+get_id+'"]').val(done);
    //         total_element.val(harga_total);
    //         $('.total > b').html(rupiah_format(harga_total));
    //         $('div[target-id="'+get_id+'"] > input[name="harga_trx[]"]').val(done);
    //         $('div[target-id="'+get_id+'"] > input[name="pcs_trx[]"]').val(val);
    //     })
    //     .fail(function(error) {
    //         console.log(error);
    //     });
    // });
    // ======= //

    // $('#focus-input-obat').click(function(){
    //     if ($('#input-kode-obat').hasClass('open')) {
    //         $('input[name="kode_obat"]').focus();
    //     }
    //     else {
    //         $('select[name="jenis_obat"]').select2('open');
    //     } 
    // });

    // $('#focus-input-bayar').click(function(){
    //     if ($('#input-tunai').hasClass('open')) {
    //         $('input[name="diskon"]').focus();
    //     }
    //     else {
    //         if ($('#input-pelanggan-aja').hasClass('open')) {
    //             $('input[name="nama_pelanggan_input"]').focus();
    //         }
    //         else {
    //             $('select[name="pelanggan_input"]').select2('open');
    //         }
    //     }
    // });

    $('select[name="jenis_racik"]').change(() => {
        let val = $('select[name="jenis_racik"]').val()
        if (val == 'dtd') {

        }
        else if (val == 'non-dtd') {
            $('#form-non-dtd').removeClass('form-hide')
            $('#btn-act-racik-obat').removeClass('form-hide')
        }
    })

    $('#tampil-trx-obat-racik').click(() => {
        if (!$('#trx-obat-tanpa-racik').hasClass('form-hide')) {
            $('#trx-obat-tanpa-racik').slideUp(() => {
                $('#trx-obat-tanpa-racik').addClass('form-hide')
            })
            $('#trx-obat-racik').slideDown(() => {
                $('#trx-obat-racik').removeClass('form-hide')
            })
        }
    })

    $('#tampil-trx-obat-tanpa-racik').click(() => {
        if (!$('#trx-obat-racik').hasClass('form-hide')) {
            $('#trx-obat-racik').slideUp(() => {
                $('#trx-obat-racik').addClass('form-hide')
            })
            $('#trx-obat-tanpa-racik').slideDown(() => {
                $('#trx-obat-tanpa-racik').removeClass('form-hide')
            })
        }
    })

    var select_ajax    = 2;
    var get_ajax       = 2;
    var input_jumlah   = 2;
    var get_jumlah     = 2;
    var satuan_obat    = 2;
    var margin_resep   = 2;
    var embalase_id    = 2;
    var label_harga    = 2;
    var label_total    = 2;
    var label_embalase = 2;
    $('#btn-tambah-racik-obat').click(() => {
        let attr_label_harga    = label_harga - 1
        let attr_label_total    = label_total - 1
        let attr_label_embalase = label_embalase - 1

        $('.obat-racik').select2('destroy')
        $('.supplier-obat').select2('destroy')
        $('#btn-hapus-racik-obat').removeClass('form-hide')
        
        $('#form-input-obat').clone().appendTo('#col-input-obat').find('input').val('')
        
        $('#obat-racik').attr('ajax-id',select_ajax++)
        $('#satuan-obat').attr('satuan-id',satuan_obat++)

        $('#harga-umum').attr('get-ajax-id',get_ajax++)
        $('#harga-umum-racik-label').attr('id-label-umum-racik',label_harga++)
        $(`.harga-umum-racik-label[id-label-umum-racik="${attr_label_harga}"]`).html('Rp. 0,00')
        
        $('#jumlah').attr('id-jumlah',input_jumlah++)

        $('#embalase').attr('id-embalase',embalase_id++)
        $(`.embalase[id-embalase="${attr_label_embalase}"]`).val(0)
        
        $('#margin-resep-racik').attr('get-id-margin-resep',margin_resep++)

        $('#embalase-racik-label').attr('id-label-embalase-racik',label_embalase++)
        $(`.embalase-racik-label[id-label-embalase-racik="${attr_label_embalase}"]`).html('Rp. 0,00')
        
        $('#harga-total').attr('get-id-jumlah',get_jumlah++)
        $('#harga-total-racik-label').attr('id-label-total-racik',label_total++)
        $(`.harga-total-racik-label[id-label-total-racik="${attr_label_total}"]`).html('Rp. 0,00')
        
        $('.obat-racik').select2()
        $('.obat-racik').focus()
    })

    $('#btn-hapus-racik-obat').click(function () {
        $('#col-input-obat #form-input-obat').last().remove()
        if ($('.form-input-obat').length == 1) {
            $(this).addClass('form-hide')
        }
    })

    $(document).on('change','.obat-racik',function() {
        let val  = $(this).val()
        let attr = $(this).attr('ajax-id')
        $.ajax({
            url: base_url + `/ajax/get-info-obat/${val}`,
        })
        .done(function(done) {
            // if (done.stok_obat == 0) {
            //     $(`.jumlah[id-jumlah="${attr}"]`).attr({'min':0,'max':0})   
            // }
            // else {
            //     $(`.jumlah[id-jumlah="${attr}"]`).attr({'min':1,'max':done.stok_obat})
            // }
            $(`.satuan-obat[satuan-id="${attr}"]`).val(done.satuan_obat)
            $(`.harga-umum[get-ajax-id="${attr}"]`).val(done.hja_resep)
            $(`.harga-umum-racik-label[id-label-umum-racik="${attr}"]`).html(rupiah_format(done.hja_resep))
            $(`.margin-resep-racik[get-id-margin-resep="${attr}"]`).val(done.margin_resep)
            $(`.jumlah[id-jumlah="${attr}"]`).focus()
            $(`.obat-racik[ajax-id="${attr}"]`).select2('close')
        })
        .fail(function() {
            console.log("error")
        })
    })

    $(document).on('keyup','.jumlah',function() {
        let val          = $(this).val()
        let attr         = $(this).attr('id-jumlah')
        let harga_umum   = parseInt($(`.harga-umum[get-ajax-id="${attr}"]`).val())
        let margin_resep = parseInt($(`.margin-resep-racik[get-id-margin-resep="${attr}"]`).val())
        let kalkulasi    = harga_umum * val
        let harga_total  = Math.ceil(kalkulasi / 1000) * 1000

        $(`.harga-total[get-id-jumlah="${attr}"]`).val(harga_total)
        $(`.harga-total-racik-label[id-label-total-racik="${attr}"]`).html(rupiah_format(harga_total))
    })

    $(document).on('keyup','.embalase',function() {
        let val  = $(this).val()
        let attr = $(this).attr('id-embalase')

        console.log(val)
        
        $(`.embalase-racik-label[id-label-embalase-racik="${attr}"]`).html(rupiah_format(val))
    })

    // TAMBAH INPUT TANPA RACIK OBAT //
    var __select_ajax    = 2;
    var __get_ajax       = 2;
    var __input_jumlah   = 2;
    var __get_jumlah     = 2;
    var __satuan_obat    = 2;
    var __margin_resep   = 2;
    var __embalase_id    = 2;
    var __label_harga    = 2;
    var __label_total    = 2;
    var __label_embalase = 2;
    $('#btn-tambah-tanpa-racik').click(() => {
        let __attr_label_harga    = __label_harga - 1
        let __attr_label_total    = __label_total - 1
        let __attr_label_embalase = __label_embalase - 1

        $('.obat-tanpa-racik').select2('destroy')
        $('#form-input-tanpa-racik').clone().appendTo('#col-input-tanpa-racik').find('input').val('')
        
        $('#btn-hapus-tanpa-racik').removeClass('form-hide')
        $('#obat-tanpa-racik').attr('ajax-id',__select_ajax++)
        
        $('#satuan-obat-tanpa-racik').attr('satuan-id',__satuan_obat++)
        $('#harga-umum-tanpa-racik').attr('get-ajax-id',__get_ajax++)

        $('#embalase-tanpa-racik').attr('id-embalase',__embalase_id++)
        $(`.embalase-tanpa-racik[id-embalase="${__attr_label_embalase}"]`).val(0)
        
        $('#harga-umum-tanpa-racik-label').attr('id-label-umum-tanpa-racik',__label_harga++)
        $(`.harga-umum-tanpa-racik-label[id-label-umum-tanpa-racik="${__attr_label_harga}"]`).html('Rp. 0,00')
        
        $('#jumlah-tanpa-racik').attr('id-jumlah',__input_jumlah++)
        
        $('#margin-resep-tanpa-racik').attr('get-id-margin-resep',__margin_resep++)
        $('#harga-total-tanpa-racik').attr('get-id-jumlah',__get_jumlah++)
        
        $('#harga-total-tanpa-racik-label').attr('id-label-total-tanpa-racik',__label_total++)
        $(`.harga-total-tanpa-racik-label[id-label-total-tanpa-racik="${__attr_label_total}"]`).html('Rp. 0,00')

        $('#embalase-tanpa-racik-label').attr('id-label-embalase-tanpa-racik',__label_embalase++)
        $(`.embalase-tanpa-racik-label[id-label-embalase-tanpa-racik="${__attr_label_embalase}"]`).html('Rp. 0,00')
        
        $('.obat-tanpa-racik').select2()
        $('.obat-tanpa-racik').focus()
    })

    $('#btn-hapus-tanpa-racik').click(function () {
        $('#col-input-tanpa-racik #form-input-tanpa-racik').last().remove()
        if ($('.form-input-tanpa-racik').length == 1) {
            $(this).addClass('form-hide')
        }
    })

    $(document).on('change','.obat-tanpa-racik',function() {
        let val = $(this).val()
        let attr = $(this).attr('ajax-id')
        $.ajax({
            url: base_url + `/ajax/get-info-obat/${val}`,
        })
        .done(function(done) {
            // $(`.jumlah-tanpa-racik[id-jumlah="${attr}"]`).attr({'min':1,'max':done.stok_obat})
            $(`.satuan-obat-tanpa-racik[satuan-id="${attr}"]`).val(done.satuan_obat)
            $(`.harga-umum-tanpa-racik[get-ajax-id="${attr}"]`).val(done.hja_resep)
            $(`.harga-umum-tanpa-racik-label[id-label-umum-tanpa-racik="${attr}"]`).html(rupiah_format(done.hja_resep))
            $(`.margin-resep-tanpa-racik[get-id-margin-resep="${attr}"]`).val(done.margin_resep)
            $(`.jumlah-tanpa-racik[id-jumlah="${attr}"]`).focus()
            $(`.obat-tanpa-racik[ajax-id="${attr}"]`).select2('close')
        })
        .fail(function() {
            console.log("error")
        })
    })

    $(document).on('keyup','.jumlah-tanpa-racik',function() {
        let val          = $(this).val()
        let attr         = $(this).attr('id-jumlah')
        let harga_umum   = $(`.harga-umum-tanpa-racik[get-ajax-id="${attr}"]`).val()
        let margin_resep = $(`.margin-resep-tanpa-racik[get-id-margin-resep="${attr}"]`).val()
        let kalkulasi    = harga_umum * val
        let harga_total  = Math.ceil(kalkulasi / 1000) * 1000

        $(`.harga-total-tanpa-racik[get-id-jumlah="${attr}"]`).val(harga_total)
        $(`.harga-total-tanpa-racik-label[id-label-total-tanpa-racik="${attr}"]`).html(rupiah_format(harga_total))
    })

    $(document).on('keyup','.embalase-tanpa-racik',function() {
        let val  = $(this).val()
        let attr = $(this).attr('id-embalase')
        
        $(`.embalase-tanpa-racik-label[id-label-embalase-tanpa-racik="${attr}"]`).html(rupiah_format(val))
    })

    // END TAMBAH OBAT TANPA RACIK //

    function clearInputRacik() {
        $('#racik-section').find('input,select').val('')
        $('#form-input-obat').find('input,select').val('')
        $('.keterangan-racik').select2('destroy')
        $('.keterangan-racik').select2()
        $('.form-input-obat').slice(1).remove()
        $('.obat-racik').select2('destroy')
        $('.obat-racik').select2()
        $('.supplier-obat').select2('destroy')
        $('.supplier-obat').select2()
        $('.supplier-obat').attr('disabled','disabled')
        $('.harga-umum-racik-label').html('Rp. 0,00')
        $('.harga-total-racik-label').html('Rp. 0,00')
        $('.embalase-racik').val(0)
        $('.embalase-racik-label').html('Rp. 0,00')
        $('#ongkos-racik-label').html('Rp. 0,00')
        
        $('#btn-hapus-racik-obat').addClass('form-hide')
    }

    function clearInputTanpaRacik() {
        $('#form-input-tanpa-racik').find('input,select').val('')
        $('.form-input-tanpa-racik').slice(1).remove()
        $('.obat-tanpa-racik').select2('destroy')
        $('.obat-tanpa-racik').select2()
        $('.harga-umum-tanpa-racik-label').html('Rp. 0,00')
        $('.harga-total-tanpa-racik-label').html('Rp. 0,00')
        $('.embalase-tanpa-racik').val(0)
        $('.embalase-tanpa-racik-label').html('Rp. 0,00')

        $('#btn-hapus-tanpa-racik').addClass('form-hide')
    }

    $('.form-post-racik').submit(function (e) {
        e.preventDefault()

        let obat        = $(this).serialize()
        let kode_racik  = $('input[name="kode_racik"]').val()
        let total_semua = $('#total-semua').val()
        let input_val   = `${obat}&kode_racik=${kode_racik}&total_semua=${total_semua}`
        $('#simpan-racikan').html(`
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
          `)
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: base_url+'/ajax/simpan-racik',
            type: 'POST',
            data: input_val,
        })
        .done(function(done) {
            let diskon    = $('#diskon-resep').val()
            var kalkulasi = 0
            if (diskon != '' || diskon != null) {
                kalkulasi = done.total_semua - ((done.total_semua * diskon) / 100)
                kalkulasi = Math.floor(kalkulasi / 1000) * 1000
            }
            else {
                kalkulasi = done.total_semua
            }
            $('input[name="kode_racik"]').val(done.kode_racik)
            $('#simpan-racikan').html('Simpan Racikan')
            $('#modal-input-racik').modal('hide')
            $('.transaksi-racik-obat').append(done.data_racik.table)
            $('#total-semua').val(done.total_semua)
            $('#total-semua-label').html(`Harga Total : ${rupiah_format(done.total_semua)}`)
            $('#total-racik').val(kalkulasi)
            $('#total-racik-label').html(rupiah_format(kalkulasi))
            $('#kembalian-racik').val(0)
            clearInputRacik()
            $('td.number-resep').each((i,v) => {
                $(v).text(i+1)
            })
        })
        .fail(function() {
            console.log("error");
        });
    })

    $('.form-post-tanpa-racik').submit(function (e) {
        e.preventDefault()
        let obat        = $(this).serialize()
        let kode_racik  = $('input[name="kode_racik"]').val()
        let total_semua = $('#total-semua').val()
        let input_val   = `${obat}&kode_racik=${kode_racik}&total_semua=${total_semua}`

        $('#simpan-resep').html(`
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
          `)
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: base_url+`/ajax/simpan-tanpa-racik`,
            type: 'POST',
            data: input_val,
        })
        .done(function(done) {
            let diskon    = $('#diskon-resep').val()
            var kalkulasi = 0
            if (diskon != '' || diskon != null) {
                kalkulasi = done.total_semua - ((done.total_semua * diskon) / 100)
                kalkulasi = Math.floor(kalkulasi / 1000) * 1000
            }
            else {
                kalkulasi = done.total_semua
            }
            $('input[name="kode_racik"]').val(done.kode_racik)
            $('#simpan-resep').html('Simpan')
            $('#modal-input-tanpa-racik').modal('hide')
            $('.transaksi-racik-obat').append(done.data_racik.table)
            $('#total-racik').val(kalkulasi)
            $('#total-racik-label').html(rupiah_format(kalkulasi))
            $('#total-semua').val(done.total_semua)
            $('#total-semua-label').html(`Harga Total : ${rupiah_format(done.total_semua)}`)
            $('#kembalian-racik').val(0)
            clearInputTanpaRacik()
            $('td.number-resep').each((i,v) => {
                $(v).text(i+1)
            })
        })
        .fail(function() {
            console.log("error");
        });
    })

    // $('#modal-input-racik').click(() => {
    //     $('input[name="nama_racik"]').focus()
    // })

    $('#modal-input-racik').on('shown.bs.modal',() => {
        $('input[name="nama_racik"]').focus()
    })

    $('#modal-input-tanpa-racik').on('shown.bs.modal',() => {
        $('.obat-tanpa-racik').focus()
    })

    $('#close-modal').click(() => {
        clearInputRacik()
    })

    $('#close-tanpa-racik').click(() => {
        clearInputTanpaRacik()
    })

    $('#diskon-resep').keyup(function(){
        let val         = $(this).val()
        let total_harga = $('#total-semua').val()
        if (val != '' && total_harga != '') {
            var hitung = total_harga - ((total_harga * val) / 100)
            hitung = Math.ceil(hitung / 1000) * 1000
            $('#total-racik').val(hitung)
            $('#total-racik-label').html(rupiah_format(hitung))
        }
        else {
            $('#total-racik').val(total_harga)
            $('#total-racik-label').html(rupiah_format(total_harga))
        }
    });
    // ============================= //

    $('table').on('click','.delete-racik',function(){
        let get_id      = $(this).attr('id-delete');
        let total_semua = $('#total-semua').val()
        let input_val   = `id_racik=${get_id}&total_semua=${total_semua}`
        $(this).html(`
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
          `)

        $.ajax({
            url: base_url+`/ajax/delete-racik`,
            type: 'GET',
            data:input_val,
        })
        .done(function(done) {
            $('#total-semua').val(done)
            $('#total-semua-label').html(`Harga Total : ${rupiah_format(done)}`)
            $('#total-racik').val(done)
            $('#total-racik-label').html(rupiah_format(done))
            $('td.number-resep').each((i,v) => {
                $(v).text(i+1)
            })
        })
        .fail(function() {
            console.log("error");
        });
            
        $(this).closest('tr').remove()
    });

    $(document).on('click','.detail-racik',function(){
        let get_id = $(this).attr('id-detail');

        $(this).html(`
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
          `)

        $.ajax({
            url: base_url+`/ajax/detail-racik/${get_id}`,
            type: 'GET',
        })
        .done(function(done) {
            $('.detail-racik').html('<span class="fa fa-info"></span>')
            $('.detail-racik-obat > tbody').html(done)
        })
        .fail(function() {
            console.log("error");
        });
    });

    $('table').on('click','.delete-tanpa-racik',function(){
        let get_id      = $(this).attr('id-delete');
        let total_semua = $('#total-semua').val()
        let input_val   = `id_racik=${get_id}&total_semua=${total_semua}`

        $(this).html(`
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
          `)

        $.ajax({
            url: base_url+`/ajax/delete-tanpa-racik`,
            type: 'GET',
            data:input_val
        })
        .done(function(done) {
            $('#total-semua').val(done)
            $('#total-semua-label').html(`Harga Total : ${rupiah_format(done)}`)
            $('#total-racik').val(done)
            $('#total-racik-label').html(rupiah_format(done))
            $('td.number-resep').each((i,v) => {
                $(v).text(i+1)
            })
        })
        .fail(function() {
            console.log("error");
        });
        
        $(this).closest('tr').remove()
    });

    $('#bayar-racik').keyup(function() {
        let val       = $(this).val()
        let total     = $('#total-racik').val()
        $('#bayar-racik-label').html(rupiah_format(val))

        if (val != '') {
            let kembalian = parseInt(val) - parseInt(total)
            if (kembalian >= 0) {
                $('#kembalian-racik').val(kembalian)
                $('#kembalian-racik-label').html(rupiah_format(kembalian))
                $('#bayar-resep').removeAttr('disabled')
            }
            else {
                $('#kembalian-racik').val('')
                $('#kembalian-racik-label').html(rupiah_format(0))
                $('#bayar-resep').prop('disabled',true)
            }
        }
        else {
            $('#kembalian-racik').val('')
            $('#kembalian-racik-label').html(rupiah_format(0))
            $('#bayar-resep').prop('disabled',true)
        }
    })

    $('#input-pasien-act').click(function() {
        $(this).addClass('form-hide')
        $('#pilih-pasien-act').removeClass('form-hide')
        $('#input-pasien').find('input,select').removeAttr('disabled')
        $('#input-pasien').find('input,select').attr('required','required')
        $('#input-pasien').removeClass('form-hide')
        $('#select-pasien').find('select').attr('disabled','disabled')
        $('#select-pasien').addClass('form-hide')
    })

    $('#pilih-pasien-act').click(function() {
        $(this).addClass('form-hide')
        $('#input-pasien-act').removeClass('form-hide')
        $('#select-pasien').find('select').removeAttr('disabled')
        $('#select-pasien').find('select').attr('required','required')
        $('#select-pasien').removeClass('form-hide')
        $('#input-pasien').find('input,select').attr('disabled','disabled')
        $('#input-pasien').addClass('form-hide')
    })

    $('#input-dokter-act').click(function() {
        $(this).addClass('form-hide')
        $('#pilih-dokter-act').removeClass('form-hide')
        $('#input-dokter').find('input,select').removeAttr('disabled')
        $('#input-dokter').find('input,select').attr('required','required')
        $('#input-dokter').removeClass('form-hide')
        $('#select-dokter').find('select').attr('disabled','disabled')
        $('#select-dokter').addClass('form-hide')
    })

    $('#pilih-dokter-act').click(function() {
        $(this).addClass('form-hide')
        $('#input-dokter-act').removeClass('form-hide')
        $('#select-dokter').find('select').removeAttr('disabled')
        $('#selec-dokter').find('select').attr('required','required')
        $('#select-dokter').removeClass('form-hide')
        $('#input-dokter').find('input,select').attr('disabled','disabled')
        $('#input-dokter').addClass('form-hide')
    })

    // ENTER NEXT INPUT EVENT //
    // $('body').on('keydown','input,select,textarea',function(e){
    //     var self = $(this),
    //         form = self.parents('form:eq(0)'),
    //         focusable,
    //         next
    //         ;
    //     if (e.keyCode == 13) {
    //         focusable = form.find('input,a,select,button,textarea').filter(':visible');
    //         next = focusable.eq(focusable.index(this)+1);
    //         if (next.length) {
    //             next.focus();
    //         }
    //         else {
    //             next.submit();
    //         }
    //         return false;
    //     }
    // });

    $('.section-obat').on('keydown','input,select,textarea',function(e){
        var self = $(this),
            form = self.parents('form:eq(0)'),
            focusable,
            next
            ;
        if (e.keyCode == 13) {
            focusable = form.find('input,a,select,button,textarea').filter(':visible');
            next = focusable.eq(focusable.index(this)+1);
            console.log(next);
            if (next.length) {
                next.focus();
            }
            else {
                next = focusable.eq(0);
                next.focus();
            }
            return false;
        }
    });

    $('.section-transaksi').on('keydown','input,select,textarea',function(e){
        var self = $(this),
            form = self.parents('form:eq(0)'),
            focusable,
            next
            ;
        if (e.keyCode == 13) {
            focusable = form.find('input,a,select,button,textarea').filter(':visible');
            next = focusable.eq(focusable.index(this)+1);
            console.log(next);
            if (next.length) {
                next.focus();
            }
            else {
                next.submit();
            }
            return false;
        }
    });
    // ================== //

});