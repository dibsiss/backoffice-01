<?php $this->load->view('pluggins/bootstrap') ?>
<?php $this->load->view('pluggins/print_element') ?>
<?php $this->load->view('pluggins/print_font') ?>
<div class="row">
    <div class="col-xs-12 text-center">
        <button type="button" class="btn btn-primary btn-lg text-center" id="simplePrint"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
    </div>
</div>
<div id="toPrint">
    <div class="container" style="margin-top:10px">
        <?php
//print_r($detail);
        if (!empty($header)) {
			$idh_penjualan = $header->idh_penjualan;
            $penanggungJawab = $this->db->get_where('data_user',array('id_user'=>$header->id_user))->row()->fullname;
            $idTrans = $header->idh_retur_customer;
            $notaTrans = $header->no_nota;
            $tgl_input = $header->tgl_input;
			$sumbers = @$this->db->get_where('data_sumber',array('id'=>$header->id_toko))->row();
			(empty($sumbers))?$sumber = 'Superadmin' : $sumber=$sumbers->nama;
			$namaToko = @$sumbers->nama;
			$alamatToko = @$sumbers->alamat;
			$telpToko = @$sumbers->telp;
			
			
		}
        if (!empty($detail)) {
            ?>
            <div class="box box-success box-solid" id="box-<?php echo $idTrans ?>">
                <div class="box-body" style="display: block;">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 text-bold text-center">
								Retur Customer
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
								<?php echo '<b>X-Metrik '.@$namaToko.'</b>' ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo @$alamatToko.' telp ('.@$telpToko.')' ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo '<b>Id Trans Retur</b> '.@$idTrans ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo '<b>Nomer Nota Retur</b> '.@$notaTrans ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo '<b>Id Penjualan</b> '.@$idh_penjualan ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo @$tgl_input ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo '<b>Kasir </b> '.@$penanggungJawab ?>
						</div>
					</div>
					<div class="row text-bold">
						<div class="col-xs-1 col-sm-1 col-md-1">
							No
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3">
							Barang
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2">
							Qty
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2">
							Harga
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3">
							Total
						</div>
					</div>
					<br>
					
					<?php
                                    $no = 1;
									$totalBayar = 0;
                                    foreach ($detail as $de) {
                                        //getBarang
                                 
										$detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
										$isHp = @$this->db->get_where('mst_barang_detail',array('id_barang'=>$de->id_barang))->row();
										$getImeys = @$this->db->get_where('imey_retur_customer', array('idd_retur_customer' => $de->idd_retur_customer))->result();
										$imeyTampil = @$this->general->listImey($getImeys, 'imey');
										$jumlah = count($getImeys);
										//get harga dari penjualan
										if($isHp->jenis=='FISIK'){
											$getHargaArrays = $this->db->get_where('d_penjualan',array('id_barang'=>$de->id_barang,'idh_penjualan'=>$idh_penjualan))->row();
											$hargaBarang = @$getHargaArrays->harga;
										}else{
											$getHargaArrays = $this->db->get_where('d_penjualan_non_fisik',array('id_barang'=>$de->id_barang,'idh_penjualan'=>$idh_penjualan))->row();
											$hargaBarang = @$getHargaArrays->harga;
										}
										?>
                                        <div class="row">
                                            <div class="col-xs-1 col-sm-1 col-md-1"><?php echo $no++; ?></div>
											<div class="col-xs-3 col-sm-3 col-md-3"><?php echo @$detailBarang->nama ?></div>
                                            <div class="col-xs-2 col-sm-2 col-md-2"><?php echo $jumlah ?></div>
                                            <div class="col-xs-2 col-sm-2 col-md-2"><?php echo $hargaBarang ?></div>
                                            <div class="col-xs-3 col-sm-3 col-md-3"><?php echo $subtotal = $jumlah*$hargaBarang?></div>
                                        </div>
                                        <?php
										$totalBayar = $totalBayar+$subtotal;
                                        if ($isHp->nama_category== 'HANDPHONE') {
                                            ?>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-12 col-md-22 text-center"><?php echo @$imeyTampil ?></div>
                                            </div>
                                            <?php
                                        }
									?>
									<!-- untuk keterangan Per item barang yang diretur -->
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-22 text-left"><b>Keterangan</b> <?php echo @$de->keterangan ?></div>
										</div>
									<?php										
                                    }
                                    ?>
									
									<br>
									<div class="row text-bold">
                                            <div class="col-xs-6 col-sm-6 col-md-6">Total Bayar</div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"><?php echo 'Rp. '.$this->general->formatRupiah(@$totalBayar)?></div>
                                    </div>
                                                                        
                </div><!-- /.box-body -->
            </div>

            <?php
        } else {
            ?>
            <section class="invoice">
                <div class="row invoice-info">
                    <div class="alert alert-danger text-center">
                        <h3 class="text-center">Invoice Kosong</h3>
                    </div>
                </div>
            </section>
            <?php
        }
        ?>
    </div>
</div>