<?php $this->load->view('pluggins/jquery') ?>
<?php $this->load->view('pluggins/switch_on_off'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable_client') ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h4>List Imey</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php $this->load->view('pluggins/loading') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table id="table_client" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Imey</th>
                        <th>Status</th>
                        <th>Retur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($isi as $key => $i) {
                        $idimey_retur_penjualan = $i->idimey_penjualan;
                        $statusDb = @$i->is_retur;
                        ($statusDb == 0) ? $status = 'Terjual' : $status = 'Retur';
                        ?>
                        <tr>
                            <td width="10" class="text-center"><?php echo $key + 1 ?></td>
                            <td><?php echo $imey = @$i->imey ?></td>
                            <td ><h4 id="<?php echo "status-" . $idimey_retur_penjualan ?>"><?php echo $status ?></h4></td>
                            <td><input id="<?php echo "reset-" . $idimey_retur_penjualan ?>" type="button" <?php echo ($statusDb == 1) ? 'Disabled' : '' ?> class="btn btn-primary" value="Retur" onclick="insertReturCustomer(<?php echo "'" . $idtemp . "','" . $id_barang . "','" . $imey . "','" . $idimey_retur_penjualan . "','" . $statusDb . "'" ?>)">
                                <input id="<?php echo "unreset-" . $idimey_retur_penjualan ?>" type="button" <?php echo ($statusDb == 0) ? 'Disabled' : '' ?> class="btn btn-danger" value="unRetur" onclick="uninstallReturCustomer(<?php echo "'" . $idtemp . "','" . $id_barang . "','" . $imey . "','" . $idimey_retur_penjualan . "','" . $statusDb . "'" ?>)"></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Imey</th>
                        <th>Status</th>
                        <th>Retur</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    function uninstallReturCustomer(idhtemp, idBarang, imey, idimey_penjualan, status) {
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
                        $.post("<?php echo site_url(); ?>/umum/unistallDetailReturCustomer/", dataObject, function (result)
                        {
                            $('#loading').css('display', 'none');
                            $("#status-" + idimey_penjualan).html("Terjual");
                            enableReset(idimey_penjualan);
                            swal("Berhasil!", "Sukses", "success");

                        });
                    } else {
                        $('#loading').css('display', 'none');
                        swal("Cancelled", "TerimaKasih", "error");
                    }
                });
        //batas
    }
    function enableReset(idimey_penjualan) {
        $('#reset-' + idimey_penjualan).prop('disabled', false);
        $('#unreset-' + idimey_penjualan).prop('disabled', true);
    }
    function disableReset(idimey_penjualan) {
        $('#reset-' + idimey_penjualan).prop('disabled', true);
        $('#unreset-' + idimey_penjualan).prop('disabled', false);
    }
    function insertReturCustomer(idhtemp, idBarang, imey, idimey_penjualan, status) {
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
                    dataObject['idhtemp'] = idhtemp;
                    dataObject['id_barang'] = idBarang;
                    dataObject['idimey_penjualan'] = idimey_penjualan;
                    dataObject['status'] = status;
                    dataObject['jenis'] = 'fisik';
                    $.post("<?php echo site_url(); ?>/umum/insertDetailReturCustomer/", dataObject, function (result)
                    {
                        $("#status-" + idimey_penjualan).html("Retur");
                        disableReset(idimey_penjualan);
                        swal("Berhasil!", "Sukses", "success");

                    });
                });
    }
</script>