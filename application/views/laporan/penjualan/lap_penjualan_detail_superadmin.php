<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>Keterangan Header
        <th>ID Barang
        <th>Nama Barang
        <th>Jenis          
        <th>Jumlah / No Pelanggan          
        <th>Harga Beli         
        <th>Harga Jual         
        <th>Margin         
        </tr>
    <?php
	
    if (!empty($header)) {
        foreach ($header as $h) {
            ?>
            <tr>
                <td>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            ID Transaksi :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_penjualan ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Tanggal :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->tgl ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Customer :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php 
							$customer=@$this->master->getById('mst_customer', 'id_customer', @$h->id_customer)->nama; 
							echo (!empty($customer)) ? $customer : 'Anonim';
							?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Penanggung Jawab :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$this->master->getById('data_user', 'id_user', @$h->id_user)->fullname ?>
                        </div>
                    </div>
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
               </tr>
            <?php
			
		   if ($jenis_barang == 'hp') {
			   $this->db->where("b.nama_category", "HANDPHONE");
		   } else if ($jenis_barang == 'nonhp') {
			   $this->db->where("b.nama_category !=", "HANDPHONE");
		   }
		   
		   if(($fisik =='fisik') || ($fisik == 'semua')){
            $getDetails = $this->db->select("a.*, b.nama")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_penjualan a', array('a.idh_penjualan' => $h->idh_penjualan))->result();
		   }
			//tampilkan jika jenis barang bukan hp
			if($jenis_barang!='hp'){
				//jika statusnya tidak fisik maka tampilkan
				if($fisik == 'elektrik' || $fisik == 'semua'){
					$getDetailsNonFisik = $this->db->select("a.*, b.nama,c.nama as jenis_barang")->group_by('a.id_barang')->join('mst_jenis_non_fisik c', "on c.id_jenis_non_fisik = a.id_jenis_non_fisik ")->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_penjualan_non_fisik a', array('a.idh_penjualan' => $h->idh_penjualan))->result();
				}
			}
			
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
					//mendapatkan harga beli dari mst stok yang yang sesuai record is_retur adalah idh penjualan
					$getHargaBeli = @$this->db->get_where('mst_stok',array('id_toko'=>$h->id_toko,'id_barang'=>$gd->id_barang,'is_retur'=>$h->idh_penjualan))->row();
                    $hargaBeli = @$getHargaBeli->harga_beli;
					//end harga beli => fungsinya untuk mengetahui keuntungan yang didapat
					$isHp = $this->master->isHp($gd->id_barang);
					$jumlah_barang = $gd->jumlah;
                    if ($isHp['is_hp'] == 1) {
                        $getImeys = $this->db->get_where('imey_penjualan', array('idd_penjualan' => $gd->idd_penjualan))->result();
                        $imeyTampil = $this->general->listImey($getImeys, 'imey');
					}
                    ?>
                    <tr>
                        <td>&nbsp;
                        <td><?php echo @$gd->id_barang ?>
                        <td><?php echo @$gd->nama ?>
                        <td>&nbsp;
                        <td><?php echo @$jumlah_barang ?>
                        <td><?php echo 'Rp.'. $this->general->formatRupiah(@$hargaBeli);
						?>
                        <td><?php $hargaJual=@$gd->harga;
							echo 'Rp.'.$this->general->formatRupiah(@$hargaJual);
						?>
                        <td><?php $margin=$hargaJual - $hargaBeli;
							echo 'Rp.'.$this->general->formatRupiah(@$margin);
						?>
					</tr>
                    <?php
                    if ($isHp['is_hp'] == 1) {
                        ?>
                        <tr>
                            <td colspan="9" class="text-center text-bold"><?php echo @$imeyTampil ?>
                        </tr>
                        <?php
                    }
                }
            }
			if(!empty($getDetailsNonFisik)){
				foreach($getDetailsNonFisik as $nf){
					?>
					<tr>
                        <td>&nbsp;
                        <td><?php echo @$nf->id_barang ?>
                        <td><?php echo @$nf->nama ?>
                        <td><?php echo @$nf->jenis_barang ?>
                        <td><?php echo @$nf->nomer ?>
						<td><?php echo 0 ?>
                        <td><?php echo 'Rp.'.$this->general->formatRupiah(@$nf->harga) ?>
                        <td><?php echo 'Rp.'.$this->general->formatRupiah(@$nf->harga) ?>
					</tr>
					<?php
				}
			}
        }
        ?>
        <tr>
             <th>Keterangan Header
			<th>ID Barang
			<th>Nama Barang
			<th>Jenis          
			<th>Jumlah / No Pelanggan          
			<th>Harga Beli
			<th>Harga Jual
			<th>Margin
		</tr>
        <?php
    } else {
        echo "<tr><td colspan='6' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>