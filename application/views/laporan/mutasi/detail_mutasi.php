<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>Tanggal
        <th>ID Transaksi
        <th>Barang       
        <th>Stok Awal  
		<th>Masuk         
		<th>Keluar         
        <th>Stok Akhir         
      <!--   <th>Harga  -->        
        <th>Tempat          
    </tr>
    <?php
    if (!empty($header)) {
        foreach ($header as $h) {
			$jenisTrans = ucwords(@$h->jenis_trans);
			?>
			<tr>
				<td><?php echo @$h->tgl ?>
				<td><?php echo @$h->idh_transaksi ?>
				<td><?php echo $h->id_barang ?>
				<td><?php 
				if($jenisTrans=='Reject'){
					$stokAwal = 'Reject';
				}else{
					$stokAwal=$h->sisa_stok;
				}
				echo $stokAwal; ?>
				<td><?php 
				if($jenisTrans=='Masuk'){
					$jumlahMasuk = $h->jumlah;
				}else if($jenisTrans=='Reject'){
					$jumlahMasuk = 'Reject';
				}else{
					$jumlahMasuk = 0;
				}
				echo $jumlahMasuk;
				// echo ($jenisTrans=='Masuk') ? $jumlah=$h->jumlah :($jenisTrans=='reject')?'reject' : '0' 
				?>
				<td><?php
				if($jenisTrans=='Keluar' || $jenisTrans=='Reject'){
					$jumlahKeluar =$h->jumlah;
				}else{
					$jumlahKeluar = 0;
				}
				echo $jumlahKeluar;
				?>
				<td><?php
					$stokAkhir=$stokAwal+$jumlahMasuk;
					if($jenisTrans=='Keluar'){
						$stokAkhir = $stokAwal-$jumlahKeluar;
					}else if($jenisTrans=='Reject'){
						$stokAkhir = 'Reject';
					}
					echo $stokAkhir;
				?>
					<!--	<td><?php echo $h->harga ?>-->
				<td><?php $getTempat = $this->db->get_where('data_sumber',array('id'=>$h->id_tempat))->row();
						echo (empty($getTempat))? 'Superadmin' : ucwords(@$getTempat->gudang.' '.@$getTempat->nama);
				?>
			</tr>
			<?php
        }
        ?>
        <?php
    } else {
        echo "<tr><td colspan='9' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>