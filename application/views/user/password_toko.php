<script>
    function changePassword(value){
        emodal("<?php echo site_url('umum/changePasswordToko/') ?>" + value, "List Imey");
    }
</script>
<input type="button" value="Ubah Password" class="btn btn-primary" onclick="changePassword('<?php echo $isi ?>')">

