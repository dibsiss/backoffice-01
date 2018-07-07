<!doctype html>
<html>
    <head>
        <title>List Imey</title>
        
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/bootstrap/css/bootstrap.css') ?>"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.css') ?>"/>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/js/jquery-1.11.2.min.js') ?>" ></script>
        <script src="<?php echo base_url() ?>/assets/template/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/jquery.dataTables.js') ?>" ></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.js') ?>"></script>
<?php $this->load->view('pluggins/alert'); ?>

    </head>
    <body>
        <div class="container">
            <!--<div class="alert alert-info"><h3>List Imey</h3></div>-->
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a data-toggle="tab" href="#nonhp">List Imey</a></li>
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
                                    <th>Imey</th>
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
                tableImey();
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
            function tableImey() {
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
                    ajax: {"url": "<?php echo site_url('umum/getListImey/' . $idh_temp) ?>", "type": "POST"},
                    columns: [
                        {
                            "data": "idd_temp",
                            "orderable": false
                        },
                        {"data": "view"},
                        {"data": "imei"},
                                //sesuai dengan didatabase
                    ],
                    order: [[2, 'asc']],
                    rowCallback: function (row, data, iDisplayIndex) {
                        var info = this.fnPagingInfo();
                        var page = info.iPage;
                        var length = info.iLength;
                        var index = page * length + (iDisplayIndex + 1);
                        $('td:eq(0)', row).html(index);
                    }
                });
            }

            function deleteImey(id, dua) {
                var idh = '<?php echo $idh_temp ?>';
                swal({
                    title: "Yakin ?",
                    text: "Anda Akan Menghapus IMey ini !!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Ya',
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                },
                        function (isConfirm) {
                            if (isConfirm) {
                                $('#loading').css('display', 'inline');
                                $.post("<?php echo site_url(); ?>/umum/deleteDetailTempPengadaan/" + id + "/" + idh, function (obj)
                                {
                                    $("#mytable").DataTable().ajax.reload();
                                    $('#loading').css('display', 'none');
                                });
                            } else {
                                swal("Cancelled", "TerimaKasih", "error");
                            }
                        });

            }
        </script>
    </body>
</html>

