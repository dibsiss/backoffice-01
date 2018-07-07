<title>Invoice Reject Pengiriman</title>
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
            $penanggungJawab = $this->db->get_where('data_user',array('id_user'=>@$detailPenerimaan->id_user))->row()->fullname;
            $idTrans = $detailPenerimaan->idh_penerimaan;
            $tgl_input =$tgl_nota= $detailPenerimaan->tgl_input;
			$sumbers = @$this->db->get_where('data_sumber',array('id'=>$header->id_sumber))->row()->nama;
			(empty($sumbers))?$sumber = 'Superadmin' : $sumber=$sumbers;
            $supliyer = $this->db->get_where('data_sumber',array('id'=>$header->id_tujuan))->row()->nama;
			$keterangan = $detailPenerimaan->keterangan;
//        $this->general->testPre($header);
        }
        if (!empty($detail)) {
            $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_pengiriman a', array('a.idh_pengiriman' => $header->idh_pengiriman,'is_trouble'=>1))->result();
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
                                <h3 class="box-title">Keterangan Invoice Reject Barang</h3>
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
                                        <dt>Keterangan : </dt>
                                        <dd><?php echo @$keterangan ?>.</dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>
										<dt>Sumber :</dt>
										<dd> <?php echo @$supliyer ?>.</dd>
										<dt>Tujuan :</dt>
										<dd> <?php echo @$sumber ?>.</dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>
                                        <dt>Penanggung Jawab : </dt>
                                        <dd><?php echo @$penanggungJawab ?>.</dd>
                                        <dt>Tanggal Input : </dt>
                                        <dd><?php echo @$tgl_input ?>.</dd>
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
                                        //getBarang
                                        $jumlah = 0;
                                        $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                        $isHp = $this->master->isHp($de->id_barang);
                                        if ($isHp['is_hp'] == 1) {
                                            $getImeys = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $header->idh_pengiriman,'id_barang'=>$de->id_barang,'is_trouble'=>1))->result();
                                            $imeyTampil = $this->general->listImey($getImeys, 'imey');
											$jumlah = $de->jumlah_hp;
                                        }else{
											$getJumlah = $this->db->get_where('imey_pengiriman',array('idd_pengiriman'=>$de->idd_pengiriman,'is_trouble'=>1))->result();
											$jumlah = count($getJumlah);
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