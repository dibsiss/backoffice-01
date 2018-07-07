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
            text: "Data Pengiriman Anda Yang Belum Disimpan Akan di Hapus",
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
                        $.post("<?php echo site_url(); ?>/umum/truncatePengiriman/", {}, function (obj)
                        {
                            location.reload();
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
	
    function insertPengiriman() {
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menyimpan Transaksi.. ? , Pastikan Anda Telah Memilih Barang Untuk di Kirim",
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
                        var datastring = $("#frm-pengiriman").serialize();
                        $.post("<?php echo site_url(); ?>/umum/insertPengiriman/", datastring, function (obj)
                        {
                            if (obj.success == 1) {
								var url = '<?php echo site_url('laporan/invoicePengiriman/') ?>'+obj.id_pengiriman;
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
            text: "Anda Akan Menghapus Barang Terpilih Dari Daftar Pengiriman",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Ya',
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        if(datastring){
                        $('#loading').css('display', 'inline');
                        $.post("<?php echo site_url(); ?>/umum/deleteDetailPengiriman/", datastring, function (obj)
                        {
                            showTable(obj);
                            $('#loading').css('display', 'none');
                        });
                    }
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }

    function insertHeaderPengiriman() {
        var datastring = $("#frm-pengiriman").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertHeaderPengiriman') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                showTable(obj.idh_pengiriman);
                $('#idh_pengiriman').val(obj.idh_pengiriman);
                disableInput();
//                $('#keterangan').ckeditorGet().config.readOnly = true;
                $(".gagal").css("display", "none");
                $("#listBarang").css("display", "inline");
                $("#simpanPengiriman").css("display", "inline");
                $("#simpanHeader").css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showTable(nota) {
        $.post("<?php echo site_url('umum/showDetailPengiriman') ?>/" + nota, function (result) {
            $("#tabelTemp").html(result);
        });
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('idd_pengiriman[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function disableInput() {
        $("#id_tujuan").prop('disabled', true).trigger("chosen:updated");
        $("#id_pengiriman").prop('disabled', true);
//        $('#keterangan').ckeditorGet().config.readOnly = true;
    }
</script>
<?php
//inisialisasi session
$sess_idh_pengiriman = $this->session->userdata('idh_pengiriman');
$sess_id_tujuan = $this->session->userdata('id_tujuan');
$sess_keterangan = $this->session->userdata('keterangan');
if (!empty($sess_idh_pengiriman)) {
    ?>
    <script>
        $(document).ready(function () {
            $("#simpanHeader").css("display", "none");
            $("#simpanPengiriman").css("display", "inline");
//            $('#keterangan').ckeditorGet().config.readOnly = true;
            showTable("<?php echo $sess_idh_pengiriman ?>");
            $("#listBarang").css("display", "inline");
        });
    </script>
    <?php
}
?>

<!-- test -->
<div class="row">
    <form id="frm-pengiriman" action="" method="post">

        <input type="hidden" name="idh_pengiriman" id="idh_pengiriman" value="<?php echo @$sess_idh_pengiriman ?>">

        <div class="col-md-12">
            <div class="form-group">
                <label>Tujuan :</label>
                <select id='id_tujuan'  name='id_tujuan' class='chosen-select' data-placeholder='Pilih Tujuan' style="width: 100% !important" <?php echo (!empty($sess_id_tujuan)) ? "disabled" : ""; ?>>
                    <?php
                    echo cmb_pengiriman();
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
            <button type="button" class="btn btn-success" onclick="insertHeaderPengiriman()" id="simpanHeader">Simpan Header</button>
            <button type="button" class="btn btn-success" onclick="showBarang()" style="display:none" id="listBarang">List Barang</button> 
            <button type="button" class="btn btn-success" onclick="insertPengiriman()" style="display:none" id="simpanPengiriman">Simpan Transaksi</button>
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
        emodal("<?php echo site_url('umum/listbarangPengiriman') ?>", "List Barang");
    }
    $(function () {
        $(document).on('hide.bs.modal', '.modal', function (event) {
            $('#keterangan').ckeditorGet().config.readOnly = true;
            var idh_pengiriman = $('#idh_pengiriman').val();
            showTable(idh_pengiriman);
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