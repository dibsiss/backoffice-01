<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php
if (!empty($header)) {
    $hasil = $this->tr->getPenanggungJawab($header->id_user);
    $penanggungJawab = @$hasil['penanggungJawab'];
    ?>
    <div class="box box-success box-solid" id="box-<?php echo $no_nota = @$header->idh_retur ?>">
        <div class="box-header with-border">
            <h4 class="box-title"># <?php echo $no_nota ?> </h4>        
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><?php echo $tgl_nota = @$header->tgl_nota ?>  <i class="fa fa-minus"></i></button>
            </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body" style="display: block;">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-text-width"></i>
                            <h3 class="box-title">Nota Pengiriman Retur Rusak</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl>
                                        <dt>Tanggal Nota :</dt>
                                        <dd><?php echo $tgl_nota ?>.</dd>
                                        <dt>Invoice :</dt>
                                        <dd> <?php echo @$no_nota ?>.</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl>
                                        <dt>Penanggung Jawab : </dt>
                                        <dd><?php echo $penanggungJawab ?>.</dd>
                                        <dt>Keterangan</dt>
                                        <dd><?php echo @$header->keterangan ?>.</dd>
                                    </dl>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-success" onclick="showTempReturRusak('<?php echo @$no_nota ?>')">Proses Transaksi Retur</button>
                </div>
            </div>
            <hr>
            <form method="post" id="frm-retur">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
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
                                $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $no_nota))->result();
                                foreach ($details as $de) {
                                    //getBarang
                                    $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                    $isHp = $this->master->isHp($de->id_barang);
                                    if ($isHp['is_hp'] == 1) {
                                        $jumlah = $de->jumlah_hp;
                                        $imey = "<a href=javascript:void(0) class='btn btn-default btn-sm'>Imey</a>";
                                    } else {
                                        $jumlah = $de->jumlah;
                                        $imey = "<a href=javascript:void(0) class='btn btn-default btn-sm'>Imey</a>";
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
    </form>
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