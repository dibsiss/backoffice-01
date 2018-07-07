<?php $this->load->view('pluggins/input_token'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable') ?>
<?php $this->load->view('pluggins/table-edit') ?>
<?php $this->load->view('pluggins/modal_bootstrap') ?>
<?php
$this->load->view('pluggins/combo_multi');
$sess_idh_penjualan = $this->session->userdata('idh_penjualan');
$sess_id_customer = $this->session->userdata('id_customer');
?>
<!-- test -->
<ul class="nav nav-tabs nav-justified">
    <li class="active"><a data-toggle="tab" href="#fisik">Fisik</a></li>
    <li><a data-toggle="tab" href="#nonFisik">Non Fisik</a></li>
</ul>
<div class="tab-content" style="margin-top:30px">
    <div id="fisik" class="tab-pane active">
        <div class="row">
            <form id="frm-penjualan" action="" method="post">
                <input type="hidden" name="imey" id="imey">
                <input type="hidden" name="harga_jual" id="harga_jual">
                <input type="hidden" name="idh_penjualan" id="idh_penjualan" value="">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pwd">Barang</label>
                        <input type="text" id="id_barang" name="id_barang" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pwd">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" min="0" value="0" max="" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pwd">Customer</label>
                        <select name="id_customer" id="id_customer" class="chosen-select-deselect" data-placeholder='Pilih Customer' style='width:100%' <?php echo (!empty($sess_idh_penjualan)) ? "disabled" : ""; ?>>
                            <?php
                            echo cmb_customer();
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pwd">Diskon</label>
                        <input type="number" name="diskon" id="disk" min="0" max="" value="0" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pwd">Potongan</label>
                        <input type="number" name="potongan" id="potong" value="0" class="form-control">
                    </div>
                </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    
                    <button type="button" class="btn btn-google" onclick="tambahBaru()" id="simpanHeader">Cancel</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <input type="text" id="captionHarga" class="text-center form-control" readonly="" style="height: 70px;font-size: 30px" value="">
            </div>
        </div>
        </form>
    </div>
    <!--untuk nono fisik-->
    <div id="nonFisik" class="tab-pane" >
        <div class="row">
            <form id="frm-penjualan-non-fisik" action="" method="post">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pwd">Barang</label>
                        <input type="text" id="id_barang_non_fisik" name="id_barang_non_fisik" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pwd">Customer</label>
                        <select name="id_customer2" id="id_customer2" class="chosen-select-deselect2" data-placeholder='Pilih Customer' style='width:100%' <?php echo (!empty($sess_idh_penjualan)) ? "disabled" : ""; ?>>
                            <?php
                            echo cmb_customer();
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pwd">Jenis Non Fisik</label>
                        <select name="jenis_non_fisik" id="jenis_non_fisik" class="chosen-select-deselect3" data-placeholder='Pilih Customer' style='width:100%'>
                            <?php
                            echo cmb_jenis_non_fisik();
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pwd">Nomer</label>
                        <input type="text" id="nomer" name="nomer" class="form-control">
                        <input type="hidden" min="1" id="harga_non_fisik" name="harga_non_fisik" class="form-control">
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <!-- <button type="button" class="btn btn-primary" onclick="insertPenjualan()" >Simpan</button> -->
                    <button type="button" class="btn btn-google" onclick="tambahBaru()" id="simpanHeader">Cancel</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <input type="text" id="captionHarga2" class="text-center form-control" readonly="" style="height: 70px;font-size: 30px" value="">
            </div>
        </div>
    </div>
</div>   
<br>
<?php $this->load->view('pluggins/loading') ?>
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div id="tabelTemp"></div>
    </div>
</div>

<?php
if (!empty($sess_idh_penjualan)) {
    ?>
    <script>
        $(document).ready(function () {
            //batas bawah
            showTable("<?php echo @$sess_idh_penjualan ?>");
            $("#listBarang").css("display", "inline");
        });
    </script>
    <?php
}
?>
<script>
    function insertKeranjangNonFisik(params) {
        datastring = $("#frm-penjualan-non-fisik").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertPenjualanNonFisik') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                mariInsert2();
                showTable(obj.idh_penjualan);
                $('#idh_penjualan').val(obj.idh_penjualan);
                $(".gagal").css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    var config = {
        '.chosen-select-deselect': {
            allow_single_deselect: true, width: '100% !important'
        }, '.chosen-select-deselect2': {
            allow_single_deselect: true, width: '100% !important'
        }, '.chosen-select-deselect3': {
            allow_single_deselect: true, width: '100% !important'
        }
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }

    function tambahBaru() {
        swal({
            title: "Yakin ?",
            text: "Data Penjualan Anda Yang Belum Disimpan Akan di Hapus",
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
                        $.post("<?php echo site_url(); ?>/umum/truncatePenjualan/", {}, function (obj)
                        {
                            location.reload();
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function insertPenjualan() {
		var datastring = $("#bayarKembalian").serialize();
		swal({
            title: "Yakin ?",
            text: "Anda Akan Menyimpan Transaksi.. ? , Pastikan Anda Telah Mememilih Barang",
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
						
                        $.post("<?php echo site_url(); ?>/umum/simpanPenjualan/", datastring, function (result)
                        {
                            obj = eval('(' + result + ')');
                            if (obj.success == 1) {
                                // location.reload();
								var url = '<?php echo site_url('laporan/invoicePenjualan/') ?>'+obj.id_penjualan;
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

    function deleteKeranjang() {
        var datastring = $("#tblPenjualan").serialize();
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menghapus Barang Terpilih Dari Daftar Penjualan",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Ya',
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        if (datastring) {
                            $('#loading').css('display', 'inline');
                            $.post("<?php echo site_url(); ?>/umum/deleteDetailPenjualan/", datastring, function (obj)
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


    function showTable(nota) {
        $.post("<?php echo site_url('umum/showTablePenjualan') ?>/" + nota, function (result) {
            $("#tabelTemp").html(result);
        });
    }
    function tokenAuto() {
        $("#id_barang").tokenInput("<?php echo site_url('umum/getBarangJual'); ?>", {
            headerku: "<div class='row alert alert-success'><div class=col-md-2>Id Barang</div><div class=col-md-2>Nama Barang</div><div class=col-md-2>Imey</div><div class=col-md-2>Harga Jual</div><div class=col-md-2>Diskon</div><div class=col-md-2>Potongan</div></div>",
            theme: "facebook",
            resultsFormatter: function (item) {
                return "<li>"
                        + "<div class=row>"
                        + "<div class=col-md-2>" + item.id + "</div>"
                        + "<div class=col-md-2>" + item.name + "</div>"
                        + "<div class=col-md-2>" + item.imey + "</div>"
                        + "<div class=col-md-2>" + item.harga_jual + "</div>"
                        + "<div class=col-md-2>" + item.diskon + "</div>"
                        + "<div class=col-md-2>" + item.potongan + "</div>"
                        + "</div>";
                +"</li>";
            },
            tokenLimit: 1,
            onAdd: function (item) {
                if (item.is_hp == 1) {
                    $('#jumlah').prop('max', "1");
                } else {
                    $('#jumlah').prop('max', "1000");
                }
                $("#jumlah").focus();
                $("#jumlah").val(1);
                $("#disk").val(0);
                $("#potong").val(0);
                $('#disk').prop('max', item.diskon);
                $('#potong').prop('max', item.potongan);
                $("#captionHarga").val(item.harga_jual);
                //insertHidden
                $('#imey').val(item.imey);
                $('#harga_jual').val(item.harga_jual);
            },
            onDelete: function (item) {
                clearHidden();
            }
        });
    }
    function tokenAuto2() {
        $("#id_barang_non_fisik").tokenInput("<?php echo site_url('umum/getBarangJualNonFisik'); ?>", {
            headerku: "<div class='row alert alert-success'><div class=col-md-2>Id Barang</div><div class=col-md-4>Nama Barang</div><div class=col-md-2>Harga Jual</div><div class=col-md-2>Diskon</div><div class=col-md-2>Potongan</div></div>",
            theme: "facebook",
            resultsFormatter: function (item) {
                return "<li>"
                        + "<div class=row>"
                        + "<div class=col-md-2>" + item.id + "</div>"
                        + "<div class=col-md-4>" + item.name + "</div>"
                        + "<div class=col-md-2>" + item.harga_jual + "</div>"
                        + "<div class=col-md-2>" + item.diskon + "</div>"
                        + "<div class=col-md-2>" + item.potongan + "</div>"
                        + "</div>";
                +"</li>";
            },
            onAdd: function (item) {
//                $(".chosen-select-deselect3").chosen().trigger("liszt:activate");
                $('.chosen-select-deselect3').trigger('chosen:activate');
                $("#harga_non_fisik").val(item.harga_jual);
                $("#captionHarga2").val("Rp. "+item.harga_jual);
            },
            onDelete: function (item) {
                $("#harga_non_fisik").val("");
            }
        });
    }
    $('.chosen-select-deselect3').on('change', function (evt, params) {
        $('#nomer').focus();
    });
    $(document).on("keypress", "#nomer", function (e) {
        if (e.keyCode == 13)
        {
            insertKeranjangNonFisik();
        }
    });

    $(function () {
        tokenAuto2();
        //token input
        tokenAuto();
        //enter
        $(document).on("change", "#jumlah", function (e) {
            setHarga();
        });
        $(document).on("change", "#disk", function (e) {
            setHarga();
            $("#potong").val(0);

        });
        $(document).on("change", "#potong", function (e) {
            setHarga();
            $("#disk").val(0);
        });
        function setHarga() {
            var token = $("#id_barang").tokenInput("get");
            var harga = token[0].harga_jual;
            var diskon = $("#disk").val();
            var potongan = $("#potong").val();
            var jumlah = $("#jumlah").val();
            var harga_jual = harga * jumlah;
            if (potongan == 0) {
                if (diskon != 0) {
                    var diskonan = harga * (diskon / 100);
                    harga_jual = jumlah * (harga - diskonan);
                }
            } else {
                harga_jual = jumlah * (harga - potongan);
            }
            var caption = "Rp. " + harga_jual;
            $("#captionHarga").val(caption);
        }
        $(document).on("keypress", "#jumlah", function (e) {
            if (e.keyCode == 13)
            {
                var token = $("#id_barang").tokenInput("get");
                setHarga();
                insertKeranjang(token);
            }
        });
        $(document).on("keypress", "#disk", function (e) {
            if (e.keyCode == 13)
            {
                setHarga();
                var token = $("#id_barang").tokenInput("get");
                insertKeranjang(token);
            }
        });
        $(document).on("keypress", "#potong", function (e) {
            if (e.keyCode == 13)
            {
                setHarga();
                var token = $("#id_barang").tokenInput("get");
                insertKeranjang(token);
            }
        });
        $(document).on('hide.bs.modal', '.modal', function (event) {
            showTable(idh_pengiriman);
        });
    });

    function insertKeranjang(params) {
        datastring = $("#frm-penjualan").serialize();
        $('#loading').css('display', 'inline');
        datastring = $("#frm-penjualan").serialize();
        $.post("<?php echo site_url('umum/insertPenjualan') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                showTable(obj.idh_penjualan);
                $('#idh_penjualan').val(obj.idh_penjualan);
                mariInsert();
                $(".gagal").css("display", "none");
            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function mariInsert2(){
        clearHidden();
        setKosong();
        $("#jenis_non_fisik").val("").trigger("chosen:updated");
        $(".chosen-select-deselect").prop('disabled', true).trigger("chosen:updated");
        $(".chosen-select-deselect2").prop('disabled', true).trigger("chosen:updated");
        $("#token-input-id_barang_non_fisik").focus();
        $("#harga_non_fisik").val("");
    }
    function mariInsert() {
        clearHidden();
        setKosong();
        $(".chosen-select-deselect").prop('disabled', true).trigger("chosen:updated");
        $(".chosen-select-deselect2").prop('disabled', true).trigger("chosen:updated");
        $("#token-input-id_barang").focus();
    }
    function clearHidden() {
        $('#imey').val("");
        $('#harga_jual').val("");
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('idd_penjualan[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function setKosong() {
        $("#disk").val(0);
        $("#potong").val(0);
        $("#jumlah").val(0);
        $("#nomer").val("");
        $("#harga_non_fisik").val(0);
        $("#captionHarga").val(0);
        $("#id_barang").tokenInput('clear');
        $(".token-input-list-facebook").remove();
        $(".token-input-dropdown-facebook").remove();
        tokenAuto();
        tokenAuto2();
    }
</script>