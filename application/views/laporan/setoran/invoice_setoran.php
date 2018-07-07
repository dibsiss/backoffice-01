<?php $this->load->view('pluggins/print_element'); ?>
<div class="row">
    <div class="col-xs-12">
        <button type="button" class="btn btn-primary btn-lg text-center" id="simplePrint"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print</button>
    </div>
</div>
<hr>
<div id="toPrint">
        <?php
		if (!empty($detailToko)) {
			$namaToko = @$detailToko->nama;
			$alamatToko = @$detailToko->alamat_toko;
			$telpToko = @$detailToko->telp_toko;
		}
        if ($detail) {
            ?>
            <div class="box box-success box-solid" >
                <div class="box-body" style="display: block;">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
								<?php echo '<b>X-Metrik '.@$namaToko.'</b>' ?>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo @$alamatToko.' telp ('.@$telpToko.')' ?>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-12 text-center">
							<?php echo '<b>Tanggal Setor '. @$tgl_setor.'</b>' ?>
						</div>
						
					</div>
					<div class="row text-bold">
						<div class="col-xs-6 col-sm-6 col-md-6">
							Jenis Setoran
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							Jumlah
						</div>
					</div>
					<div class="row text-bold">
						<div class="col-xs-12 col-sm-12 col-md-12" style="margin-left:8px">
							Pemasukan
						</div>
					</div>

					 <div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Fisik :</div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahFisik=@$setoranFisik->jumlah_fisik; echo 'Rp. '.$this->general->formatRupiah($jumlahFisik) ?></div>
					 </div>
					 <div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Non Fisik :<?php //echo $no++; ?></div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahNonFisik=@$setoranNonFisik->harga; echo 'Rp. '.$this->general->formatRupiah($jumlahNonFisik) ?></div>
					 </div>
					 <div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Lain-Lain :<?php //echo $no++; ?></div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahLain=@$setoranLain->nominal; echo 'Rp. '.$this->general->formatRupiah($jumlahLain); ?></div>
					 </div>
					 <div class="row text-bold">
						<div class="col-xs-6 col-sm-6 col-md-6">Total Pemasukan</div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $totalMasuk = $jumlahFisik+$jumlahNonFisik+$jumlahLain; echo 'Rp. '. $this->general->formatRupiah($totalMasuk) ?></div>
					 </div>
					 
					 
					 
					 <div class="row text-bold">
						<div class="col-xs-12 col-sm-12 col-md-12" style="margin-left:8px">
							Pengeluaran
						</div>
					</div>
					<div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Fisik :</div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahFisikKeluar=@$fisikKeluar->harga; echo 'Rp. '.$this->general->formatRupiah($jumlahFisikKeluar) ?></div>
					 </div>
					 <div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Non Fisik :<?php //echo $no++; ?></div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahNonFisikKeluar=@$nonFisikKeluar->harga; echo 'Rp. '.$this->general->formatRupiah($jumlahNonFisikKeluar) ?></div>
					 </div>
					 <div class="row" style="margin-left:10px">
						<div class="col-xs-6 col-sm-6 col-md-6">Setoran Lain-Lain :<?php //echo $no++; ?></div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $jumlahLainKeluar=@$lainKeluar->nominal; echo 'Rp. '.$this->general->formatRupiah($jumlahLainKeluar); ?></div>
					 </div>
					 
					 <div class="row text-bold">
						<div class="col-xs-6 col-sm-6 col-md-6">Total Pengeluaran</div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php $totalKeluar=$jumlahFisikKeluar+$jumlahNonFisikKeluar+$jumlahLainKeluar; echo 'Rp. '. $this->general->formatRupiah($totalKeluar) ?></div>
					 </div>
					 <br>
					 <div class="row text-bold">
						<div class="col-xs-6 col-sm-6 col-md-6">Total</div>
						<div class="col-xs-6 col-sm-6 col-md-6"><?php echo 'Rp. '.$this->general->formatRupiah($totalMasuk-$totalKeluar) ?></div>
					 </div>
					        
                </div><!-- /.box-body -->
            </div>

            <?php
        } else {
            ?>
            <section class="invoice">
                <div class="row invoice-info">
                    <div class="alert alert-danger text-center">
                        <h3 class="text-center">Invoice Kosong</h3>
                    </div>
                </div>
            </section>
            <?php
        }
        ?>
</div>