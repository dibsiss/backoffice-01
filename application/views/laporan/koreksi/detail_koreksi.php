<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>No
        <th>Id Transaksi
        <th>ID Barang
        <th>Nama Barang
        <th>Keterangan
        <th>Penanggung Jawab
        <th>Jumlah
        <th>Harga Beli
        <th>Tanggal        
    </tr>
    <?php
    if (!empty($header)) {
		$no=1;
		$subTotal = 0;
        foreach ($header as $h) {
			$idtr_koreksi = $h->idtr_koreksi;
			$getBarangDetail = $this->db->get_where('mst_barang_detail',array('id_barang'=>$id_barang=$h->id_barang))->row();
			$getUserDetail = $this->db->get_where('data_user',array('id_user'=>$id_user=$h->id_user))->row();
			$getDetails = $this->db->get_where('imey_koreksi',array('idtr_koreksi'=>$idtr_koreksi))->result();
			$getHargaHistory=$this->db->get_where('history_mutasi',array('idh_transaksi'=>$idtr_koreksi))->row();
			?>
            <tr>
                <td><?php echo $no++ ?>
                <td><?php echo $idtr_koreksi?>
                <td><?php echo $id_barang?>
                <td><?php echo $getBarangDetail->nama?>
                <td><?php echo $h->keterangan?>
                <td><?php echo $getUserDetail->fullname?>
                <td><?php echo $jumlah=count(@$getDetails)?>
                <td><?php $harga=@$getHargaHistory->harga; echo $this->general->formatRupiah($harga);?>
                <td><?php echo $h->tgl;
				$subTotal= $subTotal + ($jumlah*$harga);
				?>
            </tr>
            <?php
			if($getBarangDetail->nama_category=='HANDPHONE'){
				?>
				<tr>
					<td colspan="8"><center><?php echo $this->general->listImey($getDetails,'imey') ?></center>
				</tr>
				<?php
			}
        }
        ?>
	<tr>
		<td>&nbsp;</td>
        <td colspan=6><b>Total</b></td>
        <td colspan=3><?php echo "Rp. ".$this->general->formatRupiah($subTotal) ?></td>
    </tr>
      
        <?php
    } else {
        echo "<tr><td colspan='8' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>