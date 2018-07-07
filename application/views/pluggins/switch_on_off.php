<link rel="stylesheet" href="<?php echo base_url('assets/plugins/switch_on_off/bootstrap-toggle.css')?>">
<script src="<?php echo base_url('assets/plugins/switch_on_off/bootstrap-toggle.js')?>"></script>
<script>
  $(function() {
    $('#toggle-event').change(function() {
	switchOn($(this).prop('checked'));
    })
  })
</script>