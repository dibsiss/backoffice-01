<?php echo ($needJquery==true)? "<script src='".base_url()."/assets/grocery_crud/js/jquery-1.11.1.min.js'></script>" : '';?>
<script src="<?php echo base_url() ?>/assets/grocery_crud/texteditor/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url() ?>/assets/grocery_crud/texteditor/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript">
    $(function () {
        $('textarea.texteditor').ckeditor({toolbar: '<?php echo (!empty($toolbar))? $toolbar : 'Basic'?>'});
//        $('textarea.mini-texteditor').ckeditor({toolbar: 'Basic', width: 700});
    });
    var js_date_format = 'dd/mm/yy';
</script>

<!--contoh penggunaan-->
<!--<textarea id='field-keterangan' name='keterangan' class='texteditor' ></textarea>--> 
<!--contoh memanggil di controller-->
<!--$ckeditorSetting = array('toolbar'=>'Basic','needJquery'=>false);
$this->load->view('pluggins/textarea',$ckeditorSetting); ?>-->

