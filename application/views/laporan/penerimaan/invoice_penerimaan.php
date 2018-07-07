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
            $penanggungJawab = $this->tr->getPenanggungjawab($header->id_user)['penanggungJawab'];
            $idTrans = $header->idh_pengadaan;
            $no_nota = $header->no_nota;
            $tgl_input = $header->tgl_input;
            $tgl_nota = $header->tgl_nota;
            $tgl_tempo = $header->tgl_jatuhtempo;
            $supliyer = $this->master->getById('mst_supliyer', 'id_supliyer', $header->id_supliyer)->nama;
//        $this->general->testPre($header);
        }
        if (!empty($detail)) {
            $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_pengadaan a', array('a.idh_pengadaan' => $idTrans))->result();
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
                                        <dt>Tanggal Jatuh Tempo : </dt>
                                        <dd><?php echo @$tgl_tempo ?>.</dd>
                                    </dl>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <dl>
                                        <dt>No Nota :</dt>
                                        <dd> <?php echo @$no_nota ?>.</dd>
                                        <dt>Supliyer :</dt>
                                        <dd> <?php echo @$supliyer ?>.</dd>

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
                                        <th>Harga Beli</th>
                                        <th>Diskon</th>
                                        <th>Potongan</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($details as $de) {
                                        //getBarang
                                        $jumlah = $de->jumlah;
                                        $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                        $isHp = $this->master->isHp($de->id_barang);
                                        if ($isHp['is_hp'] == 1) {
                                            $getImeys = $this->db->get_where('imey_pengadaan', array('idd_pengadaan' => $de->idd_pengadaan))->result();
                                            $imeyTampil = $this->general->listImey($getImeys, 'imey');
                                        } 
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?>
                                            <td><?php echo $de->id_barang ?>
                                            <td><?php echo @$detailBarang->nama ?></td>
                                            <td><?php echo @$de->harga_beli; ?>
                                            <td><?php echo @$de->diskon; ?>
                                            <td><?php echo @$de->potongan; ?>
                                            <td><?php echo @$de->harga_diskon; ?>
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
                                        <th>Harga Beli</th>
                                        <th>Diskon</th>
                                        <th>Potongan</th>
                                        <th>Harga</th>
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