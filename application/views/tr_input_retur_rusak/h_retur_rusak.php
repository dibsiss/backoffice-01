<?php $this->load->view('pluggins/input_token'); ?>
<?php $this->load->view('pluggins/combo'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<!-- detail ---->
<?php $this->load->view('pluggins/datatable_client') ?>
<?php //$this->load->view('pluggins/alert'); ?>

<!-- detail ---->
<script>
    $(function () {
        tokenAuto();
    });
    function tokenAuto() {
        $("#id_retur").tokenInput("<?php echo site_url('umum/getReturRusak'); ?>", {
            headerku: "<div class='row alert alert-success'>\n\
                        <div class=col-md-4>Id Retur</div>\n\
                        <div class=col-md-4>Supliyer</div>\n\
                        <div class=col-md-4>Tanggal</div>\n\
                       </div>",
            theme: "facebook",
            tokenLimit: 1,
            resultsFormatter: function (item) {
                return "<li>"
                        + "<div class=row>"
                        + "<div class=col-md-4>" + item.id + "</div>"
                        + "<div class=col-md-4>" + item.name + "</div>"
                        + "<div class=col-md-4>" + item.tgl_nota + "</div>"
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
        $.post("<?php echo site_url(); ?>/umum/showReturRusak/" + id, function (obj)
        {
            $('#loading').css('display', 'none');
            $("#tabelRetur").html(obj);
        });
    }

    function showTempReturRusak(id) {
        $('#loading').css('display', 'inline');
        swal({
            title: "Yakin ?",
            text: "Anda Akan Melakukan Pengembalian Retur?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Ya',
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        $.post("<?php echo site_url(); ?>/umum/showTempReturRusak/" + id, function (obj)
                        {
                            $('#loading').css('display', 'none');
                            $("#tabelRetur").html(obj);
                        });
                    } else {
                        $('#loading').css('display', 'none');
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
        //batas
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
                        $.post("<?php echo site_url(); ?>/umum/deleteTempReturRusak/" + id, function (obj)
                        {
                            $('#loading').css('display', 'none');
                            result = eval('(' + obj + ')');
                            if (result.sukses) {
                                showDetail('kosong');
                                resetToken();
                                $('.gagal').css("display","none");
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
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="pwd">Id Retur / Tgl Retur</label>
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