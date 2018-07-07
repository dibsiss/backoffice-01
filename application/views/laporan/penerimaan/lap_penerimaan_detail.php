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
                            ID Transaksi Kirim :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_pengiriman?>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            ID Transaksi Terima :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_penerimaan ?>
                        </div>
                    </div>
                   
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Sumber :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$this->master->getById('data_sumber', 'id', @$h->id_sumber)->nama ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Penanggung Jawab :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$this->master->getById('data_user', 'id_user', @$h->id_user)->fullname ?>
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
            if ($jenis_barang == 'hp') {
                $this->db->where("b.nama_category", "HANDPHONE");
            } else if ($jenis_barang == 'nonhp') {
                $this->db->where("b.nama_category !=", "HANDPHONE");
            }
            $getDetails = $this->db->select("a.*, b.nama, count(*) as jumlah_hp")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_penerimaan a', array('a.idh_penerimaan' => $h->idh_penerimaan))->result();
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
					$jumlah_barang = $gd->jumlah;
                    $isHp = $this->master->isHp($gd->id_barang);
                    if ($isHp['is_hp'] == 1) {
                        $getImeys = $this->db->get_where('d_penerimaan', array('idh_penerimaan' => $h->idh_penerimaan, 'id_barang'=>$gd->id_barang))->result();
                        $imeyTampil = $this->general->listImey($getImeys, 'imey');
						$jumlah_barang = $gd->jumlah_hp;
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
                            <td colspan="4" class="text-center text-bold"><?php echo @$imeyTampil ?>
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
        </tr>
        <?php
    } else {
        echo "<tr><td colspan='8' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>