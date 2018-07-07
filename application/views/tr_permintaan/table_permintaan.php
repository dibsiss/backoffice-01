<style>
    tr > td:first-child{
        text-align: center !important;
    }
</style>
<button class="btn btn-danger" onclick="deleteTemp()">Hapus Terpilih</button><br><br>
<form id="tblTemp" method="post">
    <table id="table_permintaan" class="table table-striped table-bordered table-hovered" cellspacing="0">
        <thead>
            <tr>
                <th><input onclick="toggle(this)" type="checkbox">Delete</th>
                <th>Id Barang</th>
                <th>Barang</th>
                <th>Jumlah</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($isiTable as $isi) {
                //getBarang
                $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$isi->id_barang'")->row();
                $isHp = $this->master->isHp($isi->id_barang);
                $jumlah = $isi->jumlah;
                ?>
                <tr>
                    <td><input name="idd_permintaan[]" value="<?php echo $isi->idd_permintaan ?>" type="checkbox"></td>
                    <td><?php echo $isi->id_barang ?>
                    <td><?php echo @$detailBarang->merk . ' ' . @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                    <td><?php echo @$jumlah ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</form>