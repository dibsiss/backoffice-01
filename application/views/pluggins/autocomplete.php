<script src="<?php echo base_url('assets/plugins/autocomplete/js/jquery.mockjax.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/autocomplete/js/bootstrap-typeahead.js') ?>"></script>
<script>
    $(function () {
        var id;
        var nilai;

        $('#barang_pengadaan').typeahead({
            onSelect: function (item) {
                $.get("<?php echo site_url('umum/getbarangbyid') ?>/" + item.value, function (data) {
                    result = eval('(' + data + ')');
                    $('#kodeBrg').val(result.id_barang);
                    $('#id_barang').val(result.id_barang);
                    $('#namaBrg').val(result.nama);
                    if (result.nama_category == 'HANDPHONE') {
                        $("#imey").css("display", "inline-block");
                    } else {
                        $("#imey").css("display", "none");
                    }
                });
            },
            ajax: {
                url: "<?php echo site_url('umum/getBarang/') ?>",
                timeout: 100,
                valueField: "id",
                displayField: "val",
                triggerLength: 3,
                method: "get",
                preDispatch: function (query) {
                    return {
                        search: query
                    }
                },
                preProcess: function (data) {
                    return data;
                }
            }
        });

       
    });
</script>