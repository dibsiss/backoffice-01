<?php 
$this->load->view('pluggins/bootstrap');
$this->load->view('pluggins/combo');
?>

<div class="container" style="margin-top:20px">
<div class="alert alert-success">
  Koreksi ! Stok <strong><?php echo $namaBarang=@$barangs->nama ?></strong>
</div>
<?php if(validation_errors()){ ?>
<div class="alert alert-danger">
	<strong> Terjadi Kesalahan ...!</strong>
	<?php echo @validation_errors(); ?>
</div>
<?php } ?>
	 <form method="post" action="<?php echo site_url('umum/simpanKoreksi') ?>">
	  <input type=hidden name=id_barang value="<?php echo @$id_barang ?>">
	  <div class="form-group">
		<label for="email">Nama Barang:</label>
		<input type="text" value="<?php echo $namaBarang ?>" disabled class="form-control">
	  </div>
	  <div class="form-group">
		<label for="email">Stok Barang:</label>
		<input type="text" value="<?php echo $stokBarang=@$barangs->stok ?>" disabled class="form-control">
	  </div>
	  
	  <?php if(!empty($barang)){
	  if($barangs->kategori != 'HANDPHONE') {
	 } 
	 ?>
	  <input type="hidden" name=jenis value=0>
	  <input type=hidden name=stok value="<?php echo @$stokBarang ?>">
	  <div class="form-group">
		<label for="pwd">Koreksi Stok:</label>
		<input type="number" min="1" max="<?php echo $stokBarang  ?>"   class="form-control" name="koreksi[]">
	  </div>
	  <?php
	  }else{
	  ?>
	  <input type="hidden" name=jenis value=1>
	  <div class="form-group">
		<label for="pwd">Koreksi Stok:</label><br>
		<select data-placeholder="Pilih IMey" name="koreksi[]" class="chosen-select" multiple="" style="width:100%;height:30px">
              <option value=""></option>
              <?php
				foreach($imeys as $im){
					?>
						<option value="<?php echo $imeyHp=$im->imey ?>"><?php echo $imeyHp ?></option>
					<?php
				}
			  ?>
            </select>
	  </div>
	  
	  <?php } ?>
	  <div class="form-group">
		<label for="pwd">Keterangan:</label>
		 <textarea class="form-control" name="keterangan" rows="3"></textarea>
	  </div>
	  <button type="Simpan" class="btn btn-success">Submit</button>
	</form> 
	
</div>

