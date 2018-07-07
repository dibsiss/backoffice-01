<html>
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/bootstrap/css/bootstrap.css') ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.css') ?>"/>
    <script src="<?php echo base_url('assets/plugins/datatable_serverside/js/jquery-1.11.2.min.js') ?>" ></script>
    <script src="<?php echo base_url() ?>/assets/template/bootstrap/js/bootstrap.min.js"></script>
    <?php $this->load->view('pluggins/combo') ?>
    <?php
    $idh_retur = $this->uri->segment(3);
    $is_hp = $this->uri->segment(5);
    $id_barang = $this->uri->segment(4);
    ?>
    <hr>
    <div class="container">
        <div class="alert alert-info">
            <h3>List Imey</h3>
        </div>

        <form id="barangBaru" action="" method="post" >
            <input type="hidden" name="idh_retur" value="<?php echo $idh_retur ?>">
            <input type="hidden" name="is_hp" id="is_hp" value="<?php echo $is_hp ?>">
            <input type="hidden" name="id_barang" value="<?php echo $id_barang ?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="email">Pilih Barang Penganti:</label>
                        <select onchange="showTipeGanti($(this).val(), '<?php echo $is_hp ?>')" name="pilihan" class="form-control chosen-select" id="pilihan">
                            <option value=""></option>
                            <option value="sejenis">Barang Sejenis</option>
                            <option value="lain_jenis">Barang Lain Jenis</option>
                            <option value="uang">Uang</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="tipeGanti"></div><hr>
            <?php $this->load->view('pluggins/loading') ?>
        </form>
        <hr>
        <div class="row">
            <div class="col-lg-12">
                <div id="tabelRetur"></div>
            </div>
        </div>
    </div>
</html>

<script>
    function showTipeGanti(id, isHp) {
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url(); ?>/umum/showTipeGanti/" + id + "/" + isHp, function (obj)
        {
            $('#loading').css('display', 'none');
            $("#tipeGanti").html(obj);
        });
    }
    $(function () {
        showTable();
    });
    function showTable() {
        $.post("<?php echo site_url(); ?>/umum/showTableReturRusak/<?php echo $idh_retur ?>/<?php echo $id_barang ?>/<?php echo $is_hp ?>", function (obj)
                {
                    $('#loading').css('display', 'none');
                    $("#tabelRetur").html(obj);
                });
            }
            function hapusImeyReturRusak(id) {
                $('#loading').css('display', 'inline');
                $.post("<?php echo site_url(); ?>/umum/deleteRecordReturRusak/" + id, function (obj)
                {
                    $('#loading').css('display', 'none');
                    showTable();
                });
            }
            function resetPilihan(){
                $("#imey").val("");
                $("#harga").val("");
                $("#nominal").val("");
                $('#pilihan').val('').trigger('chosen:updated');
                $('#combo_id_barang').val('').trigger('chosen:updated');
            }
            function insertBaru() {
                $('#loading').css('display', 'inline');
                var datastring = $("#barangBaru").serialize();
                $.post("<?php echo site_url('umum/editTempReturRusak') ?>", datastring, function (result) {
                    $('#loading').css('display', 'none');
                    obj = eval('(' + result + ')');
                    if (obj.success == 1) {
                        resetPilihan();
                        showTable();
                        $(".gagal").css("display", "none");
                    } else {
                        $(".gagal").css("display", "block");
                        $("#pesan_gagal").html(obj.message);
                    }
                });
            }
            function selectIdBarang(id){
                $('#loading').css('display', 'inline');
                $.post("<?php echo site_url('umum/showIsianLainJenis') ?>/"+id, function (result) {
                    $('#loading').css('display', 'none');
                    obj = eval('(' + result + ')');
                    $("#isian_lain_jenis").html(obj.tampilan);
                    $('#is_hp').val(obj.is_hp);
                });
            }
</script>