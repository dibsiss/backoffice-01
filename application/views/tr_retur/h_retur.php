<?php $this->load->view('pluggins/combo'); ?>
<?php
$ckeditorSetting = array('toolbar' => 'Basic', 'needJquery' => false);
$this->load->view('pluggins/textarea', $ckeditorSetting);
?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable') ?>
<?php $this->load->view('pluggins/modal_bootstrap') ?>
<script>
    function tambahBaru() {
        swal({
            title: "Yakin ?",
            text: "Data Retur Anda Yang Belum Disimpan Akan di Hapus",
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
                        $.post("<?php echo site_url(); ?>/umum/truncateRetur/", {}, function (obj)
                        {
                            location.reload();
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function insertRetur() {
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menyimpan Transaksi, Pastikan Anda Telah Memilih Barang Untuk di Retur",
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
                        $.post("<?php echo site_url(); ?>/umum/insertRetur/", {}, function (result)
                        {
                            obj = eval('(' + result + ')');
                            if (obj.success == 1) {
                                var url = '<?php echo site_url('laporan/invoiceRetur/') ?>'+obj.idh_retur;
								$('#urlNota').prop('href',url);
								$('#modal-bootstrap').modal('show');
                            } else {
                                $('#loading').css('display', 'none');
                                swal({
                                    title: "Gagal !",
                                    text: obj.message,
                                    html: true
                                });
                            }
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }

    function deleteTemp() {
        var datastring = $("#tblTemp").serialize();
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menghapus Retur Terpilih Dari Daftar Retur",
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
                        if (datastring) {
                            $.post("<?php echo site_url(); ?>/umum/deleteDetailRetur/", datastring, function (obj)
                            {
                                showTable(obj);
                            });
                        }
                        $('#loading').css('display', 'none');
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }


    function insertHeaderRetur() {
        var datastring = $("#frm-retur").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertHeaderRetur') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                showTable(obj.idh_retur);
                $('#idh_retur').val(obj.idh_retur);
                $('#id_retur_hidd').val(obj.id_retur);
                disableInput();
                $(".gagal").css("display", "none");
                $("#imey").css("display", "none");
                $("#listBarang").css("display", "inline");
                $("#simpanRetur").css("display", "inline");
                $("#simpanHeader").css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showTable(nota) {
        $.post("<?php echo site_url('umum/showDetailRetur') ?>/" + nota, function (result) {
            $("#tabelTemp").html(result);
        });
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('idd_retur[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function disableInput() {
        $("#id_supliyer").prop('disabled', true).trigger("chosen:updated");
        $("#id_retur").prop('disabled', true).trigger("chosen:updated");
        $("#id_retur").prop('disabled', true);
        $('#keterangan').ckeditorGet().config.readOnly = true;
    }
</script>
<?php
//inisialisasi session
$sess_id_retur = $this->session->userdata('id_retur');
$sess_idh_retur = $this->session->userdata('idh_retur');
$sess_id_suplier = $this->session->userdata('id_supplier');
$sess_keterangan = $this->session->userdata('keterangan');
if (!empty($sess_id_retur)) {
    ?>
    <script>
        $(document).ready(function () {
            $("#simpanRetur").css("display", "inline");
            $("#simpanHeader").css("display", "none");
            $('#keterangan').ckeditorGet().config.readOnly = true;
            showTable("<?php echo $sess_idh_retur ?>");
    <?php echo (!empty($sess_id_retur)) ? '$("#listBarang").css("display", "inline");' : '' ?>
        });
    </script>
    <?php
}
?>
<div class="row">
    <form id="frm-retur" action="" method="post">
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog" >
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Imey</h4>
                    </div>
                    <div class="modal-body">
                        <!--hidden-->
                        <div class="copy hide">
                            <div class="control-group input-group after-add-more" style="margin-top:10px">
                                <input type="text" name="imey[]" class="form-control dinamis" placeholder="Enter Imey Here">
                                <div class="input-group-btn"> 
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                        <!--hidden-->
                        <!--content body-->
                        <div class="input-group control-group after-add-more">
                            <input type="text" name="imey[]" id="awal_dinamis" class="form-control dinamis" placeholder="Enter Imey Here">
                            <div class="input-group-btn"> 
                                <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                            </div>
                        </div>
                        <!--content body-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- tempat untuk modal -->
        <input type="hidden" name="idh_retur" id="idh_retur" value="<?php echo @$sess_idh_retur ?>">
        <input type="hidden" name="id_retur_hidd" id="id_retur_hidd" value="<?php echo @$sess_id_retur ?>">

        <div class="col-md-12">
            <div class="form-group">
                <label>Supliyer :</label>
                <select name="id_supliyer" id="id_supliyer" class='chosen-select' data-placeholder='Pilih Supliyer' style='width:100%' <?php echo (!empty($sess_id_suplier)) ? "disabled" : ""; ?>>
                    <?php
                    echo cmb_suppliyer();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pwd">Jenis Retur</label>
                <select name="id_retur" id="id_retur" class='chosen-select' data-placeholder='Pilih Jenis Retur' style='width:100%' <?php echo (!empty($sess_id_retur)) ? "disabled" : ""; ?>>
                    <?php
                    echo cmb_jenis_retur();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pwd">Keterangan</label>
                <textarea id='keterangan' rows="10" cols="10" name='keterangan' class='texteditor' ><?php echo (!empty($sess_keterangan)) ? $sess_keterangan : '' ?></textarea>
            </div>
        </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <button type="button" class="btn btn-success" onclick="insertHeaderRetur()" id="simpanHeader">Simpan Header</button>
            <button type="button" class="btn btn-success" onclick="showBarang()" style="display:none" id="listBarang">List Barang</button> 
            <button type="button" class="btn btn-success" onclick="insertRetur()" style="display:none" id="simpanRetur">Simpan Transaksi</button>
            <button type="button" class="btn btn-danger" onclick="tambahBaru()" >Cancel</button>
        </div>
    </div>
</div>
</form>
<br>
<?php $this->load->view('pluggins/loading') ?>
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div id="tabelTemp"></div>
        <?php //$this->load->view('tr_pengadaan/table_pengadaan');   ?>
    </div>
</div>
<script>
    function showBarang() {
        var id_supliyer = $('#id_supliyer').val();
        emodal("<?php echo site_url('umum/listbarang/') ?>" + id_supliyer, "List Barang");
    }
    $(function () {
        $(document).on('hide.bs.modal', '.modal', function (event) {
            $('#keterangan').ckeditorGet().config.readOnly = true;
            var idh_retur = $('#idh_retur').val();
            showTable(idh_retur);
        });
        //prevent enter to submit
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>