<div class="row">
    <div class="col-sm-6">
        <?php if($is_hp==1){ ?>
        <div class="form-group">
            <label for="email">Masukkan Imey:</label>
            <input type="text" class="form-control" id="imey" name="imey" required="required">
        </div>
        <?php }else{
            ?>
        <div class="form-group">
            <label for="email">Masukkan Jumlah barang:</label>
            <input type="number" class="form-control col-sm-3" id="imey" name="jumlah" required="required">
        </div>
        <?php
        } ?>
        <div class="form-group">
        <button type="button" onclick="insertBaru()" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>