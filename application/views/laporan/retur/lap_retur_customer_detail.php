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
			$getDetails = $this->db->select("a.*, b.nama,count(*) as jumlah_hp")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_retur_customer a', array('a.idh_retur_customer' => $h->idh_retur_customer))->result();
			?>
            <tr>
                <td>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            ID Transaksi :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$h->idh_retur_customer ?>
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
                            Toko :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo $tujuan=@$this->laporan->getNamaTempat(@$h->id_toko)->nama; ?> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Penanggung Jawab :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$this->laporan->getNamaUser(@$h->id_user)->fullname ?>
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
                            Retur Customer
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Keterangan :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo @$getDetails[0]->keterangan;?>
                        </div>
                    </div>
                <td>&nbsp;
                <td>&nbsp;
                <td>&nbsp;
               
            </tr>
            <?php
            
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
                    $isHp = $this->master->isHp($gd->id_barang);
					$getImeys = $this->db->get_where('imey_retur_customer', array('idd_retur_customer' => $gd->idd_retur_customer))->result();
                    $jumlah_barang = count($getImeys);
					if ($isHp['is_hp'] == 1) {
                        $imeyTampil = $this->general->listImey($getImeys, 'imey');
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