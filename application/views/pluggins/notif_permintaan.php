<!-- Menu toggle button -->
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-hand-lizard-o"></i>
    <span class="label label-info"><?php echo (empty($jumlah)) ? $count = 0 : $count = $jumlah; ?></span>
</a>
<ul class="dropdown-menu">
    <li class="header">Ada <?php echo @$count ?> Permintaan</li>
    <li>
        <!-- inner menu: contains the messages -->
        <ul class="menu">
            <?php if (!empty($hasil_permintaan)) { ?>
                <?php
                foreach ($hasil_permintaan as $hk) {
                    $hasil = $this->tr->detailNotif($hk);
                    $namaSumber = @$hasil['nama_sumber'];
                    $namaTujuan = $hasil['nama_tujuan'];
                    $penanggungJawab = @$hasil['penanggung_jawab'];
                    $foto = @$hasil['foto'];
                    ?>

                    <li><!-- start message -->
                        <a href="<?php echo site_url('umum/readPermintaan/' . $hk->idh_permintaan) ?>">
                            <div class="pull-left">
                                <img src="<?php echo base_url('assets/uploads/files/' . $foto) ?>" class="img-circle" alt="User Image">
                            </div>
                                    <p>Sumber : <?php echo $namaSumber ?></p>
                                    <p>Nota : <?php echo @$hk->idh_permintaan ?></p>
                                    <p>Penanggung Jawab : <?php echo @$penanggungJawab ?></p>
                        </a>
                    </li>
                    <!-- end message  -->

                <?php } ?>
            <?php } ?>
        </ul>
        <!-- /.menu -->
    </li>
    <li class="footer"><a href="<?php echo site_url('umum/readPermintaan') ?>">See All Invoice</a></li>
</ul>