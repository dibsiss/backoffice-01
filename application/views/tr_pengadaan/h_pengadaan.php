<?php $this->load->view('pluggins/combo'); ?>
<?php $this->load->view('pluggins/autocomplete'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable') ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<script>
    function tambahBaru() {
        swal({
            title: "Yakin ?",
            text: "Data Keranjang Belanja Anda Akan Dihapus Semua",
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
                        $.post("<?php echo site_url(); ?>/umum/truncatePengadaan/", {}, function (obj)
                        {
                            location.reload();
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }

    function insertPengadaan() {
        swal({
            title: "Yakin ?",
            text: "Anda Akan Memasukkan Transaksi",
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
                        $.post("<?php echo site_url(); ?>/umum/insertPengadaan/", {}, function (obj)
                        {
							if(obj != false){
                            showPrint(obj);
							}else{
								$('#loading').css('display', 'none');
								$(".gagal").css("display", "block");
								$("#pesan_gagal").html("<p><b>Terjadi Kesalahan</b></p><p> Silahkan Masukkan Barang Dalam Keranjang Belanja Terlebih Dahulu</p>");
							}
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function showPrint(idpengadaan) {
    var url = '<?php echo site_url('laporan/invoicePengadaan/') ?>'+idpengadaan;
        swal({
            title: "Cetak Nota",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            closeOnCancel: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.open(url, '_blank');
                    } 
                    location.reload();
                });
    }
    function deleteTemp() {
        var datastring = $("#tblTemp").serialize();
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menghapus Keranjang Belanja Terpilih",
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
                        $.post("<?php echo site_url(); ?>/umum/deleteTempPengadaan/", datastring, function (obj)
                        {
                            showTable(obj);
                            $('#loading').css('display', 'none');
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function ubahKeranjang() {
        var datastring = $("#frm-pengadaan").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/updateKeranjang') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                showTable(obj.no_nota);
                disableInput();
                clearInput();
                $("#barang_pengadaan").prop('disabled', false);
                $(".gagal").css("display", "none");
                $("#imey").css("display", "none");
                $('#idh_temp').val("");
                $('#btn-update').css("display", "none");
                $('#btn-insert').css("display", "inline");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function editTemp() {
        var datastring = $("#tblTemp").serialize();
        swal({
            title: "Yakin ?",
            text: "Anda Akan Mengubah Keranjang Belanja Terpilih",
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
                        $.post("<?php echo site_url(); ?>/umum/editTempPengadaan/", datastring, function (obj)
                        {
                            $('#loading').css('display', 'none');
                            result = eval('(' + obj + ')');
                            if (result.status == 1) {
                                $('#kodeBrg').val(result.id_barang);
                                $('#id_barang').val(result.id_barang);
                                $('#namaBrg').val(result.nama);
                                $('#harga_beli').val(result.harga_beli);
                                $('#jumlah').val(result.jumlah);
                                $('#barang_pengadaan').val(result.nama);
                                $("#barang_pengadaan").prop('disabled', true);
                                $('#hargaBrg').val(result.harga_jual);
                                $('#idh_temp').val(result.idh_temp);
                                $('#btn-update').css("display", "inline");
                                $('#btn-insert').css("display", "none");
                                $("#harga_beli_r").val(result.harga_diskon);
                                $("#number_picker").val(result.diskon);
                                $('#potongan').val(result.potongan);
                                if (result.is_hp == 1) {
                                    $("#imey").css("display", "inline");
                                    $(".dinamis").val("");
                                    $(".copyan").remove();
                                    $.each(result.imey, function (i, item) {
                                        inputDinamis();
                                        $('.dinamis:last').val(item);
                                    });
                                } else {
                                    $("#imey").css("display", "none");
                                }
                            } else {
                                swal("Error", "Pilihan Tidak Boleh Lebih Dari Satu", "error");
                            }
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });

    }
    function insertKeranjang() {
        var datastring = $("#frm-pengadaan").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertKeranjang') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $("#tombolRefund").css("display", "none");
                $('#loading').css('display', 'none');
                $('#idh_temp').val(obj.no_nota);
                showTable(obj.no_nota);
                disableInput();
                clearInput();
                $(".copyan").remove();
                $(".gagal").css("display", "none");
                $("#imey").css("display", "none");
                $("#simpanTransaksi").css("display", "inline");
                //menghilangkan Tabel Retur
                $("#idh_retur").val(obj.idh_retur);
                $("#tombolRetur").css("display", "none");
                $('#tabelRetur').css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showTable(nota) {
        $.post("<?php echo site_url('umum/showTempPengadaan') ?>/" + nota, function (result) {
            $("#tabelTemp").html(result);
        });
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('idh_temp[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function disableInput() {
        $("#id_supliyer").prop('disabled', true).trigger("chosen:updated");
        $("#no_nota").prop('disabled', true);
        $("#tgl_nota").prop('disabled', true);
        $("#tgl_tempo").prop('disabled', true);
    }
    function clearInput() {
        $("#barang_pengadaan").val("");
        $("#id_barang").val("");
        $("#harga_beli").val("");
        $("#jumlah").val("");
        $("#kodeBrg").val("");
        $("#namaBrg").val("");
        $("#hargaBrg").val("");
        $(".dinamis").val("");
        $("#harga_beli_r").val("0");
        $("#number_picker").val('0');
        $("#potongan").val('0');
    }
</script>
<?php
//inisialisasi session
$sess_no_nota = $this->session->userdata('no_nota');
$sess_tgl_nota = $this->session->userdata('tgl_nota');
$sess_tgl_tempo = $this->session->userdata('tgl_tempo');
$sess_id_suplier = $this->session->userdata('id_supplier');
$sess_idh_retur_pengadaan = $this->session->userdata('idh_retur_pengadaan');
if (!empty($sess_no_nota)) {
    ?>
    <script>
        $(document).ready(function () {
            $("#simpanTransaksi").css("display", "inline");
            showTable("<?php echo $this->session->userdata('no_nota') ?>");
        });
    </script>
    <?php
}
?>
<div class="row">
    <form id="frm-pengadaan" action="<?php echo site_url('umum/test') ?>" method="post">
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
        <input type="hidden" name="idh_temp" id="idh_temp" value="<?php echo @$sess_no_nota; ?>">

        <div class="col-md-4">
            <div class="form-group">
                <label>Supliyer :</label>
                <select name="id_supliyer" id="id_supliyer" class='chosen-select' data-placeholder='Pilih Supliyer' style='width:100%' <?php echo (!empty($sess_id_suplier)) ? "disabled" : ""; ?>>
                    <?php echo cmb_suppliyer(); ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pwd">No Nota</label>
                <input type="text" name="no_nota" class="form-control" id="no_nota" <?php echo (!empty($sess_no_nota)) ? "disabled value='$sess_no_nota'" : '' ?>>
            </div>
            <div class="form-group">
                <label for="pwd">Tanggal Nota</label>
                <input type="text" name="tgl_nota" class="form-control" <?php echo (!empty($sess_tgl_nota)) ? "disabled value='$sess_tgl_nota'" : '' ?> id="tgl_nota">
            </div>
            <div class="form-group">
                <label for="pwd">Tanggal Jatuh Tempo</label>
                <input type="text" name="tgl_tempo" class="form-control" <?php echo (!empty($sess_tgl_tempo)) ? "disabled value='$sess_tgl_tempo'" : '' ?> id="tgl_tempo">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="sel1">Barang :</label>
                <input id="barang_pengadaan" type="text" class="form-control" placeholder="Masukkan Minimal 4 huruf" autocomplete="off" />
                <input type=hidden name=id_barang id=id_barang>
            </div>

            <div class="form-group">
                <label for="pwd">Harga Beli</label>
                <input type="number" name="harga_beli" onchange="copyTo($(this).val());" id="harga_beli" class="form-control">
            </div>
            <div class="form-group">
                <label for="pwd">Jumlah Beli</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control">
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="pwd">Diskon</label>
                        <input id="number_picker" onchange="disk($(this).val())" class="form-control" name="diskon" type="number" value="0" min="0" max="100" style="display:inline !important;width: 80%;" />%
                    </div>
                    <div class="col-md-6">
                        <label for="pwd">Potongan</label>
                        <input type="number" id="potongan" onchange="potong($(this).val())" value="0" min="0" name="potongan" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="pwd">Kode Barang</label>
                <input id="kodeBrg" type="text" class="form-control" readonly >
            </div>

            <div class="form-group">
                <label for="pwd">Nama Barang</label>
                <input type="text" id="namaBrg" class="form-control"  readonly>
            </div>
            <div class="form-group">
                <label for="pwd">Harga</label>
                <input type="number" name="harga_beli_r" id="harga_beli_r" class="form-control" style="height: 110px;font-size: 30px" value="0" readonly>
            </div>

            <!-- tempat untuk modal -->
        </div>
</div>
<div class="row">
    <div class="col-md-12">
        <button type="button" class="btn btn-success" id="btn-insert" onclick="insertKeranjang()">Masuk Keranjang</button>
        <button type="button" class="btn btn-success" id="btn-update" onclick="ubahKeranjang()" style="display: none">Ubah Keranjang</button>
        <button type="button" class="btn btn-success" onclick="insertPengadaan()" id="simpanTransaksi" style="display: none">Simpan Transaksi</button>
        <button type="button" id="imey" class="btn btn-primary" data-toggle="modal" data-target="#myModal" style="display:none !important">Tambahkan Imey</button>
        <button type="button" class="btn btn-danger" onclick="tambahBaru()">Cancel</button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div id="tabelRetur" style="display:none"></div>
    </div>
</div>
<?php $this->load->view('pluggins/loading') ?>
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div id="tabelTemp"></div>
    </div>
</div>
</form>
<script>
    function inputDinamis() {
        var html = $(".copy").html();
        $(".after-add-more:last").after(html);
        $('.dinamis:last').parent(".after-add-more").addClass("copyan");
        $('.dinamis:last').focus();
        return false;
    }

    function disk(harga) {
        var hargaDiskon;
        var hargaAsli;
        var total;
        hargaAsli = $('#harga_beli').val();
        if (hargaAsli.length > 0) {
            hargaDiskon = hargaAsli * (harga / 100);
            total = hargaAsli - hargaDiskon;
            $('#harga_beli_r').val(total);
            $('#potongan').val(0);
        }
    }
    function potong(harga) {
        var hargaDiskon;
        var hargaAsli;
        var total;
        hargaAsli = $('#harga_beli').val();
        if (hargaAsli.length > 0) {
            total = hargaAsli - harga;
            $('#harga_beli_r').val(total);
            //set diskon
            $('#number_picker').val(0);
        }
    }

    function copyTo(harga) {
        var totalDiskon = $('#number_picker').val();
        var totalPotongan = $('#potongan').val();
        if (totalPotongan.length > 0) {
            potong(totalPotongan);
        } else if (totalDiskon.length > 0) {
            disk(totalDiskon);
        } else {
            $('#harga_beli_r').val(harga);
        }
    }
    $(function () {
        $(document).on('show.bs.modal', '.modal', function (event) {
            $('#awal_dinamis').focus();
        });
        $(document).on('hide.bs.modal', '.modal', function (event) {
            var id = $('#idh_temp').val(); //idh temp sudah diisi dengan no nota
            showTable(id);
        });

        $(".add-more").click(function () {
            inputDinamis();
        });

        $(document).on('keyup', '.dinamis', function (e) {
            if (e.keyCode == 13)
            {
                inputDinamis();
                e.preventDefault();
                return false;
            }
        });
        $("body").on("click", ".remove", function () {
            $(this).parents(".control-group").remove();
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