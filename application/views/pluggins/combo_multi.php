<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/chosen_multi/chosen.min.css">
<script src="<?php echo base_url() ?>assets/plugins/chosen_multi/chosen.jquery.min.js"></script>
<style>
    .chosen-container-single .chosen-single {
        height: 34px !important;
        line-height: 29px !important;
    }
    .chosen-container {
        font-weight: normal !important;
    }
</style>
<script>
    //untuk chosen
        var config = {
            '.chosen-select-deselect': {
                allow_single_deselect: true
            }, '.chosen-select-deselect2': {
                allow_single_deselect: true
            }
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
</script>