<style>
    tr > td:first-child{
        text-align: center !important;
    }
</style>
<button class="btn btn-danger" onclick="deleteTemp()">Hapus Terpilih</button>
<button class="btn btn-primary" onclick="editTemp()">Ubah Terpilih</button><br><br>

<form id="tblTemp" method="post">
    <table id="table_pengadaan" class="table table-striped table-bordered table-hovered" cellspacing="0">
        <thead>
            <tr>
                <th><input onclick="toggle(this)" type="checkbox">Delete</th>
                <th>Barang</th>
                <th>Imey</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th class="text-center">Diskon</th>
                <th>Potongan</th>
                <th>Harga</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($isiTable as $isi) {
                //getBarang
                $detailBarang = $this->master->getDetailBarang("where a.id_barang= '$isi->id_barang'")->row();
                ?>
                <tr>
                    <td><input name="idh_temp[]" value="<?php echo $isi->idh_temp ?>" type="checkbox"></td>
                    <td><?php echo @$detailBarang->merk . ' ' . @$detailBarang->category . ' ' . @$detailBarang->nama ?></td>
                    <td><?php if ($isi->is_hp == 1) { ?><a class="btn btn-info btn-sm" href="javascript:void(0)" onclick="emodal('<?php echo site_url('umum/showImey/' . $isi->idh_temp) ?>', 'List Imey')">Imey</a> <?php } ?>
                    <td><?php echo $jml = @$isi->jumlah ?></td>
                    <td>Rp. <?php echo $harga = @$isi->harga_beli ?></td>
                    <td class="text-center"><?php echo $harga = @$isi->diskon ?> %</td>
                    <td>Rp. <?php echo $harga_potongan = @$isi->potongan ?></td>
                    <td>Rp. <?php echo $harga_diskon = @$isi->harga_diskon ?></td>
                    <td>Rp. <?php echo $subTotal = $jml * $harga_diskon ?>
                </tr>
                <?php
                $getImey = $this->db->get_where('d_temp', array('idh_temp' => $isi->idh_temp))->result();
                if (!empty($getImey)) {
                    $imeys = $this->general->convertArray($getImey, 'imei');
                    $imeyImplode='#'.implode(" #", $imeys);
                    echo " <tr>
                            <td>&nbsp;
                            <td colspan='8' class='text-center'> $imeyImplode
                           </tr>";
                }
                ?>

                <?php
                $total = $total + $subTotal;
            }
            ?>
        </tbody>
        <tr>
            <td colspan=7>&nbsp;
            <td><b>Total</b> :
            <td>Rp. <?php echo $this->general->formatRupiah($total) ?>
        </tr>
    </table>
</form>