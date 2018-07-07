<?php
$this->load->view('pluggins/datatable_server');
?>
<table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
           <th>Action</th>
            <th>ID Transaksi</th>
            <th>Supliyer</th>
            <th>Tanggal Nota</th>
            <th>No Nota</th>
            <th>Id User</th>
            <th>Tgl Input</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Action</th>
            <th>ID Transaksi</th>
            <th>Supliyer</th>
            <th>Tanggal Nota</th>
            <th>No Nota</th>
            <th>Id User</th>
            <th>Tgl Input</th>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function () {
        $('#example tfoot th').each(function () {
            var title = $(this).text();
            if (title != 'Action') {
                var inp = '<input type="text" class="form-control" placeholder="Search ' + title + '" />';
                $(this).html(inp);
            }
        });
        var table = $('#example').DataTable({
        "responsive": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo site_url('laporan/prosesTablePengadaan'); ?>",
                "type": "POST"
            }
        });
        table.columns().every(function () {
            var that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                            .search(this.value)
                            .draw();
                }
            });
        });
    });
</script>
