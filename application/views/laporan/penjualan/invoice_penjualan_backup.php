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
            $penanggungJawab = $this->db->get_where('data_user',array('id_user'=>$header->id_user))->row()->fullname;
            $idTrans = $header->idh_penjualan;
            $tgl_input = $header->tgl;
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
						<div class="col-xs-6 col-sm-6 col-md-6">
							 <div class="form-group">
								<label for="email">Nama Toko:</label>
								<div><?php echo @$namaToko ?></div>
							  </div>
							  <div class="form-group">
								<label for="email">Alamat Toko:</label>
								<div><?php echo @$alamatToko ?></div>
							  </div>
							  <div class="form-group">
								<label for="email">Telp Toko:</label>
								<div><?php echo @$telpToko ?></div>
							  </div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<label for="email">Id Trans:</label>
								<div><?php echo @$idTrans ?></div>
							  </div>
							  <div class="form-group">
								<label for="email">Tanggal:</label>
								<div><?php echo @$tgl_input ?></div>
							  </div>
							  <div class="form-group">
								<label for="email">Kasir Toko:</label>
								<div><?php echo @$penanggungJawab ?></div>
							  </div>
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
										$isHp = $this->master->isHp($de->id_barang);
										$getImeys = $this->db->get_where('imey_penjualan', array('idd_penjualan' => $de->idd_penjualan))->result();
										$imeyTampil = $this->general->listImey($getImeys, 'imey');
										$jumlah = count($getImeys);
                                       
                                        ?>
                                        <div class="row">
                                            <div class="col-xs-1 col-sm-1 col-md-1"><?php echo $no++; ?></div>
											<div class="col-xs-3 col-sm-3 col-md-3"><?php echo @$detailBarang->nama ?></div>
                                            <div class="col-xs-2 col-sm-2 col-md-2"><?php echo $jumlah ?></div>
                                            <div class="col-xs-2 col-sm-2 col-md-2"><?php echo $hargaBarang=$de->harga ?></div>
                                            <div class="col-xs-3 col-sm-3 col-md-3"><?php echo $subtotal = $jumlah*$hargaBarang?></div>
                                        </div>
                                        <?php
										$totalBayar = $totalBayar+$subtotal;
                                        if ($isHp['is_hp'] == 1) {
                                            ?>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-12 col-md-22 text-center"><?php echo @$imeyTampil ?></div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?><br>
									<div class="row text-bold">
                                            <div class="col-xs-1 col-sm-1 col-md-1">&nbsp;</div>
											<div class="col-xs-3 col-sm-3 col-md-3">&nbsp;</div>
                                            <div class="col-xs-2 col-sm-2 col-md-2">&nbsp;</div>
                                            <div class="col-xs-2 col-sm-2 col-md-2">Total Bayar</div>
                                            <div class="col-xs-3 col-sm-3 col-md-3"><?php echo $totalBayar?></div>
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