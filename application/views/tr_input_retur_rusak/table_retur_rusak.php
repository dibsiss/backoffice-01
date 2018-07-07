<?php $this->load->view('pluggins/datatable_client') ?>
<script type="text/javascript">
    $(function () {
        $("#table_client").dataTable();
    });
</script>
<table id="table_client" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Id Barang</th>
            <th>Imey</th>
            <th>Jenis Kembalian</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $no = 1;
        $id_barang = $this->uri->segment(4);
        foreach ($isi as $de) {
            ?>
            <tr>
                <td><?php echo @$no++; ?>
                <td><?php echo @$id_barang ?>
                <td><?php echo @$de->imey ?></td>
                <td><?php echo @$de->jenis_kembalian.' '.@$de->id_barang_kembalian ?>
                <td><button class="btn btn-danger" onclick="hapusImeyReturRusak('<?php echo $de->idd_temp_retur_rusak ?>')">Hapus</button>
            </tr>
        <?php } ?>
    </tbody>

    <tfoot>
        <tr>
            <th>No</th>
            <th>Id Barang</th>
            <th>Imey</th>
            <th>Jenis Kembalian</th>
            <th>Action</th>
        </tr>
    </tfoot>
</table>