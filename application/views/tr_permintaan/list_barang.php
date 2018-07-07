<!doctype html>
<html>
    <head>
        <title>List Barang</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/bootstrap/css/bootstrap.css') ?>"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.css') ?>"/>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/js/jquery-1.11.2.min.js') ?>" ></script>
        <script src="<?php echo base_url() ?>/assets/template/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/jquery.dataTables.js') ?>" ></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.js') ?>"></script>
        <?php $this->load->view('pluggins/alert'); ?>

    </head>
    <?php 
    $id_tujuan = $this->session->userdata('id_tujuan_permintaan');
    ?>
    <body>
        <div class="container">
            <div class="alert alert-info"><h3>List Barang</h3></div>
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a data-toggle="tab" href="#nonhp">Non HP</a></li>
                <li><a data-toggle="tab" href="#hp">HP</a></li>
            </ul>
            <br>
            <?php $this->load->view('pluggins/loading') ?>
            <div class="tab-content">
                <div id="nonhp" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mytable">
                            <thead>
                                <tr>
                                    <th width="30px">No</th>
                                    <th width="40px">Action</th>
                                    <th>Nama</th>
                                    <th>Stok</th>
                                    <th>Id Barang</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div id="hp" class="tab-pane fade">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mytable2">
                            <thead>
                                <tr>
                                    <th style="width:10px !important">No</th>
                                    <th>Action</th>
                                    <th>Nama</th>
                                    <th>Stok</th>
                                    <th>Id Barang</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                initPertama();
                tableNonHp();
                tableHp();
            });
            function initPertama() {
                $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
                {
                    return {
                        "iStart": oSettings._iDisplayStart,
                        "iEnd": oSettings.fnDisplayEnd(),
                        "iLength": oSettings._iDisplayLength,
                        "iTotal": oSettings.fnRecordsTotal(),
                        "iFilteredTotal": oSettings.fnRecordsDisplay(),
                        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
                    };
                };
            }
            function tableNonHp() {
                var tnon = $("#mytable").dataTable({
                    initComplete: function () {
                        var api = this.api();
                        $('#mytable_filter input')
                                .off('.DT')
                                .on('keyup.DT', function (e) {
                                    if (e.keyCode == 13) {
                                        api.search(this.value).draw();
                                    }
                                });
                    },
                    oLanguage: {
                        sProcessing: "loading..."
                    },
                    processing: true,
                    serverSide: true,
                    ajax: {"url": "<?php echo site_url('umum/getListNonHpPermintaan/0/').$id_tujuan ?>", "type": "POST"},
                    columns: [
                        {
                            "data": "id_barang",
                            "orderable": false
                        },
                        {"data": "view"},
                        {"data": "nama"},
                        {"data": "stock","bSearchable": false},
                        {"data": "id_barang"},
                        //sesuai dengan didatabase
                    ],
                    order: [[4, 'asc']],
                    rowCallback: function (row, data, iDisplayIndex) {
                        var info = this.fnPagingInfo();
                        var page = info.iPage;
                        var length = info.iLength;
                        var index = page * length + (iDisplayIndex + 1);
                        $('td:eq(0)', row).html(index);
                    }
                });
            }
            function tableHp() {
                var thp = $("#mytable2").dataTable({
                    initComplete: function () {
                        var api = this.api();
                        $('#mytable2_filter input')
                                .off('.DT')
                                .on('keyup.DT', function (e) {
                                    if (e.keyCode == 13) {
                                        api.search(this.value).draw();
                                    }
                                });
                    },
                    oLanguage: {
                        sProcessing: "loading..."
                    },
                    processing: true,
                    serverSide: true,
                    ajax: {"url": "<?php echo site_url('umum/getListHpPermintaan/1/').$id_tujuan ?>", "type": "POST"},
                    columns: [
                        {
                            "data": "id_barang",
                            "orderable": false
                        },
                        {"data": "view"},
                        {"data": "nama"},
                        {"data": "stock","bSearchable": false},
                        {"data": "id_barang"},
                        //sesuai dengan didatabase
                        //sesuai dengan didatabase
                    ],
                    order: [[4, 'asc']],
                    rowCallback: function (row, data, iDisplayIndex) {
                        var info = this.fnPagingInfo();
                        var page = info.iPage;
                        var length = info.iLength;
                        var index = page * length + (iDisplayIndex + 1);
                        $('td:eq(0)', row).html(index);
                    }
                });
            }
            function insertPermintaanNonHp(id) {
                swal({
                    title: "Masukkan",
                    text: "Jumlah Barang Permintaan:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    animation: "slide-from-top",
                    inputPlaceholder: "Input Jumlah Barang Pengiriman"
                },
                function (inputValue) {
                    if (inputValue === false)
                        return false;

                    if (inputValue === "") {
                        swal.showInputError("Silahkan Tuliskan Jumlah Barang");
                        return false
                    }

                    $.post("<?php echo site_url(); ?>/umum/insertDetailPermintaanNonHp/" + id + "/" + inputValue, function (result)
                    {
                        obj = eval('(' + result + ')');
                        if (obj.success) {
                            $("#mytable").DataTable().ajax.reload();
                            swal("Berhasil!", obj.message, "success");
                        } else {
                            swal({
                                title: "Error",
                                text: obj.message,
                                html: true
                            });
                        }
                    });
                });
            }
            function insertPermintaanHp(id) {
                swal({
                    title: "Masukkan",
                    text: "Jumlah Barang Permintaan:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    animation: "slide-from-top",
                    inputPlaceholder: "Input Jumlah Barang Pengiriman"
                },
                function (inputValue) {
                    if (inputValue === false)
                        return false;

                    if (inputValue === "") {
                        swal.showInputError("Silahkan Tuliskan Jumlah Barang");
                        return false
                    }

                    $.post("<?php echo site_url(); ?>/umum/insertDetailPermintaanHp/" + id + "/" + inputValue, function (result)
                    {
                        obj = eval('(' + result + ')');
                        if (obj.success) {
                            $("#mytable").DataTable().ajax.reload();
                            swal("Berhasil!", obj.message, "success");
                        } else {
                            swal({
                                title: "Error",
                                text: obj.message,
                                html: true
                            });
                        }
                    });
                });
            }

        </script>
    </body>
</html>

