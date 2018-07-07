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
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <label>Status Kepemilikan:</label>
                        <select name="status_milik" id="status_milik" onchange="getGudang($(this).val())" class="chosen-select-deselect" data-placeholder='Pilih Supliyer' style='width:100%'>
                            <?php echo cmb_stock() ?>
                        </select>
                    </div>
					<div id="cmb_gudang"></div>
					<div id="cmb_toko"></div>
                </div>
            </div>
        </div>
		<div class="col-md-4">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <label>Jenis Barang :</label>
                        <div class="radio" >
                            <label>
                                <input name="jenis_barang" id="radio-default" value="semua" checked type="radio">Semua
                            </label>
                            <label>
                                <input name="jenis_barang" value="hp" type="radio">Hp
                            </label>
                            <label>
                                <input name="jenis_barang" value="nonhp" type="radio">Non Hp
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Id Barang:</label>
                        <input type="text" name="id_barang" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-primary">
                <!-- <div class="box-header">
                       <h3 class="box-title">Date picker</h3>
                     </div>
                -->
                <div class="box-body">
                    <!-- Date range -->
                    <div class="form-group">
                        <label>IMey :</label>
                        <input type="text" name="imey" class="form-control">
                    </div><!-- /.form group -->    
                    <div class="form-group">
                        <label>Supliyer:</label>
                        <select name="id_supliyer" id="id_supliyer" class="chosen-select-deselect" data-placeholder='Pilih Supliyer' style='width:100%'>
                            <?php
                            echo cmb_suppliyer();
                            ?>
                        </select>
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

function getGudang(id){
	$.post("<?php echo site_url(); ?>/laporan/getGudang/"+id, '', function (result)
        {
            $('#cmb_gudang').html(result);
        });
}
function getToko(id){
			$.post("<?php echo site_url(); ?>/laporan/getToko/"+id,'', function (result)
        {
            $('#cmb_toko').html(result);
        });
		}
    function cari() {
        $('#loading').css('display', 'inline');
        var datastring = $('#frm-laporan').serialize();
        $.post("<?php echo site_url(); ?>/laporan/stockRentang/", datastring, function (result)
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
