<?php
$this->load->view('pluggins/combo_multi');
$this->load->view('pluggins/daterange');
$this->load->view('pluggins/print_element');
?>
<script>
    var site_urlku = "<?php echo site_url() ?>";
</script>
<div class="alert alert-info">
    <h4>Header History</h4>
</div>
<form id="frm-laporan" action="" method="post">
    <div class="row">
		<div class="col-md-4">
            <div class="box box-primary">
                <div class="box-body">
                    <!-- Date range -->
                    <div class="form-group">
                        <label>Tempat:</label>
                        <select name="id_tempat" id="id_tempat" class="chosen-select-deselect" data-placeholder='Pilih Tempat' style='width:100%'>
                            <?php
                            echo cmb_antar_toko();
                            ?>
                        </select>
                    </div><!-- /.form group -->    

                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<button type="button" class="btn btn-primary" id="cari">Proses</button>
		</div>
	</div>
</form>

<?php $this->load->view('pluggins/loading') ?>
<div id="toPrint">
    <div id=hasil_lap></div>
</div>

<script type="text/javascript">
    function cari() {
        $('#loading').css('display', 'inline');
        var datastring = $('#frm-laporan').serialize();
        $.post("<?php echo site_url(); ?>/superadmin/prosesMutasiAwal/", datastring, function (result)
        {
            $('#loading').css('display', 'none');
            $('#hasil_lap').html(result);
        });
    }
    
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
</script>
