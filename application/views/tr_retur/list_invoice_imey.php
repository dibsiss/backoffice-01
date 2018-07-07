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
                        <th>Imey</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($isi as $key => $i) {
                        ?>
                        <tr>
                            <td width="10" class="text-center"><?php echo $key + 1 ?></td>
                            <td><?php echo @$i->imey ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Imey</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>