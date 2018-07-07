<div class="alert alert-info">
    <h4>Jumlah Laporan Yang Ditemukan <b><?php echo count(@$stocks) ?></b><h4/><br>
   
    
</div>
<table class="table table-bordered">
    <tr>
        <th>No
        <th>ID Barang
        <th>Nama Barang
        <th>Jumlah
        <th>Harga beli
        <th>sub total
        
    </tr>
    <?php
    $no = 1;
    $countJumlah=$countSubtotal=0;
    if (!empty($stocks)) {
        
        foreach ($stocks as $h) {
            @$subtotal=@$h->jumlah*@$h->harga_beli;
            ?>

            <tr>
                <td><?php echo $no ?>
                <td><?php echo $id_barang = @$h->id_barang ?>
                <td><?php echo @$h->nama_barang ?>
                <?php $countJumlah = $countJumlah + @$h->jumlah;?>           
                <td><?php echo @$h->jumlah  ?>
            <?php 
                if ($this->hak_user!='toko'){
            ?>
               <td><?php echo @$h->harga_beli ?>
                <?php $countSubtotal=$countSubtotal + $subtotal;?>
                <td><?php echo @$subtotal ?>            
             <?php                 
             }

             ?>
            </tr>
                
            <?php


            if ($h->is_hp == 1) {
                if(!empty($imey)){
                    $this->db->where('imey',$imey);
                }
                $getImeys = $this->db->get_where('mst_stok', array('id_toko' => @$h->id_toko, 'id_barang' => $id_barang, 'id_supliyer' => @$h->id_supliyer, 'status' => 0))->result();
                $imeyTampil = $this->general->listImey($getImeys, 'imey');
                ?>
                <tr>
                    <td colspan="5" class="text-center text-bold"><?php echo @$imeyTampil ?>
                </tr>
                <?php
            }
            $no++;
        }
        ?>
      
            <?php 
                if ($this->hak_user!='toko'){
            ?>
                Jumlah Item<?php echo @$countJumlah  ?><br>
                Jumlah Asset <?php echo 'Rp. '. $this->general->formatRupiah($countSubtotal)   ?>     
             <?php                 
             }

             ?>


        <tr>
        <th>No
        <th>ID Barang
        <th>Nama Barang
        <th>Harga beli
        <th>Jumlah
        <th>sub total
        </tr>
        <?php
    } else {
        echo "<tr><td colspan='5' class='text-center text-bold'> <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Data Kosong <span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></tr>";
    }
    ?>
</table>