<?php $this->load->view('pluggins/input_token'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php $this->load->view('pluggins/combo'); ?>
<?php $this->load->view('pluggins/modal_bootstrap') ?>
<script>
    $(function () {
        tokenAuto();
    });
    function tokenAuto() {
        $("#id_retur").tokenInput("<?php echo site_url('umum/getReturCustomer'); ?>", {
            headerku: "<div class='row alert alert-success'>\n\
                        <div class=col-md-3>Id Retur</div>\n\
                        <div class=col-md-3>ID Barang</div>\n\
                        <div class=col-md-3>Imey</div>\n\
                        <div class=col-md-3>Tanggal</div>\n\
                       </div>",
            theme: "facebook",
            tokenLimit: 1,
            resultsFormatter: function (item) {
                return "<li>"
                        + "<div class=row>"
                        + "<div class=col-md-3>" + item.id + "</div>"
                        + "<div class=col-md-3>" + item.name + "</div>"
                        + "<div class=col-md-3>" + item.imey + "</div>"
                        + "<div class=col-md-3>" + item.tgl + "</div>"
                        + "</div>";
                +"</li>";
            },
            onAdd: function (item) {
                $('#loading').css('display', 'inline');
                showDetail(item.id);
            },
            onDelete: function (item) {

            }
        });
    }
    function showDetail(id) {
        $.post("<?php echo site_url(); ?>/umum/showReturCustomer/" + id, function (obj)
        {
            $('#loading').css('display', 'none');
            $("#tabelRetur").html(obj);
        });
    }
</script>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="pwd">Id Penjualan / Imey</label>
            <input type="text" id="id_retur" name="id_retur" class="form-control">
        </div>
    </div>
</div>
<hr>
<?php $this->load->view('pluggins/loading') ?>
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div id="tabelRetur"></div>
    </div>
</div>
<script>
    function prosesTransaksi(id) {
        $('#loading').css('display', 'inline');
        swal({
            title: "Yakin ?",
            text: "Anda Akan Melakukan Retur?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Ya',
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        $.post("<?php echo site_url(); ?>/umum/showTempReturCustomer/" + id, function (obj)
                        {
                            $('#loading').css('display', 'none');
                            $("#tabelRetur").html(obj);
                             $('.gagal').css('display', 'none');
							
                        });
                    } else {
                        $('#loading').css('display', 'none');
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
        //batas
    }
    
    function showImey(idh_penjualan, id_barang, is_hp) {
        emodal("<?php echo site_url('umum/showImeyReturCustomer/') ?>" + idh_penjualan + "/" + id_barang + "/" + is_hp, "List Imey");
    }
    function truncateRetur(id) {
        swal({
            title: "Yakin ?",
            text: "Anda Akan Menghapus Transaksi",
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
                        $.post("<?php echo site_url(); ?>/umum/deleteTempReturCustomer/" + id, function (obj)
                        {
                            $('#loading').css('display', 'none');
                            result = eval('(' + obj + ')');
                            if (result.sukses) {
                                showDetail('kosong');
                                resetToken();
                            } else {
                                $("#pesanEror").html(result.pesan);
                            }
                        });
                    } else {
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
    }
    function resetToken() {
        $(".token-input-list-facebook").remove();
        $(".token-input-dropdown-facebook").remove();
        tokenAuto();
    }
</script>