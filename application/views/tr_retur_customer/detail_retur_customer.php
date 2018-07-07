<?php $this->load->view('pluggins/datatable_client') ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datepicker'); ?>
<?php
//print_r($detail);
if (!empty($header)) {
    $hasil = $this->tr->getPenanggungJawab($header->id_user);
    $penanggungJawab = @$hasil['penanggungJawab'];
    if (!empty($pesan)) {
        ?>
        <div class="alert alert-danger">
            <h4>Peringatan</h4>
            <p id="pesanEror"><?php echo @$pesan ?></p>
        </div>
        <?php
    }
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
                    <button class="btn btn-success" onclick="simpanTransaksi()">Simpan Transaksi</button>
                    <button class="btn btn-danger" onclick="truncateRetur('<?php echo $no_nota ?>')">Cancel</button>
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
                                        <input type="text" name="nota_penerimaan_customer" class="form-control" id="nota_penerimaan_customer" >
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
                        <input type="hidden" name="idh_penjualan" value="<?php echo @$no_nota ?>">
                        <table id="table_client-<?php echo @$no_nota ?>" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Id Barang</th>
                                    <th>Barang</th>
                                    <th>Imey/No Pelanggan</th>
                                    <th>Jumlah/Harga</th>
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
                                        //idtemp_retur customer berasal dari controller
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$idtemp_retur_customer','$de->idh_penjualan','$de->id_barang',1) class='btn btn-info btn-xs'>Imey</a>";
                                    } else {
                                        $jumlah = $de->jumlah;
                                        $imey = "<a href=javascript:void(0) onclick=showImey('$idtemp_retur_customer','$de->idh_penjualan','$de->id_barang',0) class='btn btn-info btn-xs'>Imey</a>";
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
                                $getNonFisik = $this->db->get_where('d_penjualan_non_fisik', array('idh_penjualan' => $no_nota))->result();
                                foreach ($getNonFisik as $gnf) {
                                    $detailNonFisik = $this->master->getDetailBarang("where a.id_barang= '$gnf->id_barang'")->row();
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?>
                                        <td><?php echo $idBarangNonFisik = $gnf->id_barang ?>
                                        <td><?php echo @$detailNonFisik->category . ' ' . @$detailNonFisik->nama ?></td>
                                        <td> 
                                            <?php
                                            if ($gnf->is_retur == 0) {
                                                ?>
                                                <div id="statusBarangTerjual-<?php echo $idtemp_retur_customer ?>" style="display: inline">
                                                    <a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="returNonFisik('<?php echo $idtemp_retur_customer ?>', '<?php echo $idBarangNonFisik ?>', '<?php echo $gnf->nomer ?>', '<?php echo $gnf->idd_penjualan ?>', '<?php echo @$gnf->harga ?>', '<?php echo $gnf->is_retur ?>')">Retur</a>
                                                    <span class="label label-success">Terjual</span> </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div id="statusBarangRetur-<?php echo $idtemp_retur_customer ?>" style="display: inline">
                                                    <a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="uninstallReturNonFisik('<?php echo $idtemp_retur_customer ?>', '<?php echo $idBarangNonFisik ?>', '<?php echo $gnf->nomer ?>', '<?php echo $gnf->idd_penjualan ?>', '<?php echo $gnf->is_retur ?>')">unRetur</a>
                                                    <span class="label label-danger">Retur</span> </div>
                                                <?php
                                            }
                                            ?>

                                            | <?php echo $gnf->nomer; ?>
                                        <td><?php echo $gnf->harga ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Id Barang</th>
                                    <th>Barang</th>
                                    <th>Imey/No Pelanggan</th>
                                    <th>Jumlah/Harga</th>
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
        $.post("<?php echo site_url('umum/insertReturCustomer') ?>", datastring, function (result) {
            obj = eval('(' + result + ')');
            if (obj.success == 1) {
				var url = '<?php echo site_url('laporan/invoiceReturCustomer/') ?>'+obj.idh_retur_customer;
				$('#urlNota').prop('href',url);
				$('#modal-bootstrap').modal('show');
                // $('#loading').css('display', 'none');
                // $(".gagal").css("display", "none");
                // showDetail(0);
                // $(".token-input-list-facebook").remove();
                // $(".token-input-dropdown-facebook").remove();
                // tokenAuto();

            } else {
                $('#loading').css('display', 'none');
                $(".gagal").css("display", "block");
                $("#pesan_gagal").html(obj.message);
            }
        });
    }
    function showImey(idtemp, idh_penjualan, id_barang, is_hp) {
        emodal("<?php echo site_url('umum/showImeyReturCustomer/') ?>" + idtemp + "/" + idh_penjualan + "/" + id_barang + "/" + is_hp + "/isi", "List Imey");
    }

    function returNonFisik(idhtemp, idBarang, imey, idimey_penjualan, harga, status) {
        swal({
            title: "Masukkan",
            text: "Keterangan Retur:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            animation: "slide-from-top",
            inputPlaceholder: "Input Keterangan Barang Retur"
        },
                function (keterangan) {
                    if (keterangan === false)
                        return false;

                    if (keterangan === "") {
                        swal.showInputError("Silahkan Tuliskan Keterangan Barang Rusak");
                        return false
                    }

                    var dataObject = {};
                    dataObject['keterangan'] = keterangan;
                    dataObject['imey'] = imey;
                    dataObject['harga'] = harga;
                    dataObject['idhtemp'] = idhtemp;
                    dataObject['id_barang'] = idBarang;
                    dataObject['idimey_penjualan'] = idimey_penjualan;
                    dataObject['status'] = status;
                    $.post("<?php echo site_url(); ?>/umum/insertDetailReturCustomerNonFisik/", dataObject, function (result)
                    {  
                        swal("Berhasil!", "Sukses", "success");
                        showDetail();
                    });
                });
    }
    function uninstallReturNonFisik (idhtemp, idBarang, imey, idimey_penjualan, status) {
        $('#loading').css('display', 'inline');
        swal({
            title: "Yakin ?",
            text: "Anda Akan Melakukan Retur?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Ya',
            showLoaderOnConfirm: true,
            closeOnConfirm: true
        },
                function (isConfirm) {
                    if (isConfirm) {
                        var dataObject = {};
                        dataObject['imey'] = imey;
                        dataObject['idhtemp'] = idhtemp;
                        dataObject['id_barang'] = idBarang;
                        dataObject['idimey_penjualan'] = idimey_penjualan;
                        dataObject['status'] = status;
                        dataObject['jenis'] = 'nonfisik';
                        $.post("<?php echo site_url(); ?>/umum/unistallDetailReturCustomer/", dataObject, function (result)
                        {
                            $('#loading').css('display', 'none');
                            showDetail('<?php echo @$no_nota ?>');
                            swal("Berhasil!", "Sukses", "success");

                        });
                    } else {
                        $('#loading').css('display', 'none');
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
        //batas
    }
</script>