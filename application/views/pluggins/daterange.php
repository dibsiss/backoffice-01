<link rel="stylesheet" href="<?php echo base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
<script src="<?php echo base_url('assets/plugins/daterangepicker/moment.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/daterangepicker/daterangepicker.js') ?>" type="text/javascript"></script>
<script>
    $(function () {
        $('.daterange').daterangepicker(
                {
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    rangeSplitter: ' to ',
                    datepickerOptions: {
                        changeMonth: true,
                        changeYear: true
                    }
                }
        );
    });
</script>