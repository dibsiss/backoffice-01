<!-- Menu toggle button -->
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-mail-reply"></i>
    <span class="label label-warning"><?php echo (empty($jumlah)) ? $count = 0 : $count = $jumlah; ?></span>
</a>
<ul class="dropdown-menu">
    <li class="header">Ada <?php echo @$count ?> Retur</li>
    <li>
        <!-- inner menu: contains the messages -->
        <ul class="menu">
            <?php if (!empty($hasil_retur)) { ?>
                <?php
                foreach ($hasil_retur as $hk) {
                    $hasil = $this->tr->prosesDetailNotif($hk);
                    $namaSumber = @$hasil['nama_sumber'];
                    $penanggungJawab = @$hasil['penanggung_jawab'];
                    $foto = @$hasil['foto'];
                    ?>

                    <li><!-- start message -->
                        <a href="<?php echo site_url('umum/readRetur/' . $hk->idh_retur) ?>">
                            <div class="pull-left">
                                <img src="<?php echo base_url('assets/uploads/files/' . $foto) ?>" class="img-circle" alt="User Image">
                            </div>
                            <!--The message--> 
                            <p>Sumber : <?php echo $namaSumber ?></p>
                            <p>No Nota : <?php echo @$hk->idh_retur ?></p>
                            <p>Penanggung Jawab : <?php echo @$penanggungJawab ?></p>
                        </a>
                    </li>
                    <!-- end message  -->

                <?php } ?>
            <?php } ?>
        </ul>
        <!-- /.menu -->
    </li>
    <li class="footer"><a href="<?php echo site_url('umum/readRetur') ?>">See All Invoice</a></li>
</ul>

<!-- style="overflow-x: auto;height: 350px;" -->