<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/switch_on_off'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/modal_bootstrap') ?>
<?php
$ckeditorSetting = array('toolbar' => 'Basic', 'needJquery' => false);
$this->load->view('pluggins/textarea', $ckeditorSetting);
//bawah
if (!empty($detail)) {
    foreach ($detail as $d) {
        $hasil = $this->tr->detailNotif($d);
        $namaSumber = @$hasil['nama_sumber'];
        $penanggungJawab = @$hasil['penanggung_jawab'];
        $no_nota = @$d->idh_pengiriman;
        $foto = @$hasil['foto'];
        $reject = @$d->is_reject;
        //get detail
        $details = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_pengiriman a', array('a.idh_pengiriman' => $d->idh_pengiriman))->result();
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
                    <div class="col-md-4">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Pengirim</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <dl>
                                    <dt>Sumber :</dt>
                                    <dd><?php echo $namaSumber ?>.</dd>
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
                    <div class="col-md-8">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-text-width"></i>
                                <h3 class="box-title">Penerimaan Barang & Complain</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <h4 style="text-decoration:underline ">Penerimaan Barang</h4>
                                        <input value="<?php echo $no_nota ?>" class="simpan-cuy" data-toggle="toggle" data-size="small" type="checkbox" data-on="Ya" data-off="Tidak"> <br>Dengan Ini Saya Menyatakan Barang Telah Saya Terima.
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group pull-right">
                                            <h4 style="text-decoration:underline ">Complain Barang</h4>
                                            <input data-on="Ya" class="pull-right" data-off="Tidak" id="toggle-event-<?php echo @$no_nota ?>" data-toggle="toggle" <?php echo ($reject == 1) ? 'checked' : '' ?> type="checkbox">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12" id="simpan-<?php echo $no_nota ?>" style="display: none">
                                        <form id="ket-<?php echo $no_nota ?>" method="post">
                                            <div class="form-group">
                                                <label for="pwd">Keterangan</label>
                                                <textarea id='keterangan-<?php echo $no_nota ?>' rows="10" cols="10" name='keterangan-<?php echo $no_nota ?>' class='texteditor' ></textarea>
                                            </div>
                                        </form>
                                        <button class="btn btn-success pull-right" value="<?php echo $no_nota ?>" onclick="simpanPenerimaan($(this).val())"><i class="fa fa-credit-card"></i> Simpan Penerimaan</button> 
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="loading-simpan-<?php echo $no_nota ?>" style="display: none">
                            <div class='alert alert-danger alert-dismissable text-left fade in'>
                                <div id='pesan_gagal-<?php echo $no_nota ?>'></div>
                            </div>
                            <?php $this->load->view('pluggins/loading_default_show') ?>
                        </div>
                        <div id="loading-<?php echo $no_nota ?>" style="display: none">
                            <?php $this->load->view('pluggins/loading_default_show') ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="table_client-<?php echo @$no_nota ?>" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Id Barang</th>
                                    <th>Barang</th>
                                    <th>Imey</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($details as $de) {
                                    //getBarang
                                    $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$de->id_barang'")->row();
                                    $isHp = $this->master->isHp($de->id_barang);
                                    if ($isHp['is_hp'] == 1) {
                                        $jumlah = $de->jumlah_hp;
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$de->idh_pengiriman','$de->id_barang',1) class='btn btn-info btn-sm'>Imey</a>";
                                    } else {
                                        $jumlah = $de->jumlah;
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$de->idh_pengiriman','$de->id_barang',0) class='btn btn-info btn-sm'>Imey</a>";
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $de->id_barang ?>
                                        <td><?php echo @$detailBarang->merk . ' ' . @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                                        <td><?php echo @$imey ?>
                                        <td><?php echo @$jumlah ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
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
            });
            function switchOn(respon, id_toggle) {
                if (respon === true) {
                    swal({
                        title: "Yakin ?",
                        text: "Anda Akan Melakukan Complain Barang..?",
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
                                    $('.detail-toggle-' + id_toggle).css('display', 'inline');
                                    $.post("<?php echo site_url(); ?>/umum/setComplain/" + id_toggle, {}, function (obj)
                                    {
                                        $('#loading-' + id_toggle).css('display', 'none');
                                    });
                                } else {
                                    $('#toggle-event-' + id_toggle).bootstrapToggle('off');
                                    $('.detail-toggle-' + id_toggle).css('display', 'none');
                                }
                            });

                } else {
                    swal({
                        title: "Yakin ?",
                        text: "Anda Akan Menghapus Complain Barang",
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
                                    $('.detail-toggle-' + id_toggle).css('display', 'none');
                                    $.post("<?php echo site_url(); ?>/umum/destroyComplain/" + id_toggle, {}, function (obj)
                                    {
                                        $(".combo-header-" + id_toggle).bootstrapToggle('off');
                                        $('#loading-' + id_toggle).css('display', 'none');
                                    });

                                } else {
                                    $(id_toggle).bootstrapToggle('off');
                                    $('#loading').css('display', 'inline');
                                    $('.detail-toggle-' + id_toggle).css('display', 'inline');
                                }
                            });
                }
            }

        </script>
        <?php
    }
    ?>
    <script>
        function showImey(idh_penerimaan, id_barang, is_hp) {
            emodal("<?php echo site_url('umum/showImeyPenerimaan/') ?>" + idh_penerimaan + "/" + id_barang + "/" + is_hp, "List Imey");
        }
        $('.simpan-cuy').change(function () {
            switchSimpan($(this).prop('checked'), $(this).val());
        })
        function switchSimpan(respon, val) {
            if (respon === true) {
                $('#simpan-' + val).css('display', 'inline');
            } else {
                $('#simpan-' + val).css('display', 'none');
            }
        }
        function simpanPenerimaan(id) {
            var datastring = $("#ket-" + id).serialize();
            $('#loading-simpan-' + id).css('display', 'inline');
            $.post("<?php echo site_url(); ?>/umum/simpanPenerimaan/" + id, datastring, function (obj)
            {
                result = eval('(' + obj + ')');
                if (result.success == 1) {
					$('#loading-simpan-' + id).css('display', 'none');
					if(result.is_reject==1){
						//jika variabel reject bernilai satu maka keluar notif untuk mencetak nota
						var url = '<?php echo site_url('laporan/invoiceReject/') ?>'+result.id_pengiriman+"/"+result.id_penerimaan;
						$('#urlNota').prop('href',url);
						$('#modal-bootstrap').modal('show');
					}else{
						//jika tidak maka langsung direload saja
						location.reload();
					}
                    //                showTable(result.no_nota);
                } else {
                    $("#pesan_gagal-" + id).html(result.message);
                }
            });
        }
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