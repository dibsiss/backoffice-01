<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Beckoffice | Dasboard</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url('assets/template') ?>/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo base_url('assets/template/font-awesome') ?>/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo base_url('assets/template/ionicons') ?>/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url('assets/template') ?>/dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo base_url('assets/template') ?>/dist/css/skins/skin-blue.min.css">
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <?php
        $imageUser = $this->session->userdata('foto');
        (empty($imageUser)) ? $imageUser = 'default-superadmin.png' : $imageUser = $imageUser;
        $cek = $this->tr->cekImageExist($imageUser);
        ($cek) ? $imageUser = $imageUser : $imageUser = 'default-superadmin.png';
        $fullname = ucwords($this->session->userdata('fullname'));
        ?>
        <div class="wrapper">
            <!-- Main Header -->
            <header class="main-header">
                <!-- Logo -->
                <a href="<?= site_url('/superadmin/'); ?>" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>X-</b>metrik</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>X-Metrik</b></span>
                </a>
                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown notifications-menu">
                                <!-- Menu toggle button -->
                                <a href="javascript:void(0)" onclick="manualCheck()" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-refresh"></i>
                                    Refresh
                                </a>
                            </li>
                            <!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu" id="notif-kirim">
                                <!-- notif pengiriman -->
                            </li>
                            <li class="dropdown messages-menu" id="notif-kirim-sumber">
                                <!-- notif pengiriman -->
                            </li>
                            <li class="dropdown messages-menu" id="notif-reject">
                                <!-- notif pengiriman -->
                            </li>
                            <li class="dropdown messages-menu" id="notif-retur">
                                <!-- notif retur -->
                            </li>
                            <li class="dropdown messages-menu" id="notif-permintaan">
                                <!-- notif retur -->
                            </li>
                            <li class="dropdown messages-menu" id="notif-complain">
                                <!-- notif retur -->
                            </li>
                            <!-- /.messages-menu -->
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="<?php echo base_url('assets/uploads/files/') . $imageUser ?>" class="user-image" alt="User Image"/>
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs"><?php echo $fullname; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img src="<?php echo base_url('assets/uploads/files/') . $imageUser ?>" class="img-circle" alt="User Image">
                                        <p>
                                            <?php echo $fullname ?>
                                            <small>Superadmin</small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">

                                        <!-- /.row -->
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?php echo site_url('superadmin/user/edit/') . $this->id_user ?>" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?php echo site_url('login/superadmin/keluar_superadmin'); ?>" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#"><i class="fa fa-arrows"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">

                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">

                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel">
                        <div class="pull-left image">

                            <img src="<?php echo base_url('assets/uploads/files/') . $imageUser ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p><?php echo $fullname ?></p>
                            <!--Status--> 
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- Sidebar Menu -->
                    <ul class="sidebar-menu">
                        <li class="header">MENU</li>
                        <!-- Optionally, you can add icons to the links -->
						<li><a href="<?= site_url('superadmin'); ?>"><i class="fa  fa-gears"></i> <span>Beranda</span></a></li>
                        <li class="treeview">
                            <a href="#"><i class="fa fa-cubes"></i> <span>Master</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?= site_url('superadmin/merk'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Merk</a></li>
                                <li><a href="<?= site_url('superadmin/category'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Category</a></li>
                                <li><a href="<?= site_url('superadmin/jenis_non_fisik'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Jenis Non Fisik</a></li>
                                <li><a href="<?= site_url('superadmin/mst_retur'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Status Return</a></li>
                                <li><a href="<?= site_url('superadmin/barang'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Barang</a></li>
                                <li><a href="<?= site_url('superadmin/supliyer'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Supliyer</a></li>
                                <li><a href="<?= site_url('superadmin/toko'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Toko</a></li>
                                <li><a href="<?= site_url('superadmin/gudang'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Gudang</a></li>
                                <li><a href="<?= site_url('superadmin/mst_bank'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Bank</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= site_url('umum/mst_harga'); ?>"><i class="fa fa-balance-scale"></i> <span>Harga Barang</span></a></li>
                        <li><a href="<?= site_url('superadmin/stokTersedia'); ?>"><i class="fa fa-database"></i> <span>Stok Barang</span></a></li>


                        <li class="treeview">
                            <a href="#"><i class="fa fa-users"></i> <span>User</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?= site_url('superadmin/User'); ?>"><i class="fa fa-check" aria-hidden="true"></i>User</a></li>
                                <li><a href="<?= site_url('superadmin/Usergudang'); ?>"><i class="fa fa-check" aria-hidden="true"></i>User Gudang</a></li>
                                <li><a href="<?= site_url('superadmin/Usertoko'); ?>"><i class="fa fa-check" aria-hidden="true"></i>User Toko</a></li>
                            </ul>
                        </li>

                        <li class="treeview">
						 <li><a href="<?= site_url('superadmin/history_awal'); ?>"><i class="fa fa-balance-scale"></i> <span>Insert History Awal</span>
                            <a href="#"><i class="fa fa-shopping-cart"></i> <span>Transaksi</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="treeview">
                                    <a href="#"><i class="fa fa-sign-in"></i> <span>Input</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li><a href="<?= site_url('superadmin/tr_pengadaan'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengadaan</a></li>
                                        <li><a href="<?= site_url('superadmin/inputReturHarga'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur Harga</a></li>
                                        <li><a href="<?= site_url('superadmin/inputReturRusak'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur Rusak</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="treeview-menu">
                                <li class="treeview">
                                    <a href="#"><i class="fa fa-sign-out"></i> <span>Out</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li><a href="<?= site_url('superadmin/tr_pengiriman'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengiriman</a></li>
                                        <li><a href="<?= site_url('superadmin/tr_retur'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-print"></i> <span>Cetak Nota</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= site_url('laporan/lapKoreksi'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Koreksi Barang</a></li>
                            <li><a href="<?= site_url('laporan/lapPengadaan'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengadaan</a></li>
                            <li><a href="<?= site_url('laporan/lapPengiriman'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengiriman</a></li>
                            <li><a href="<?= site_url('laporan/lapReturRusak'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur Rusak</a></li>
                            <li><a href="<?= site_url('laporan/lapPengembalianReturRusak '); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengembalian Retur Rusak</a></li>
                            <li><a href="<?= site_url('laporan/lapReturHarga'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur Harga</a></li>
                            <li><a href="<?= site_url('laporan/lapPengembalianReturHarga'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengembalian Retur Harga</a></li>
                            
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#"><i class="fa fa-print"></i> <span>Laporan</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= site_url('laporan/koreksi'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Koreksi</a></li>
                            <li><a href="<?= site_url('laporan/mutasi'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Mutasi</a></li>
                            <li><a href="<?= site_url('laporan/stock'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Stock</a></li>
                            <li><a href="<?= site_url('laporan'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengadaan</a></li>
                            <li><a href="<?= site_url('laporan/pengiriman'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Pengiriman</a></li>
                            <li><a href="<?= site_url('laporan/retur'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Retur</a></li>
							<li><a href="<?= site_url('laporan/lapAntarToko'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Kirim Antar Toko</a></li>
							<li><a href="<?= site_url('laporan/penjualan'); ?>"><i class="fa fa-check" aria-hidden="true"></i>Penjualan</a></li>
						</ul>
                    </li>

                    </ul>
                    <!-- /.sidebar-menu -->
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <?php
                if (!empty($css_files)) {
                    foreach ($css_files as $file):
                        ?>
                        <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
                        <?php
                    endforeach;
                }
                ?>
                <?php
                if (!empty($js_files)) {
                    foreach ($js_files as $file):
                        ?>
                        <script src="<?php echo $file; ?>"></script>
                        <?php
                    endforeach;
                }
                else {
                    ?>
    <!--<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/grocery_crud/themes/bootstrap/css/bootstrap/bootstrap.min.css') ?>" />--> 
                    <script src="<?php echo base_url('assets/grocery_crud/js/jquery-1.11.1.min.js') ?>"></script>
                    <script src="<?php echo base_url('assets/template') ?>/bootstrap/js/bootstrap.min.js"></script>
                    <?php
                }
                ?>
                <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/template/css/styleku.css') ?>" />
                <?php
                $this->load->view('pluggins/notif');
                ?>
                <!-- AdminLTE App -->
                <script src="<?php echo base_url('assets/template/emodal') ?>/emodal.min.js" type="text/javascript"></script>
                <script src="<?php echo base_url('assets/template') ?>/dist/js/app.js"></script>

                <!-- Main content -->
                <section class="content">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-text-width"></i> <?php echo @$ket_header ?> </h3>        
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse">  <i class="fa fa-minus"></i></button>
                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?php echo @$output; ?> 
                            <?php echo @$customFunction; ?> 
                        </div>
                    </div>
                    <!-- Your Page Content Here -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Main Footer -->
            <footer class="main-footer">
                <!-- To the right -->
                <div class="pull-right hidden-xs">
                    Anything you want
                </div>
                <!-- Default to the left -->
                <strong>Copyright &copy; 2016 <a href="#">X-METRIK</a>.</strong> All rights reserved.
            </footer>
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->
        <script>
                                    function emodal(urlmodal, titlemodal)
                                    {
                                        var options = {
                                            url: urlmodal,
                                            title: titlemodal,
                                            loading: true,
                                            size: 'lg'
                                        };
                                        eModal.iframe(options);
                                    }
        </script>
        <style>
            .gc-container{
                width:100% !important;
            };
        </style>
    </body>
</html>
