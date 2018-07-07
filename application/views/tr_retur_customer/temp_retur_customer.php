<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php
//print_r($detail);
if (!empty($header)) {
    $hasil = $this->tr->getPenanggungJawab($header->id_user);
    $penanggungJawab = @$hasil['penanggungJawab'];
    ?>
    <div class="box box-success box-solid" id="box-<?php echo $no_nota = @$header->idh_penjualan ?>">
        <div class="box-header with-border">
            <h4 class="box-title"># <?php echo $no_nota ?> </h4>        
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><?php echo $tgl_nota = @$header->tgl ?>  <i class="fa fa-minus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body" style="display: block;">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">

                        <div class="box-header with-border">
                            <i class="fa fa-text-width"></i>
                            <h3 class="box-title">Nota Retur Customer</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <dl>
                                        <dt>Tanggal Nota :</dt>
                                        <dd><?php echo $tgl_nota ?>.</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <dl>
                                        <dt>Invoice :</dt>
                                        <dd> <?php echo @$no_nota ?>.</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <dl>
                                        <dt>Penanggung Jawab : </dt>
                                        <dd><?php echo $penanggungJawab ?>.</dd>
                                    </dl>
                                </div>
                            </div>

                        </div><!-- /.box-body -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-success" onclick="prosesTransaksi('<?php echo $no_nota ?>')">Proses Transaksi</button>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-xs-12 table-responsive">

                    <input type="hidden" name="id_supliyer" value="<?php echo @$header->id_supliyer ?>">
                    <input type="hidden" name="idh_penjualan" value="<?php echo @$no_nota ?>">
                    <table id="table_client-<?php echo @$no_nota ?>" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Id Barang</th>
                                <th>Barang</th>
                                <th>Imey</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_penjualan a', array('a.idh_penjualan' => $no_nota))->result();
                            foreach ($details as $de) {
                                //getBarang
                                $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
//                                    $getHargaLama = $this->db->get_where('mst_stok', array('id_barang' => $de->id_barang, 'is_retur' => $no_nota))->row();
                                $isHp = $this->master->isHp($de->id_barang);
                                if ($isHp['is_hp'] == 1) {
                                    $jumlah = $de->jumlah_hp;
                                    $imey = "<a href=javascript:void(0) onclick=showImey('idtemp','$de->idh_penjualan','$de->id_barang',1,'temporary') class='btn btn-info btn-sm'>Imey</a>";
                                } else {
                                    $jumlah = $de->jumlah;
                                    $imey = "<a href=javascript:void(0) onclick=showImey('idtemp','$de->idh_penjualan','$de->id_barang',0,'temporary') class='btn btn-info btn-sm'>Imey</a>";
                                }
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?>
                                    <td><?php echo $de->id_barang ?>
                                    <td><?php echo @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                                    <td><?php echo $imey; ?>
                                    <td><?php echo $jumlah ?></td>
                                </tr>
                                <?php
                            }
                            $getNonFisik= $this->db->get_where('d_penjualan_non_fisik',array('idh_penjualan'=>$no_nota))->result();
                            foreach($getNonFisik as $gnf){
                                $detailNonFisik = $this->master->getDetailBarang("where a.id_barang= '$gnf->id_barang'")->row();
                                 ?>
                                <tr>
                                    <td><?php echo $no++; ?>
                                    <td><?php echo $gnf->id_barang ?>
                                    <td><?php echo @$detailNonFisik->category . ' ' . @$detailNonFisik->nama ?></td>
                                    <td><?php echo $gnf->nomer; ?>
                                    <td><?php echo $gnf->harga ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Id Barang</th>
                                <th>Barang</th>
                                <th>Imey</th>
                                <th>Jumlah</th>
                            </tr>
                        </tfoot>
                    </table>

                </div><!-- /.col -->
            </div>
        </div><!-- /.box-body -->
    </div>
    <script>
        $(function () {
            $("#table_client-<?php echo $no_nota ?>").dataTable();
        })
    </script>
    <?php
} else {
    ?>
    <section class="invoice">
        <div class="row invoice-info">
            <div class="alert alert-danger text-center">
                <h3>Invoice Kosong</h3>
            </div>
        </div>
    </section>
    <?php
}
?>
