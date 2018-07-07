<?php $this->load->view('pluggins/combo'); ?>
<?php
$ckeditorSetting = array('toolbar' => 'Basic', 'needJquery' => false);
$this->load->view('pluggins/textarea', $ckeditorSetting);
?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable') ?>
<script>
    function tambahBaru() {
        swal({
            title: "Yakin ?",
            text: "Data Permintaan Anda Yang Belum Disimpan Akan di Hapus",
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
                        $.post("<?php echo site_url(); ?>/umum/truncatePermintaan/", {}, function (obj)
                        {
                            location.reload();
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function insertPermintaan() {
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menyimpan Transaksi.. ? , Pastikan Anda Telah Memasukkan Barang",
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
                        $.post("<?php echo site_url(); ?>/umum/insertPermintaan/", {}, function (result)
                        {
                            obj = eval('(' + result + ')');
                            if (obj.success == 1) {
                                location.reload();
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
            text: "Anda Akan Menghapus Barang Dari List Permintaan?",
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
                        $.post("<?php echo site_url(); ?>/umum/deleteDetailPermintaan/", datastring, function (obj)
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

    function insertHeaderPermintaan() {
        var datastring = $("#frm-permintaan").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertHeaderPermintaan') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                showTable(obj.idh_permintaan);
                $('#idh_permintaan').val(obj.idh_permintaan);
                disableInput();
                $('#keterangan').ckeditorGet().config.readOnly = true;
                $(".gagal").css("display", "none");
                $("#listBarang").css("display", "inline");
                $("#simpanPermintaan").css("display", "inline");
                $("#simpanHeader").css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showTable(nota) {
        $.post("<?php echo site_url('umum/showDetailPermintaan') ?>/" + nota, function (result) {
            $("#tabelTemp").html(result);
        });
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('idd_permintaan[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function disableInput() {
        $("#id_tujuan").prop('disabled', true).trigger("chosen:updated");
        $("#id_permintaan").prop('disabled', true);
        $('#keterangan').ckeditorGet().config.readOnly = true;
    }
</script>
<?php
//inisialisasi session
$sess_idh_permintaan = $this->session->userdata('idh_permintaan');
$sess_id_tujuan = $this->session->userdata('id_tujuan_permintaan');
$sess_keterangan_permintaan = $this->session->userdata('keterangan_permintaan');
if (!empty($sess_idh_permintaan)) {
    ?>
    <script>
        $(document).ready(function () {
            $("#simpanHeader").css("display", "none");
            $("#simpanPermintaan").css("display", "inline");
            $('#keterangan').ckeditorGet().config.readOnly = true;
            showTable("<?php echo $sess_idh_permintaan ?>");
            $("#listBarang").css("display", "inline");
        });
    </script>
    <?php
}
?>

<!-- test -->
<div class="row">
    <form id="frm-permintaan" action="" method="post">

        <input type="hidden" name="idh_permintaan" id="idh_permintaan" value="<?php echo @$sess_idh_permintaan ?>">

        <div class="col-md-12">
            <div class="form-group">
                <label>Tujuan :</label>
                <select id='id_tujuan'  name='id_tujuan' class='chosen-select' data-placeholder='Pilih Tujuan' style="width: 100% !important" <?php echo (!empty($sess_id_tujuan)) ? "disabled" : ""; ?>>
                    <?php
                    echo cmb_permintaan();
                    ?>          
                </select>  
            </div>
            <div class="form-group">
                <label for="pwd">Keterangan</label>
                <textarea id='keterangan' rows="10" cols="10" name='keterangan' class='texteditor' ><?php echo (!empty($sess_keterangan_permintaan)) ? $sess_keterangan_permintaan : '' ?></textarea>
            </div>
        </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <button type="button" class="btn btn-success" onclick="tambahBaru()" >Transaksi Baru</button>
            <button type="button" class="btn btn-success" onclick="insertHeaderPermintaan()" id="simpanHeader">Simpan Header</button>
            <button type="button" class="btn btn-success" onclick="showBarang()" style="display:none" id="listBarang">List Barang</button> 
            <button type="button" class="btn btn-success" onclick="insertPermintaan()" style="display:none" id="simpanPermintaan">Simpan Transaksi</button>
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
        emodal("<?php echo site_url('umum/listbarangPermintaan') ?>", "Transaksi Retur");
    }
    $(function () {
        $(document).on('hide.bs.modal', '.modal', function (event) {
            $('#keterangan').ckeditorGet().config.readOnly = true;
            var idh_permintaan = $('#idh_permintaan').val();
            showTable(idh_permintaan);
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