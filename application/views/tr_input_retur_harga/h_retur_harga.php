<?php $this->load->view('pluggins/input_token'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php $this->load->view('pluggins/combo'); ?>
<?php $this->load->view('pluggins/modal_bootstrap') ?>

<script>
    $(function () {
        tokenAuto();
    });
    function tokenAuto() {
        $("#id_retur").tokenInput("<?php echo site_url('umum/getReturHarga'); ?>", {
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
        $.post("<?php echo site_url(); ?>/umum/showReturHarga/" + id, function (obj)
        {
            $('#loading').css('display', 'none');
            $("#tabelRetur").html(obj);
        });
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