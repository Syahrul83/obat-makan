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
        .page-break-after {
            page-break-after: always;
        }
    </style>
    <script type="text/javascript">
        window.print();
    </script>
</head>
<body class="receipt">
    <p>
        <a href="{{ url('/admin/data-penjualan-racik-obat') }}" class="btn-hide">Kembali</a>
    </p>
    <section class="sheet padding-5">
        <h3 class="title">{{$profile_instansi->nama_instansi}}</h3>
        <h4 class="subtitle">{{$profile_instansi->alamat_instansi}}</h4>
        <h4 class="subtitle">Telp : {{$profile_instansi->nomor_telepon_instansi}}</h4>
        <hr>
        <p>Pasien &nbsp;&nbsp;&nbsp;&nbsp;: {{ $get_pasien->nama_pasien }}</p>
        <p>Alamat &nbsp;&nbsp;&nbsp;: {{ $get_pasien->alamat_pasien }}</p>
        <p>Telepon : {{ $get_pasien->nomor_telepon_pasien }}</p>
        <p>Dokter &nbsp;&nbsp;: {{ $get_dokter->nama_dokter }}</p>
        <hr>
        <h4 class="subtitle" align="center">BUKTI TRANSAKSI</h4>
        <hr>
        <div>
            <p>No Nota: {{ $transaksi_racik->kode_transaksi }}</p>
            <p>KASIR : {{ $transaksi_racik->name }}</p>
        </div>
        <div>
            <p class="right">{{ human_date($transaksi_racik->tanggal_transaksi) }}</p>
            <br>
            <p class="right">{{ $transaksi_racik->jam_transaksi }}</p>
        </div>
        <div style="clear: both"></div>
        <hr>
        <table border="0" width="100%">
            <tbody>
                {{-- @foreach ($get_racik as $index => $value)
                    @foreach ($get_racik_detail->getData($value->id_racik_obat) as $key => $element)
                    <tr>
                        <td>{{ $element->nama_obat }}</td>
                        <td>{{ $element->jumlah.' '.$element->satuan_obat }}</td>
                        <td>{{ money_receipt($element->sub_total) }}</td>
                    </tr>
                    @endforeach
                @endforeach --}}
                @foreach ($get_obat_non_resep as $value)
                <tr>
                    <td>{{ $value->nama_obat }}</td>
                    <td>{{ $value->jumlah.' '.$value->satuan_obat }}</td>
                    <td>{{ money_receipt($value->sub_total+$value->embalase) }}</td>
                </tr>
                @endforeach
                @foreach ($get_resep_total as $value)
                <tr>
                    <td>{{ $value->nama_racik }}</td>
                    <td>{{ $value->jumlah_racik.' '.$value->keterangan_racik }}</td>
                    <td>{{ money_receipt($value->harga_total_racik+$value->ongkos_racik) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
            </tbody>
        </table>
        <table width="100%">
            <tr>
                <td style="text-align:right; padding-right:10px;">Total</td>
                <td>{{ money_receipt($sum_total_racik) }}</td>
            </tr>
            @if ($transaksi_racik->diskon != 0)
            <tr>
                <td style="text-align:right; padding-right:10px;">Diskon</td>
                <td>{{ money_receipt(real_discount(get_discount($transaksi_racik->diskon,$sum_total_racik),1000,$transaksi_racik->harga_total,$sum_total_racik)) }}</td>
            </tr>
            @else
            <tr>
                <td style="text-align:right; padding-right:10px;">Diskon</td>
                <td>{{ 0 }}</td>
            </tr>
            @endif
            <tr>
                <td style="text-align: right; padding-right: 10px" width="85%">Grand Total</td>
                <td> {{ money_receipt($transaksi_racik->harga_total) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Bayar </td>
                <td> {{ money_receipt($transaksi_racik->bayar) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Kembalian </td>
                <td> {{ money_receipt($transaksi_racik->kembalian) }}</td>
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
    </section>
    <div class="page-break-after"></div>
    <section class="sheet padding-5">
        <h3 class="title">{{$profile_instansi->nama_instansi}}</h3>
        <h4 class="subtitle">{{$profile_instansi->alamat_instansi}}</h4>
        <h4 class="subtitle">Telp : {{$profile_instansi->nomor_telepon_instansi}}</h4>
        <hr>
        <p>Nama Pasien &nbsp;&nbsp;: {{ $get_pasien->nama_pasien }}</p>
        <p>Nama Dokter : {{ $get_dokter->nama_dokter }}</p>
        <hr>
        <h4 class="subtitle" align="center">BUKTI TRANSAKSI</h4>
        <hr>
        <p>No Nota: {{ $transaksi_racik->kode_transaksi }}</p>
        <div>
            <p class="left">{{ $transaksi_racik->name }}</p>
            <p class="right">{{ human_date($transaksi_racik->tanggal_transaksi) }}</p>
            <br>
            <p class="right">{{ $transaksi_racik->jam_transaksi }}</p>
        </div>
        <div style="clear: both"></div>
        <hr>
        <table border="0" width="100%">
            <tbody>
                {{-- @foreach ($get_racik as $index => $value)
                    @foreach ($get_racik_detail->getData($value->id_racik_obat) as $key => $element)
                    <tr>
                        <td>{{ $element->nama_obat }}</td>
                        <td>{{ $element->jumlah.' '.$element->satuan_obat }}</td>
                        <td>{{ money_receipt($element->sub_total) }}</td>
                    </tr>
                    @endforeach
                @endforeach --}}
                @foreach ($get_obat_non_resep as $value)
                <tr>
                    <td>{{ $value->nama_obat }}</td>
                    <td>{{ $value->jumlah.' '.$value->satuan_obat }}</td>
                    <td>{{ money_receipt($value->sub_total+$value->embalase) }}</td>
                </tr>
                @endforeach
                @foreach ($get_obat_resep as $value)
                <tr>
                    <td>{{ $value->nama_obat }}</td>
                    <td>{{ $value->jumlah.' '.$value->satuan_obat }}</td>
                    <td>{{ money_receipt($value->sub_total+$value->embalase) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
            </tbody>
        </table>
        <table width="100%">
            <tr>
                <td style="text-align:right; padding-right:10px;">Total</td>
                <td>{{ money_receipt($sum_total_racik) }}</td>
            </tr>
            @if ($transaksi_racik->diskon != 0)
            <tr>
                <td style="text-align:right; padding-right:10px;">Diskon</td>
                <td>{{ money_receipt(real_discount(get_discount($transaksi_racik->diskon,$sum_total_racik),1000,$transaksi_racik->harga_total,$sum_total_racik)) }}</td>
            </tr>
            @else
            <tr>
                <td style="text-align:right; padding-right:10px;">Diskon</td>
                <td>{{ 0 }}</td>
            </tr>
            @endif
            <tr>
                <td style="text-align: right; padding-right: 10px" width="85%">Grand Total</td>
                <td> {{ money_receipt($transaksi_racik->harga_total) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Bayar </td>
                <td> {{ money_receipt($transaksi_racik->bayar) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px">Kembalian </td>
                <td> {{ money_receipt($transaksi_racik->kembalian) }}</td>
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