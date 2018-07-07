<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/switch_on_off'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php
//print_r($detail);
if (!empty($detail)) {
    foreach ($detail as $d) {
        $hasil = $this->tr->detailNotif($d);
        $namaSumber = @$hasil['nama_sumber'];
        $penanggungJawab = @$hasil['penanggung_jawab'];
        $foto = @$hasil['foto'];
        $reject = @$d->is_reject;
        $no_nota = @$d->idh_permintaan;
        //get detail
        $details = $this->db->select("a.*")->group_by('a.id_barang')->get_where('d_permintaan a', array('a.idh_permintaan' => $d->idh_permintaan))->result();
        ?>
        <div class="box box-success box-solid" id="box-<?php echo $no_nota ?>">
            <div class="box-header with-border">
                <h4 class="box-title"># <?php echo $no_nota ?> </h4>        
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><?php echo @$d->tgl_input ?>  <i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body" style="display: block;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Penerima</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <dl>
                                    <dt>Asal :</dt>
                                    <dd><?php echo @$namaSumber ?>.</dd>
                                    <dt>Invoice :</dt>
                                    <dd> <?php echo @$no_nota ?>.</dd>
                                    <dt>Penanggung Jawab : </dt>
                                    <dd><?php echo @$penanggungJawab ?>.</dd>
                                    <dt>Keterangan</dt>
                                    <dd><?php echo @$d->keterangan ?>.</dd>
                                </dl>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Tandai</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body pull-right">
                                 Tandai Sudah Dibaca <input value="<?php echo $no_nota ?>"  data-on="Ya" data-off="Tidak" id="toggle-event-<?php echo @$no_nota ?>" data-toggle="toggle" type="checkbox">
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="loading-<?php echo $no_nota ?>" style="display: none">
                            <?php $this->load->view('pluggins/loading_default_show') ?>
                        </div>
                    </div>
                </div>
                <!--batas logika lama-->
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="table_client-<?php echo @$no_nota ?>" class="table table-bordered table-striped">
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
                                    $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                    $isHp = $this->master->isHp($de->id_barang);
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?>
                                        <td><?php echo $de->id_barang ?>
                                        <td><?php echo @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                                        <td><?php echo @$de->jumlah ?></td>
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
                $('#toggle-event-<?php echo $no_nota ?>').change(function () {
                    var id_toggle = '<?php echo $no_nota ?>';
                    switchOn($(this).prop('checked'), id_toggle);
                });
            })
            function switchOn(respon, id_toggle) {
                if (respon === true) {
                    swal({
                        title: "Yakin ?",
                        text: "Anda Menandai Transaksi..?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Ya',
                        showLoaderOnConfirm: true,
                        closeOnConfirm: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    $('#loading-' + id_toggle).css('display', 'inline');
                                    $.post("<?php echo site_url(); ?>/umum/tandaiPermintaan/" + id_toggle, {}, function (obj)
                                    {
                                        $('#loading-' + id_toggle).css('display', 'none');
                                        $('#box-' + id_toggle).css('display', 'none');
                                        manualCheck();
                                    });
                                } else {
                                    $('#toggle-event-'+id_toggle).bootstrapToggle('off');
                                    swal("Cancelled", "Your imaginary file is safe :)", "error");
                                }
                            });
                }
            }
        </script>
        <?php
    }
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
