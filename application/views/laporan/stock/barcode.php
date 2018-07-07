<title>Print Barcode</title>
<?php
//get setingan pengaturan yang diprint apakah id barang atau imey
$getPengaturan=$this->db->get_where('pengaturan',array('nama'=>'barcode'))->row();
($getPengaturan->value==0)?$param='imey' : $param = 'id_barang';
?>
<div class="row text-center">
	<h4>Print Barcode Barang</h4>
	<button class="btn btn-primary" id="print"><span class="fa fa-print"></span> Print</button>
	<hr>
		<div id="toPrint">
		<?php
		$this->load->view('pluggins/bootstrap');
		$this->load->view('pluggins/print_element');
			foreach($getData as $gd){
			?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" >
					<img style="border:1px solid black;margin:4px;" src="<?php echo site_url();?>/umum/bikin_barcode/<?php echo $gd->$param;?>">
				</div>
			<?php } ?>
		</div>
</div>
<script>
$(document).ready(function () {
	$("#print").click(function () {
		$('#toPrint').printElement();
	});
});
</script>