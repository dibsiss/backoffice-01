<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-info">
            <h3>Form Return Barang</h3>
        </div>
        <form class="form-inline">
            <div class="form-group">
                <label for="email">Jenis Return:</label>
                <select name="id_return" class="form-control">
                    <option value="">---Pilih Jenis Return---</option>
                    <?php
                    $jenis = $this->db->get('mst_retur')->result();
                    foreach ($jenis as $key => $val) {
                        ?>
                        <option value="<?php echo $val->id_retur ?>"><?php echo $val->status ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pwd">No Nota:</label>
                <input type="text" name="no_nota" class="form-control" placeholder="Input No Nota">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>