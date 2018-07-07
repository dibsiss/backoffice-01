<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/switch_on_off'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php
//print_r($detail);
if (!empty($detail)) {
    foreach ($detail as $d) {
        $hasil = $this->tr->prosesDetailNotif($d);
        $nama_penerima = @$hasil['nama_sumber'];
        $penanggungJawab = @$hasil['penanggung_jawab'];
        $foto = @$hasil['foto'];
        $no_nota = @$d->idh_retur;
        //get detail
        $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $d->idh_retur))->result();
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
                    <div class="col-md-10">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Retur</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <dl>
                                    <dt>Asal :</dt>
                                    <dd><?php echo $nama_penerima ?>.</dd>
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
                    <div class="col-md-2">
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
                                foreach ($details as $de) {
                                    //getBarang
                                    $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                    $isHp = $this->master->isHp($de->id_barang);
                                    if ($isHp['is_hp'] == 1) {
                                        $jumlah = $de->jumlah_hp;
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$de->idh_retur','$de->id_barang',1) class='btn btn-info btn-sm'>Imey</a>";
                                    } else {
                                        $jumlah = $de->jumlah;
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$de->idh_retur','$de->id_barang',0) class='btn btn-info btn-sm'>Imey</a>";
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
                                    $.post("<?php echo site_url(); ?>/umum/tandaiRetur/" + id_toggle, {}, function (obj)
                                    {
                                        $('#loading-' + id_toggle).css('display', 'none');
                                        $('#box-' + id_toggle).css('display', 'none');
                                        manualCheck();
                                    });
                                } else {
                                    $('#toggle-event-' + id_toggle).bootstrapToggle('off');
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
<script>
    function showImey(idh_retur, id_barang, is_hp) {
        emodal("<?php echo site_url('umum/showImeyReturNotif/') ?>" + idh_retur + "/" + id_barang + "/" + is_hp, "List Imey");
    }
</script>
