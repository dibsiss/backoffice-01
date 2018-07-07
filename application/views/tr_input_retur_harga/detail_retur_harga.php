<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php
//print_r($detail);
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
                            <h3 class="box-title">Nota Pengiriman Retur Harga</h3>
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
                    <button class="btn btn-danger" onclick="simpanTransaksi()">Simpan Transaksi</button>
                </div>
            </div>

            <br><br>
            <form method="post" id="frm-retur">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-header with-border">
                            <i class="fa fa-text-width"></i>
                            <h3 class="box-title" style="margin-bottom: 20px;">Nota Penerimaan Retur Harga</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd">No Nota</label>
                                        <input type="text" name="nota_penerimaan_harga" class="form-control" id="nota_penerimaan_harga" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd">Tanggal Nota</label>
                                        <input type="text" id="tgl_tempo" name="tgl_nota" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-header -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 table-responsive">

                        <input type="hidden" name="id_supliyer" value="<?php echo @$header->id_supliyer ?>">
                        <input type="hidden" name="idh_retur" value="<?php echo @$no_nota ?>">
                        <table id="table_client-<?php echo @$no_nota ?>" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id Barang</th>
                                    <th>Barang</th>
                                    <th>Imey</th>
                                    <th>Jumlah</th>
                                    <th>Harga Lama</th>
                                    <th>Harga Baru</th>

                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;
                                $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $no_nota))->result();
                                foreach ($details as $de) {
                                    //getBarang
                                    $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                    $getHargaLama = $this->db->get_where('mst_stok', array('id_barang' => $de->id_barang, 'is_retur' => $no_nota))->row();
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
                                        <td><?php echo @$getHargaLama->harga_beli ?>
                                        <td><input type="number" class="form-control" name="hargaBaru[<?php echo $de->id_barang ?>]" id="hargaBaru">
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
                                    <th>Harga Lama</th>
                                    <th>Harga Baru</th>
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
<script>
    function simpanTransaksi() {
        var datastring = $("#frm-retur").serialize();
        $('#loading').css('display', 'inline');
        $.post("<?php echo site_url('umum/insertReturHarga') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "none");
                showDetail(0);
                $(".token-input-list-facebook").remove();
                $(".token-input-dropdown-facebook").remove();
                tokenAuto();
				//show modal
				var url = '<?php echo site_url('laporan/invoiceInputReturHarga/') ?>'+obj.idh_retur_harga;
				$('#urlNota').prop('href',url);
				$('#modal-bootstrap').modal('show');
				//===============

            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showImey(idh_retur, id_barang, is_hp) {
        emodal("<?php echo site_url('umum/showImeyReturNotif/') ?>" + idh_retur + "/" + id_barang + "/" + is_hp, "List Imey");
    }
</script>