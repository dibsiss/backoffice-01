<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-history"></i>
    <span class="label label-danger"><?php echo (empty($jumlah)) ? $count = 0 : $count = $jumlah; ?></span>
</a>
<ul class="dropdown-menu">
    <li class="header">Ada <?php echo @$count ?> Reject Pengiriman</li>
    <li>
        <!-- inner menu: contains the messages -->
        <ul class="menu">
            <?php if (!empty($hasil_kirim)) { ?>
                <?php
                foreach ($hasil_kirim as $hk) {
                    $hasil = $this->tr->detailNotif($hk);
                    $namaTujuan = @$hasil['nama_tujuan'];
                    $penanggungJawab = @$hasil['penanggung_jawab'];
                    $foto = @$hasil['foto'];
                    ?>

                    <li><!-- start message -->
                        <a href="<?php echo site_url('umum/readReject/' . $hk->idh_pengiriman) ?>">
                            <div class="pull-left">
                                <img src="<?php echo base_url('assets/uploads/files/' . $foto) ?>" class="img-circle" alt="User Image">
                            </div>
                            <!--The message--> 
                            <p>Tujuan : <?php echo $namaTujuan ?></p>
                            <p>No Nota : <?php echo @$hk->idh_pengiriman ?></p>
                            <p>Penanggung Jawab : <?php echo @$penanggungJawab ?></p>
                        </a>
                    </li>
                    <!-- end message  -->

                <?php } ?>
            <?php } ?>
        </ul>
        <!-- /.menu -->
    </li>
    <li class="footer"><a href="<?php echo site_url('umum/readReject') ?>">See All Invoice</a></li>
</ul>

<!-- style="overflow-x: auto;height: 350px;" -->