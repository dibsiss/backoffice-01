<?php 
foreach($pengaturan as $p){
	@$aturan[$p->nama] = $p->value.'<br>';
}
//echo $aturan['barcode'];
?>
<h3 class="text-center"><i class="fa fa-user"></i> Selamat Datang</h3> 
<hr>
<div class="alert alert-success">
	<h4>Pengaturan Aplikasi</h4>
</div>
<hr>
<div class="row" style="margin-bottom:300px">
	<div class="col-md-6">
		<div class="box box-primary">
		<div class="box-header with-border">
                  <i class="fa fa-warning"></i>
                  <h3 class="box-title">Print Barcode</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                       <div class="radio" >
                            <label>
                                <input onclick="changeBarcode($(this).val())" name="jenis_barang" id="radio-default" value="0" <?php echo ($aturan['barcode']==0)? 'checked' : '' ?> type="radio">Imey
                            </label>
                            <label>
                                <input onclick="changeBarcode($(this).val())" name="jenis_barang" <?php echo ($aturan['barcode']==1)? 'checked' : '' ?> value="1" type="radio">Id Barang
                            </label>
                        </div>
                    </div>
					<div class="loading-barcode" style="display:none">
						<?php $this->load->view('pluggins/loading_default_show') ?>
					</div>
                </div>
            </div>
	</div>
</div>
<script>
var site_url = '<?php echo site_url() ?>';
function changeBarcode(val){
	$(".loading-barcode").css("display","block");
	$.post( site_url+"/superadmin/changePrintBarcode/"+val, function( data ) {
	  $(".loading-barcode").css("display","none");
	});
}
</script>