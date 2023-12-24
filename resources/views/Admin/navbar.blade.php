<header class="main-header">
  <!-- Logo -->
  <a href="#" class="logo">
    <span class="logo-lg"><b>Apotik </b></span>
  </a>

  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="{{asset('apotek_bunda_farma.jpeg')}}" class="user-image" alt="User Image">
            <span class="hidden-xs">{{Auth::user()->name}}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="{{asset('apotek_bunda_farma.jpeg')}}" class="img-circle" alt="User Image">
              <p>
                {{Auth::user()->name}} | Kasir Apotek Bunda Farma
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="{{url('/admin/ubah-profile')}}" class="btn btn-default btn-flat">Profile</a>
              </div>
              <div class="pull-right">
                <a href="{{url('/logout')}}" class="btn btn-default btn-flat">Logout</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>

  </nav>
</header>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{asset('apotek_bunda_farma.jpeg')}}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{Auth::user()->name}}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li @if(isset($page)){!!$page=='dashboard' ? 'class="active"' : '' !!} @endif>
        <a href="{{url('/admin/panel')}}"><i class="fa fa-dashboard"></i> Dashboard</a>
      </li>
      <li class="treeview @if(isset($link)){!!$link=='data-master'?'active menu-open':''!!}@endif">
        <a href="#">
          <i class="fa fa-database"></i> <span>Data Master</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li @if(isset($page)){!!$page=='jam-shift' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/jam-shift')}}"><i class="fa fa-circle-o"></i> Jam Shift</a>
          </li>
          <li @if (isset($page)){!!$page=='data-pasien' ? 'class="active"' : '' !!}@endif>
            <a href="{{url('/admin/data-pasien')}}"><i class="fa fa-circle-o"></i> Data Pasien</a>
          </li>
          <li @if (isset($page)){!!$page=='data-dokter' ? 'class="active"' : '' !!}@endif>
            <a href="{{url('/admin/data-dokter')}}"><i class="fa fa-circle-o"></i> Data Dokter</a>
          </li>
          <li @if(isset($page)){!!$page=='margin-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/margin-obat') }}"><i class="fa fa-circle-o"></i> Margin Obat</a>
          </li>
          <li @if (isset($page)){!!$page=='data-ppn' ? 'class="active"' : '' !!}@endif>
            <a href="{{ url('/admin/data-ppn') }}"><i class="fa fa-circle-o"></i> Data PPn</a>
          </li>

          <!-- <li @if (isset($page)){!!$page == 'data-debitur' ? 'class="active"' : ''!!}@endif>
              <a href="{{ url('/admin/data-debitur') }}"><i class="fa fa-circle-o"></i> Data Debitur</a>
            </li> -->
        </ul>
      </li>
      <li class="treeview @if(isset($link)){!!$link=='obat'?'active menu-open':''!!}@endif">
        <a href="#">
          <i class="fa fa-medkit"></i> <span>Obat</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li @if(isset($page)){!!$page=='data-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/data-obat') }}"><i class="fa fa-circle-o"></i> Data Obat</a>
          </li>
          <li @if(isset($page)){!!$page=='data-supplier-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/data-supplier-obat') }}"><i class="fa fa-circle-o"></i> Data Supplier Obat</a>
          </li>
          <li @if(isset($page)){!!$page=='data-pabrik-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/data-pabrik-obat') }}"><i class="fa fa-circle-o"></i> Data Pabrik Obat</a>
          </li>
          <li @if(isset($page)){!!$page=='data-jenis-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/data-jenis-obat')}}"><i class="fa fa-circle-o"></i> Bentuk Sediaan Obat</a>
          </li>
          <li @if (isset($page)){!!$page=='data-golongan-obat' ? 'class="active"' : '' !!}@endif>
            <a href="{{url('/admin/data-golongan-obat')}}"><i class="fa fa-circle-o"></i> Data Golongan Obat</a>
          </li>
        </ul>
      </li>
      <li class="treeview @if(isset($link)){!!$link=='pembelian'?'active menu-open':''!!}@endif">
        <a href="#">
          <i class="fa fa-money"></i> <span>Pembelian</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li @if(isset($page)){!!$page=='data-pembelian' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/data-pembelian')}}"><i class="fa fa-circle-o"></i> Data Pembelian Obat</a>
          </li>
          <li @if (isset($page)){!!$page=='kartu-stok' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/kartu-stok') }}"><i class="fa fa-circle-o"></i> Kartu Stok</a>
          </li>
          <li @if (isset($page)){!!$page=='history-beli' ? 'class="active"' : '' !!} @endif>
            <a href="{{ url('/admin/history-beli') }}"><i class="fa fa-circle-o"></i> History Beli</a>
          </li>
        </ul>
      </li>
      <li class="treeview @if(isset($link)){!!$link=='penjualan'?'active menu-open':''!!}@endif">
        <a href="#">
          <i class="fa fa-money"></i> <span>Penjualan</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li>
            <a href="{{url('/admin/penjualan')}}"><i class="fa fa-circle-o"></i> Penjualan UPDS</a>
          </li>
          <li>
            <a href="{{url('/admin/racik-obat')}}"><i class="fa fa-circle-o"></i> Penjualan Resep</a>
          </li>
          <li>
            <a href="{{ url('/admin/penjualan-relasi') }}"><i class="fa fa-circle-o"></i> Penjualan Relasi</a>
          </li>
          <li @if(isset($page)){!!$page=='data-penjualan' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/data-penjualan')}}"><i class="fa fa-circle-o"></i> Data Penjualan UPDS</a>
          </li>
          <li @if (isset($page)){!!$page=='data-penjualan-racik-obat' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/data-penjualan-racik-obat')}}"><i class="fa fa-circle-o"></i> Data Penjualan
              Resep</a>
          </li>
          <li @if(isset($page)){!!$page=='data-kredit' ? 'class="active"' : '' !!} @endif>
            <a href="{{url('/admin/data-kredit')}}"><i class="fa fa-circle-o"></i> Data Kredit</a>
          </li>
          <li @if (isset($page)){!!$page=='retur-barang' ? 'class="active"' : '' !!}@endif>
            <a href="{{ url('/admin/retur-barang') }}"><i class="fa fa-circle-o"></i> Retur Barang</a>
          </li>
        </ul>
      </li>
      <li @if (isset($page)){!!$page=='laporan-data' ? 'class="active"' : '' !!}@endif>
        <a href="{{url('/admin/laporan-data')}}"><i class="fa fa-database"></i> Laporan Data</a>
      </li>
      <li @if (isset($page)){!!$page=='stok-opnem' ? 'class="active"' : '' !!}@endif>
        <a href="{{url('/admin/stok-opnem')}}"><i class="fa fa-database"></i> Stok Opnem</a>
      </li>
      <li @if(isset($page)){!!$page=='data-users' ? 'class="active"' : '' !!} @endif>
        <a href="{{url('/admin/data-users')}}"><i class="fa fa-users"></i> Data User</a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>