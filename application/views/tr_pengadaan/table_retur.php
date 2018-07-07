<script type="text/javascript">
    $(function () {
        $("#example1").dataTable();
    });
</script>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">List Retur</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">Action</th>
                    <th>Nota</th>
                    <th>Tanggal</th>
                    <th>Jenis Retur</th>
                    <th class="text-center">Detail Barang</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($tabel as $t) {
                    ?>
                    <tr>
                        <td class="text-center"><a href="javascript:void(0)" onclick="setRetur('<?php echo @$t->idh_retur ?>')" class="btn btn-danger btn-sm">Set Retur</a>
                        <td><?php echo $idh_retur = $t->idh_retur ?>
                        <td><?php echo $t->tgl_nota ?>
                        <td><?php echo $t->status ?>
                        <td colspan="3" class="text-center"><a href="javascript:void(0)" onclick="showDetailBarang('<?php echo $t->idh_retur ?>')" class="btn btn-info btn-sm">Detail</a>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center">Action</th>
                    <th>Nota</th>
                    <th>Tanggal</th>
                    <th>Jenis Retur</th>
                    <th class="text-center">Detail Barang</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<script>
    function showDetailBarang(id){
        emodal("<?php echo site_url('umum/showDetailBarangReturPengadaan/')?>"+id,"Detail Barang");
    }
</script>