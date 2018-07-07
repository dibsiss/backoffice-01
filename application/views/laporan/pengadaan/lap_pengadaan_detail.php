<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>Keterangan Header
        <th>ID Barang
        <th>Nama Barang
        <th>Harga Beli
        <th>Diskon
        <th>Potongan
        <th>Harga
        <th>Jumlah          
    </tr>
    <?php
    if (!empty($header)) {
        foreach ($header as $h) {
            ?>
            <tr>
                <td colspan="2">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            ID Transaksi :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_pengadaan ?>
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
                            Tangal Jatuh Tempo :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->tgl_jatuhtempo ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            No Nota :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->no_nota ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Supliyer :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo $this->master->getById('mst_supliyer', 'id_supliyer', @$h->id_supliyer)->nama ?>
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
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
            </tr>
            <?php
            if ($jenis_barang == 'hp') {
                $this->db->where("b.nama_category", "HANDPHONE");
            } else if ($jenis_barang == 'nonhp') {
                $this->db->where("b.nama_category !=", "HANDPHONE");
            }
            $getDetails = $this->db->select("a.*, b.nama")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_pengadaan a', array('a.idh_pengadaan' => $h->idh_pengadaan))->result();
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
					$jumlah_barang = $gd->jumlah;
                    $isHp = $this->master->isHp($gd->id_barang);
                    if ($isHp['is_hp'] == 1) {
                        $getImeys = $this->db->get_where('imey_pengadaan', array('idd_pengadaan' => $gd->idd_pengadaan))->result();
                        $imeyTampil = @$this->general->listImey($getImeys, 'imey');
                    }
                    ?>
                    <tr>
                        <td>&nbsp;
                        <td><?php echo @$gd->id_barang ?>
                        <td><?php echo @$gd->nama ?>
                        <td><?php echo @$gd->harga_beli ?>
                        <td><?php echo @$gd->diskon ?>
                        <td><?php echo @$gd->potongan ?>
                        <td><?php echo @$gd->harga_diskon ?>
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
            <th>Harga Beli
            <th>Diskon
            <th>Potongan
            <th>Harga
            <th>Jumlah          
        </tr>
        <?php
    } else {
        echo "<tr><td colspan='8' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>