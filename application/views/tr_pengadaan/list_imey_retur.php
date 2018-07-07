<!doctype html>
<html>
    <head>
        <title>Detail Barang</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/bootstrap/css/bootstrap.css') ?>"/>
        <link rel="stylesheet" href="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.css') ?>"/>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/js/jquery-1.11.2.min.js') ?>" ></script>
        <script src="<?php echo base_url() ?>/assets/template/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/jquery.dataTables.js') ?>" ></script>
        <script src="<?php echo base_url('assets/plugins/datatable_serverside/datatables/dataTables.bootstrap.js') ?>"></script>
        <?php $this->load->view('pluggins/alert'); ?>

    </head>
    <body>
        <div class="container">
            <!--<div class="alert alert-info"><h3>List Imey</h3></div>-->
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a data-toggle="tab" href="#nonhp">Detail Barang</a></li>
            </ul>
            <br>
            <?php $this->load->view('pluggins/loading') ?>
            <div class="tab-content">
                <div id="nonhp" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mytable">
                            <thead>
                                <tr>
                                    <th class="text-center">No
                                    <th >Id Barang</th>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Imey</th>
                                </tr>
                            </thead>
                            <tbody>
                                   <?php 
                                   $no=1;
                                   foreach($tabel as $t){
                                       //get Imey
                                       $imey = $t->imey;
                                       if($imey=='imey_retur'){
                                           $dataImey=$this->db->get_where('imey_retur',array('idd_retur'=>$t->idd_retur))->result();
                                       }else{
                                           $dataImey = $this->db->get_where('d_retur',array('idh_retur'=>$t->idh_retur,'id_barang'=>$t->id_barang))->result();
                                       }
                                       $imeyku = $this->general->extrackArray($dataImey,'imey');
                                       ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++ ?>
                                    <td><?php echo @$t->id_barang ?>
                                    <td><?php echo @$t->nama ?>
                                    <td><?php echo (@$t->imey=='imey_retur')?@$t->jumlah:@$t->jumlah_hp ?>
                                    <td><?php echo @$imeyku ?>
                                </tr>
                                       <?php
                                   }
                                   ?>
                            </tbody>
                            <tfoot>
                                 <tr>
                                     <th>No
                                    <th>Id Barang</th>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Imey</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <script type="text/javascript">
            $(function () {
                $("#mytable").dataTable();
                console.log('testing');
            });
        </script>
    </body>
</html>

