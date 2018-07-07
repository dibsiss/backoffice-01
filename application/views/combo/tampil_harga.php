<script>
    $(document).on('change', '#field-id_barang', function (e) {
        var id=$("#field-id_barang").val();
        $.post("<?php echo site_url('umum/showHargaBeli') ?>/" + id, function (result) {
            $("#tampilHargaBeli").html(result);
        });
    })
</script>
<div id='tampilHargaBeli' style="display:inline;"> Harga Beli </div> / 
<input class='numeric form-control' style="width: 85% !important;display:inline;" type='text' maxlength='50' value=' <?php echo $value ?>' name='harga_jual'>