<?php $this->load->view('pluggins/bootstrap') ?>
<?php $this->load->view('pluggins/print_element') ?>
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
            $idTrans = $header->idh_retur_rusak;
            $tgl_input = $header->tgl_nota;
            $tgl_nota = $header->tgl_nota;
			$idHeaderProsesRetur = @$header->idh_retur;
			$sumbers = @$this->db->get_where('data_sumber',array('id'=>$header->id_supliyer))->row()->nama;
			(empty($sumbers))? $sumber = 'Superadmin' : $sumber=$sumbers;
            $supliyer = @$this->db->get_where('data_sumber',array('id'=>$this->owner))->row()->nama;
			(empty($supliyer))?$supliyer = 'Superadmin' : $supliyer=$supliyer;
//        $this->general->testPre($header);
        }
			$details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $idHeaderProsesRetur))->result();
             if (!empty($details)) {
			?>
            <div class="box box-success box-solid" id="box-<?php echo $idTrans ?>">
                <div class="box-header with-border">
                    <h4 class="box-title"># <?php echo $idTrans ?> </h4>        
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><?php echo @$tgl_input ?>  <i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                <div class="box-body" style="display: block;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Keterangan</h3>
                            </div><!-- /.box-header -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>

                                        <dt>Tanggal Nota : </dt>
                                        <dd><?php echo @$tgl_nota ?>.</dd>
                                        <dt>Jenis Retur : </dt>
                                        <dd>Retur Rusak.</dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>
                                       <dt>Sumber :</dt>
                                        <dd> <?php echo @$sumber ?>.</dd>
										<dt>Tujuan :</dt>
                                        <dd> <?php echo @$supliyer ?>.</dd>

                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>
                                        <dt>Tanggal Input : </dt>
                                        <dd><?php echo @$tgl_input ?>.</dd>
										<dt>Id Retur Rusak : </dt>
                                        <dd><?php echo @$header->idh_retur ?>.</dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="table_client" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Id Barang</th>
                                        <th>Barang</th>
										<th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($details as $de) {
										$jumlah = $de->jumlah;
                                        $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                        $isHp = $this->master->isHp($de->id_barang);
                                        if ($isHp['is_hp'] == 1) {
                                            $getImeys = $this->db->get_where('d_retur', array('idh_retur' => $idHeaderProsesRetur,'id_barang'=>$de->id_barang))->result();
                                            $imeyTampil = $this->general->listImey($getImeys, 'imey');
											$jumlah = $de->jumlah_hp;
                                        } 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?>
                                            <td><?php echo $de->id_barang ?>
                                            <td><?php echo @$detailBarang->nama ?></td>
                                            <td><?php echo $jumlah ?></td>
                                        </tr>
                                        <?php
                                        if ($isHp['is_hp'] == 1) {
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center"><?php echo @$imeyTampil ?>
                                            </tr>
                                            <?php
                                        }
										/*
										 $detailsKembali = $this->db->select("a.*")->group_by('a.id_barang')->get_where('d_retur_rusak a', array('a.id_barang'=>$de->id_barang,'a.idh_retur_rusak' => $idTrans))->result();
                                        //getBarang
										$detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                        $isHp = $this->master->isHp($de->id_barang);
										$getJumlah = $this->db->get_where('d_retur', array('idh_retur' => $idHeaderProsesRetur,'id_barang'=>$de->id_barang))->result();
										$getImeys = $this->db->select("a.*,count(*) as jumlah")->group_by('a.id_barang_kembalian')->get_where('imey_retur_rusak a', array('a.idd_retur_rusak' => $de->idd_retur_rusak))->result();
                                        
										// $this->general->testPre($getImeys);
										*/
                                         $detailsKembali = $this->db->select("a.*")->group_by('a.id_barang')->get_where('d_retur_rusak a', array('a.id_barang'=>$de->id_barang,'a.idh_retur_rusak' => $idTrans))->row();
										 $getImeys = $this->db->select("a.*,count(*) as jumlah")->group_by('a.id_barang_kembalian')->get_where('imey_retur_rusak a', array('a.idd_retur_rusak' => $detailsKembali->idd_retur_rusak))->result();
                                       
										$idbarangKembali = 0;
										// $this->general->testPre($getImeys);
										foreach($getImeys as $gi){
											$detailBarangKembali = $this->master->getDetailBarang("where a.id_barang= '$gi->id_barang_kembalian'")->row();
											$isHp2 = $this->master->isHp($gi->id_barang_kembalian);
											$getImeys2 = $this->db->select("a.*,count(*) as jumlah")->get_where('imey_retur_rusak a', array('id_barang_kembalian'=>$gi->id_barang_kembalian,'a.idd_retur_rusak' => $detailsKembali->idd_retur_rusak))->result();
											?>
											<tr>
												<td>&nbsp;
												<td><?php echo $gi->id_barang_kembalian?>
												<td><?php echo @$detailBarangKembali->nama ?></td>
												<td><?php echo count($getImeys2) ?></td>
											</tr>
											<?php
										 ?>
                                        <?php
										
                                        if ($isHp2['is_hp'] == 1) {
											 $imeyGantiTampil = $this->general->listImey($getImeys2, 'imey');
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center"><?php echo @$imeyGantiTampil ?>
                                            </tr>
                                            <?php
											}
											//untuk jika uang
											if ($gi->jenis_kembalian == 'uang') {
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Rp. <?php echo $this->general->formatRupiah(@$gi->imey); ?>
                                            </tr>
                                            <?php
											}
										}
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Id Barang</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        
                                    </tr>
                                </tfoot>
                            </table>
                        </div><!-- /.col -->
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <h4>Mengetahui</h4><br><br><br>( ....................................... )
                        </div>
                        <div class="col-xs-4 text-center">
                            <h4>Penerima</h4><br><br><br>( ....................................... )
                        </div>
                        <div class="col-xs-4 text-center">
                            <h4>Pengirim</h4><br><br><br>( ....................................... )
                        </div>
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