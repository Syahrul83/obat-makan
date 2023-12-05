<!DOCTYPE html>
<html>
<head>
    <title>Cetak Nota Penjualan</title>
    <link rel="stylesheet" href="{{ asset('normalize.css') }}">
    <style>
        body.receipt .sheet { width: 70mm; } /* sheet size */
        @media print { body.receipt { width: 70mm } .btn-hide { display: none !important; } } /* fix for Chrome */

        * {
            font-size: 14px;
            padding: 0;
            margin: 0;
            font-family: 'Arial';
            line-height: 23px;
            text-transform: uppercase;
        }
        .left {
            float: left;
        }
        .right {
            float: right;
        }
        .padding-5 {
            padding: 5mm;
        }
        .title  {
            font-size: 16px;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle  {
            font-size: 14px;
            text-align: center;
            margin-bottom: 5px;
        }
        .regards {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
        }
        hr {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        tfoot tr td:first-child {
            text-align: right;
            padding-right: 10px;
        }
    </style>
    <script type="text/javascript">
        window.print();
    </script>
</head>
<body class="receipt">
    <p>
        <a href="{{ url('/admin/penjualan') }}" class="btn-hide">Kembali</a>
    </p>
    <section class="sheet padding-5">
        <h3 class="title">{{$profile_instansi->nama_instansi}}</h3>
        <h4 class="subtitle">{{$profile_instansi->alamat_instansi}}</h4>
        <h4 class="subtitle">Telp : {{$profile_instansi->nomor_telepon_instansi}}</h4>
        <hr>
        <h4 class="subtitle" align="center">BUKTI TRANSAKSI</h4>
        <hr>
        <div>
            <p>No Nota: {{ $transaksi->kode_transaksi }}</p>
            <p>KASIR : {{ $transaksi->name }}</p>
        </div>
        <div>
            <p class="right">{{ human_date($transaksi->tanggal_transaksi) }}</p>
            <br>
            <p class="right">{{ $transaksi->jam_transaksi }}</p>
        </div>
        <div style="clear: both"></div>
        <hr>
        <table border="0" width="100%">
            <tbody>
            	@foreach ($transaksi_detail as $key => $value)
                <tr>
                    <td width="60%" class="item-name">{{ $value->nama_obat }}</td>
                    <td>{{ $value->jumlah.' '.$value->satuan_obat }}</td>
                    <td>{{ money_receipt($value->sub_total) }}</td>
                </tr>
            	@endforeach
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
            </tbody>
        </table>
        <table width="100%">
            <tr>
                <td style="text-align: right; padding-right: 10px" width="85%">Total</td>
                <td> {{ money_receipt($sum_sub_total_obat) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px" width="85%">Total Diskon</td>
                <td> {{ money_receipt($sum_diskon) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px" width="85%">Grand Total</td>
                <td> {{ money_receipt($transaksi->total) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Bayar </td>
                <td> {{ money_receipt($transaksi->bayar) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Kembalian </td>
                <td> {{ money_receipt($transaksi->kembali) }}</td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr>
                <td style="text-align:center; padding-right: 10px;">Terima Kasih</td>
            </tr>
            <tr>
                <td style="text-align:center; padding-right: 10px;">Semoga Lekas Sembuh</td>
            </tr>
            <tr>
                <td style="text-align:center; padding-right: 10px;">Harga Sudah Termasuk PPn</td>
            </tr>
        </table>
        <p>
            <button type="button" class="btn-hide" onclick="window.print()">Cetak</button>
        </p>
    </section>
</body>
</html>