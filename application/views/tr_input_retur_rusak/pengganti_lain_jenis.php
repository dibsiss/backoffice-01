<script>
    $(function () {
        $(".chosen-select,.chosen-multiple-select").chosen({allow_single_deselect: true});
    });
</script>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            <label for="email">Masukkan Id Barang:</label>
            <select name="id_barang" onchange="selectIdBarang($(this).val())" id="combo_id_barang" class="form-control chosen-select">
                <?php echo cmb_barang() ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            <label for="email">Harga barang:</label>
            <input type="number" class="form-control col-sm-3" id="harga" name="harga" required="required">
        </div>
    </div>
</div>
<div id="isian_lain_jenis"></div>