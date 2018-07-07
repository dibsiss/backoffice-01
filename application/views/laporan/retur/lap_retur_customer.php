<?php
$this->load->view('pluggins/combo_multi');
$this->load->view('pluggins/daterange');
$this->load->view('pluggins/print_element');
?>
<script>
    var site_urlku = "<?php echo site_url() ?>";
</script>
<div class="alert alert-info">
    <h4>Filter Pencarian</h4>
</div>
<form id="frm-laporan" action="" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <label>Rentang Tanggal:</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name=tanggal_rentang class="form-control pull-right daterange"/>
                        </div><!-- /.input group -->
                    </div><!-- /.form group -->   
                </div>
            </div>
        </div>
        
		<div class="col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    <!-- Date range -->
                    <div class="form-group">
                        <label>ID Transaksi:</label>
                        <input type=text name=id_transaksi class="form-control">
                    </div><!-- /.form group -->    

                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
</form>
<p style="text-align:right">
    <button type="button" class="btn btn-primary" id="reset">Reset</button>
    <button type="button" download="ganteng" class="btn btn-primary" id="excel">excel</button>
    <button type="button" class="btn btn-primary" id="print">Print</button>
    <button type="button" class="btn btn-primary" id="cari">Pencarian</button>
</p>

<?php $this->load->view('pluggins/loading') ?>
<div id="toPrint">
    <div id=hasil_lap></div>
</div>

<script type="text/javascript">
    function cari() {
        $('#loading').css('display', 'inline');
        var datastring = $('#frm-laporan').serialize();
        $.post("<?php echo site_url(); ?>/laporan/prosesReturCustomer/", datastring, function (result)
        {
            $('#loading').css('display', 'none');
            $('#hasil_lap').html(result);
        });
    }
    $(document).ready(function () {
        $("#print").click(function () {
            $('#toPrint').printElement();
        });
    });
    $("#excel").click(function (e) {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#toPrint').html()));
        e.preventDefault();
    });
    $("#cari").click(function (e) {
        cari();
    });
    var config = {
        '.chosen-select-deselect': {
            allow_single_deselect: true, width: '100% !important'
        }
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
    $("#reset").click(function () {
        $('.chosen-select-deselect').val('').trigger('chosen:updated');
        $('input:text').val('');
        $(".daterange").val('');
        $("#radio-default").prop("checked",true);
    });
</script>
