<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$header) ?></b><h4/>
</div>
<table class="table table-bordered">
    <tr>
        <th>Keterangan Header
        <th>ID Barang
        <th>Nama Barang
        <th>Jumlah          
        <th>Status Reject          
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
                            <?php echo @$h->idh_pengiriman ?>
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
                            Sumber :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php $sumber=@$this->laporan->getNamaTempat(@$h->id_sumber)->nama;
							 echo (empty($sumber))? 'Superadmin': $sumber;
							?>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Tujuan :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php 
							$tujuan=@$this->laporan->getNamaTempat(@$h->id_tujuan)->nama; 
							echo (empty($tujuan))? 'Superadmin' : $tujuan;
							?>
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
                            Status Sampai :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo ($h->is_arrived == 0) ? 'Belum Sampai' : 'Sampai'; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-bold">
                            Status Reject :
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php echo ($h->is_reject == 0) ? 'Non Reject' : 'Reject'; ?>
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
                <td>&nbsp;
            </tr>
            <?php
           // if ($jenis_barang == 'hp') {
               // $this->db->where("b.nama_category", "HANDPHONE");
           // } else if ($jenis_barang == 'nonhp') {
               // $this->db->where("b.nama_category !=", "HANDPHONE");
           // }
            $getDetails = $this->db->select("a.*, b.nama,count(*) as jumlah_hp")->group_by('a.id_barang')->join('mst_barang_detail b', "b.id_barang = a.id_barang")->get_where('d_pengiriman a', array('a.idh_pengiriman' => $h->idh_pengiriman))->result();
            if (!empty($getDetails)) {
                foreach ($getDetails as $gd) {
                    $isHp = $this->master->isHp($gd->id_barang);
                    if ($isHp['is_hp'] == 1) {
                        $getImeys = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $gd->idh_pengiriman,'id_barang'=>$gd->id_barang))->result();
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
                        <td><?php echo (@$gd->is_trouble==1)?'Reject' : 'Non Reject'; ?>
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
            <th>Status Reject     
        </tr>
        <?php
    } else {
        echo "<tr><td colspan='5' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>