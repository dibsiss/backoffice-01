<?php $this->load->view('pluggins/jquery') ?>
<?php $this->load->view('pluggins/switch_on_off'); ?>
<?php $this->load->view('pluggins/alert'); ?>
<?php $this->load->view('pluggins/datatable_client') ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h4>List Imey</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php $this->load->view('pluggins/loading') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table id="table_client" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Trouble</th>
                        <th>Imey</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    ($tombolComplain == 1) ? $tampil = 'inline' : $tampil = 'none';
                    foreach ($isi as $key => $i) {
                        if ($is_hp == 1) {
                            $idTable = $i->idd_pengiriman;
                        } else {
                            $idTable = $i->idimey_pengiriman;
                        }
                        ?>
                        <tr>
                            <td width="10" class="text-center"><?php echo $key + 1 ?></td>
                            <td width="20" class="text-center"><div style="display: <?php echo $tampil ?>"><input data-on="Ya" data-off="Tidak" class="combo-complain" value="<?php echo $idTable; ?>" data-toggle="toggle" data-size="mini" <?php echo (@$i->is_trouble == 1) ? 'checked' : '' ?>  type="checkbox"></div> </td>
                            <td><?php echo @$i->imey ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Trouble</th>
                        <th>Imey</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.combo-complain').change(function () {
            switchOn($(this).prop('checked'), $(this).val());
        })
    });
    function switchOn(respon, id_detail) {
        if (respon === true) {
            $('#loading').css('display', 'inline');
            $.post("<?php echo site_url(); ?>/umum/setDetailComplain/" + id_detail + "/" +<?php echo $is_hp ?>, {}, function (obj)
            {
                $('#loading').css('display', 'none');
            });
        } else {
            $('#loading').css('display', 'inline');
            $.post("<?php echo site_url(); ?>/umum/destroyDetailComplain/" + id_detail + "/" +<?php echo $is_hp ?>, {}, function (obj)
            {
                $('#loading').css('display', 'none');
            });
        }
    }
</script>