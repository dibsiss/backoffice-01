<style>
    tr > td:first-child{
        text-align: center !important;
    }
    #headerTabel{
        background: #9cd58e;
        font-weight: bold;
    }
</style>
<button class="btn btn-danger" onclick="deleteKeranjang()" style="margin-bottom: 10px;">Hapus Terpilih</button>

<form id="tblPenjualan" method="post">
    <table id="table_pengadaan" class="table table-striped table-bordered table-hovered" cellspacing="0">
        <thead>
            <tr>
                <th id="headerTabel"><input onclick="toggle(this)" type="checkbox">Delete</th>
                <th id="headerTabel">Barang</th>
                <th id="headerTabel">Imey</th>
                <th id="headerTabel" class="text-center">Jumlah</th>
                <th id="headerTabel" class="text-center">Diskon</th>
                <th id="headerTabel">Potongan</th>
                <th id="headerTabel">Harga</th>
                <th id="headerTabel">Harga Potongan</th>
                <th id="headerTabel">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalFisik = 0;
            if (!empty($isiTable)) {
                foreach ($isiTable as $isi) {
                    //getBarang
                    $detailBarang = $this->db->get_where('mst_barang', array('id_barang' => @$isi->id_barang))->row();
                    //harga
                    $harga_awal = @$isi->harga;
                    $harga_jual = @$isi->harga;
                    $hargaPotongan = $isi->potongan;
                    if ($hargaPotongan == 0) {
                        $diskon = $isi->diskon;
                        if ($diskon != 0) {
                            $hargaDiskonan = $harga_jual * ($diskon / 100);
                            $harga_jual = $harga_jual - $hargaDiskonan;
                        }
                    } else {
                        $harga_jual = $harga_jual - $hargaPotongan;
                    }
                    $isHp = $this->master->isHp($isi->id_barang)['is_hp'];
                    if($isHp==1){
                        $getImey = $this->db->get_where('imey_penjualan',array('idd_penjualan'=>$isi->idd_penjualan))->row();
                        $imeyHp = @$getImey->imey;
                    }
                    ?>
                    <tr>
                        <td><input name="idd_penjualan[]" value="<?php echo $idd_penjualan = $isi->idd_penjualan ?>" type="checkbox"></td>
                        <td><?php echo @$detailBarang->nama ?></td>
                        <td><?php echo ($isHp == 1) ? @$imeyHp : '' ?>
                        </td>
                        <td class="text-center">
                            <?php
                            if ($isHp == 1) {
                                echo $jml = @$isi->jumlah;
                            } else {
                                ?>
                                <a href="#" class="table-jumlah" data-type="number" data-min="1" data-menclek="menclek" data-name="jumlah" data-pk="<?php echo $idd_penjualan ?>" data-url="<?php echo site_url('umum/editRowPenjualan') ?>" data-title="Input Jumlah"> <?php echo $jml = @$isi->jumlah ?></a></td>
                        <?php } ?>
                        <td class="text-center"><a href="#" class="table-diskon" data-type="number" data-min="0" data-name="diskon" data-pk="<?php echo $idd_penjualan ?>" data-url="<?php echo site_url('umum/editRowPenjualanHarga') ?>" data-title="Input Diskon"> <?php echo $diskonan = @$isi->diskon ?> %</a></td>
                        <td><a href="#" class="table-potongan" data-type="number" data-min="0" data-name="potongan" data-pk="<?php echo $idd_penjualan ?>" data-url="<?php echo site_url('umum/editRowPenjualanHarga') ?>" data-title="Input Potongan">Rp. <?php
                                $harga_potongan = @$isi->potongan;
                                echo $this->general->formatRupiah($harga_potongan);
                                ?></a></td>
                        <td>Rp. <?php echo $this->general->formatRupiah($harga_awal); ?>
                        <td>Rp. <?php
                            $harga_jual;
                            echo $this->general->formatRupiah($harga_jual)
                            ?></td>
                        <td>Rp. <?php
                            $subTotal = $jml * $harga_jual;
                            echo $this->general->formatRupiah($subTotal);
                            ?>
                    </tr>
                    <?php
                    $totalFisik = @$totalFisik + $subTotal;
                }
            }
            ?><tr>
                <td id="headerTabel">&nbsp;
                <td id="headerTabel">Barang
                <td id="headerTabel">Jenis
                <td id="headerTabel">Jumlah
                <td id="headerTabel">&nbsp;
                <td id="headerTabel" class="text-center">No Pelanggan
                <td id="headerTabel">Harga
                <td id="headerTabel">Harga Potongan
                <td id="headerTabel">Sub Total
            </tr>
            <?php
            //penjualan Non Fisik
            $subTotalNonFisik = 0;
            if (!empty($isitTableNonFisik)) {
                foreach ($isitTableNonFisik as $nf) {
                    $getDetailBarangNon = $this->db->get_where('mst_barang', array('id_barang' => $nf->id_barang))->row();
                    $getDetailJenisNon = $this->db->get_where('mst_jenis_non_fisik', array('id_jenis_non_fisik' => $nf->id_jenis_non_fisik))->row();
                    ?>
                    <tr>
                        <td><input name="idd_penjualan[]" value="<?php echo $idd_penjualan = $nf->idd_penjualan ?>" type="checkbox"></td>
                        <td><?php echo @$getDetailBarangNon->nama ?>
                        <td><?php echo @$getDetailJenisNon->nama ?>
                        <td class="text-center">1 
                        <td>&nbsp;
                        <td class="text-center"><?php echo @$nf->nomer ?>
                        <td class="text-center"><?php $hargaNonFisik=@$nf->harga; echo 'Rp. '.$this->general->formatRupiah($hargaNonFisik)  ?>
                        <td class="text-center"><?php echo 'Rp. '.$this->general->formatRupiah($hargaNonFisik) ?>
                        <td><?php
                            $subTotalNonFisik = $subTotalNonFisik + @$hargaNonFisik;
                            echo 'Rp. '.$this->general->formatRupiah($hargaNonFisik);
                    ?>
                    </tr>
                    <?php
                }
            }
            $total = $subTotalNonFisik + $totalFisik;
            ?>
        </tbody>
        <tr>
            <td>&nbsp;
            <td><b style="height: 70px;font-size: 20px;vertical-align: middle">Total</b> :
            <td colspan="7" class="text-right" style="height: 70px;font-size: 30px">Rp. <?php echo $this->general->formatRupiah($total) ?>
        </tr>
    </table>
