<style>
    tr > td:first-child{
        text-align: center !important;
    }
</style>
<button class="btn btn-danger" onclick="deleteTemp()">Hapus Terpilih</button><br><br>
<form id="tblTemp" method="post">
    <table id="table_pengiriman" class="table table-striped table-bordered table-hovered" cellspacing="0">
        <thead>
            <tr>
                <th><input onclick="toggle(this)" type="checkbox">Delete</th>
                <th>Id Barang</th>
                <th>Barang</th>
                <th>Imey</th>
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
                if ($isHp['is_hp'] == 1) {
                    $jumlah = $isi->jumlah_hp;
                    $imey = "<a href=javascript:void(0) onclick=showImey('$isi->idh_pengiriman','$isi->id_barang',1) class='btn btn-info btn-sm'>Imey</a>";
                } else {
                    $jumlah = $isi->jumlah;
                    $imey = "<a href=javascript:void(0) onclick=showImey('$isi->idd_pengiriman','$isi->id_barang',0) class='btn btn-info btn-sm'>Imey</a>";
                }
                ?>
                <tr>
                    <td><input name="idd_pengiriman[]" value="<?php echo $isi->idd_pengiriman ?>" type="checkbox"></td>
                    <td><?php echo $isi->id_barang ?>
                    <td><?php echo @$detailBarang->merk . ' ' . @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                    <td><?php echo @$imey ?>
                    <td><?php echo @$jumlah ?></td>
                </tr>
                <?php
                if ($isHp['is_hp'] == 1) {
                    $getImey = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $isi->idh_pengiriman,'id_barang'=>$isi->id_barang))->result();
                    if (!empty($getImey)) {
                        $imeys = $this->general->convertArray($getImey, 'imey');
                        $imeyImplode = '#' . implode(" #", $imeys);
                        echo " <tr>
                            <td>&nbsp;
                            <td colspan='4' class='text-center'> $imeyImplode
                           </tr>";
                    }
                }
                ?>
                <?php
            }
            ?>
        </tbody>
    </table>
</form>
<script>
    function showImey(idh_pengiriman, id_barang, is_hp) {
        emodal('<?php echo site_url('umum/showImeyPengiriman/') ?>/' + idh_pengiriman + "/" + id_barang + "/" + is_hp, 'List Imey');
    }
</script>