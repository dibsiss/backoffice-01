<?php $this->load->view('pluggins/print_element'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php $this->load->view('pluggins/print_font') ?>
<div class="container">
<form method="post" id="frm-laporan">
	<div class="row">
		<div class="col-md-6">
					<div class="box box-primary">
						<div class="box-body">
							<!-- Date range -->
							<div class="col-md-8 col-lg-8 col-xs-8">
								<div class="form-group">
									<label>Pilih Tanggal:</label>
									<input class="form-control" type=date name=tgl id="tgl_setoran">
								</div><!-- /.form group -->    
							</div>
							<div class="col-md-4 col-xs-4 col-lg-4">
							<div class="form-group">
									<label>&nbsp;</label>
									<button class="btn btn-primary btn-block" id="cariLaporan">Submit</button>
								</div><!-- /.form group -->  
							</div>
						</div><!-- /.box-body -->
					</div>
				</div>
	</div>
</div>
</form>
<?php $this->load->view('pluggins/loading') ?>
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12">
			<div id=hasil_lap></div>
		</div>
	</div>
</div><!-- milik container -->
<script>
    $("#cariLaporan").click(function (e) {
        $('#loading').css('display', 'inline');
        var datastring = $('#frm-laporan').serialize();
        $.post("<?php echo site_url(); ?>/laporan/prosesSetoranKasir/", datastring, function (result)
        {
			obj = eval('(' + result + ')');
			if (obj.sukses == 1) {
				$(".gagal").css("display", "none");
				$('#loading').css('display', 'none');
				$('#hasil_lap').html(obj.invoice);
			}else{
				$('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.pesan);
			}
            
        });
        e.preventDefault();
    });
</script>