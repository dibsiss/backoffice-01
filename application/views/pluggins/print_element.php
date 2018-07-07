<script type="text/javascript" src="<?php echo base_url('assets/plugins/print/jquery.printelement.js') ?>"></script>    
<script>
        $(document).ready(function () {
            $("#simplePrint").click(function (e) {
                $('#toPrint').printElement(
				{
					printBodyOptions:
						{
						styleToAdd:'font-size:10px !important;padding:3px !important;margin:3px !important',
						}
				}
				);
            });
        });

    </script>