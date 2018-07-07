<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>Keterangan Header
        <th>ID Barang
        <th>Nama Barang
        <th>Jumlah          
    </tr>
    <?php
    if (!empty($header)) {
        foreach ($header as $h) {
            ?>
            <tr>
                <td>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            ID Transaksi :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_retur ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Tangal Nota :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->tgl_nota ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Tujuan :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo $this->tr->getTujuan(@$h->id_supliyer)['nama_tujuan'] ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Penanggung Jawab :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo $this->tr->getPenanggungjawab(@$h->id_user)['penanggungJawab'] ?>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Tanggal Input :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->tgl_input ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Status Retur :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo ($h->is_replay == 0) ? 'Non Balas' : 'Balas'; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Jenis Retur :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo $this->master->getById('mst_retur', 'id_retur', @$h->id_retur)->status ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Keterangan :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->keterangan ?>
                        </div>
                    </div>
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
               
            </tr>
            <?php
            $getDetails = $this->db->select("a.*, b.nama,count(*) as jumlah_hp")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_retur a', array('a.idh_retur' => $h->idh_retur))->result();
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
                    $isHp = $this->master->isHp($gd->id_barang);
                    if ($isHp['is_hp'] == 1) {
                        $getImeys = $this->db->get_where('d_retur', array('idh_retur' => $gd->idh_retur,'id_barang'=>$gd->id_barang))->result();
                        $imeyTampil = $this->general->listImey($getImeys, 'imey');
						$jumlah_barang = $gd->jumlah_hp;
                    }else{
						$jumlah_barang = $gd->jumlah;
					}
                    ?>
                    <tr>
                        <td>&nbsp;
                        <td><?php echo @$gd->id_barang ?>
                        <td><?php echo @$gd->nama ?>
                        <td><?php echo @$jumlah_barang ?>
                        </tr>
                    <?php
                    if ($isHp['is_hp'] == 1) {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-bold"><?php echo @$imeyTampil ?>
                        </tr>
                        <?php
                    }
                }
            }
        }
        ?>
        <tr>
            <th>Keterangan Header
            <th>ID Barang
            <th>Nama Barang
            <th>Jumlah          
            <tr>
        <?php
    } else {
        echo "<tr><td colspan='5' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>