</form>
<form id="bayarKembalian" method="post">
<input type=hidden name=jumlahTotal value="<?php echo $total ?>">
<input type=hidden name=uangKembali id="uangKembali">
<div class="row">
	<div class="col-md-2 col-lg-2 col-xs-2">
		<div class="form-group">
			<label for="email">Jenis Bayar:</label><br>
			<input onclick="jenisBayar($(this).val())" type="radio" name="jenis_bayar" value="0" checked>Tunai
			<input onclick="jenisBayar($(this).val())" type="radio" name="jenis_bayar" value="1">Non Tunai
		  </div>
	</div>
	<div id="implementBayar"></div>
</div>
</form>
<div class="row">
	<div class="col-md-12">
		<button type="button" class="btn btn-primary btn-block" onclick="insertPenjualan()" >Simpan</button>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12 col-xs-12">
		<input id="kembalian" class="text-center form-control" readonly="" style="height: 70px;font-size: 30px" value="" type="text">
	</div>
</div>
<?php $sess_idh_penjualan = $this->session->userdata('idh_penjualan'); ?>
<?php //get bank 
$getBank = $this->db->get('mst_bank')->result();
?>
<script>
//ketika dienter maka panggil insert penjualan
$(document).on("keypress", "#bayar", function (e) {
	if (e.keyCode == 13)
	{
		insertPenjualan();
	}else{
		return e;
	}
	e.preventDefault();
});

$(document).on("keypress", "#no_referensi", function (e) {
	if (e.keyCode == 13)
	{
		insertPenjualan();
	}else{
		return e;
	}
	e.preventDefault();
});

function setBayar(){
	$("#implementBayar").html('<div class="col-md-10 col-lg-10 col-xs-10"><div class="form-group"><label for="email">Bayar:</label><input type="number" name="jumlahBayar" class="form-control" id="bayar"></div></div>');
	$("#kembalian").val("");
}
function jenisBayar(id){
	if(id==0){
		setBayar();
	}else{
		$("#implementBayar").html('<div class="col-md-5"><div class="form-group"><label for="email">Bank:</label><select required class="form-control" name="bank"><option value=""></option><?php foreach($getBank as $g){ ?> <option value="<?php echo $g->id_bank ?>"><?php echo $g->nama ?></option> <?php } ?></select></div></div> <div class="col-md-5"><div class="form-group"><label for="email">No Referensi:</label><input type="text" class="form-control" required name="no_referensi" id="no_referensi"></div></div>');
		$("#kembalian").val("Lunas");
	}
}
$(document).on("keyup", "#bayar", function (e) {
	var bebanBayar = '<?php echo @$total ?>';
	var jumlahBayar = $("#bayar").val();
	var kembalian = jumlahBayar-bebanBayar;
  $("#kembalian").val("Kembalian Rp. "+kembalian);
  $("#uangKembali").val(kembalian);
});
    $(document).ready(function () {
		//set default bayar
		setBayar();
		//end set
        $(".table-jumlah").editable({
            success: function (result, newValue) {
                response = eval('(' + result + ')');
                if (response.status == 'sukses') {
                    $(".gagal").css("display", "none");
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                } else {
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                    $(".gagal").css("display", "block");
                    $("#pesan_gagal").html(response.pesan);
                }
            }
        });
        $(".table-diskon").editable({
            success: function (result, newValue) {
                response = eval('(' + result + ')');
                if (response.status == 'sukses') {
                    $(".gagal").css("display", "none");
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                } else {
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                    $(".gagal").css("display", "block");
                    $("#pesan_gagal").html(response.pesan);
                }
            }
        });
        $(".table-potongan").editable({
            success: function (result, newValue) {
                response = eval('(' + result + ')');
                if (response.status == 'sukses') {
                    $(".gagal").css("display", "none");
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                } else {
                    showTable("<?php echo @$sess_idh_penjualan ?>");
                    $(".gagal").css("display", "block");
                    $("#pesan_gagal").html(response.pesan);
                }
            }
        });
    })
</script>