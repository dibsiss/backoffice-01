<script src="<?php echo base_url('assets/plugins/daterangepicker/bootstrap-datepicker.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/daterangepicker/datepicker3.css') ?>">

<script>
    $(function () {
        $('#tgl_nota').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
        $('#tgl_tempo').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
		$('#tgl_setoran').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
    })
</script>