<?php
    //Start counting the buttons that we have:
    $buttons_counter = 0;

    if (!$unset_edit) {
        $buttons_counter++;
    }

    if (!$unset_read) {
        $buttons_counter++;
    }

    if (!$unset_delete) {
        $buttons_counter++;
    }

    if (!empty($list[0]) && !empty($list[0]->action_urls)) {
        $buttons_counter = $buttons_counter +  count($list[0]->action_urls);
    }

    $show_more_button  = $buttons_counter > 2 ? true : false;

    //The more lang string exists only in version 1.5.2 or higher
    $more_string =
        preg_match('/1\.(5\.[2-9]|[6-9]\.[0-9])/', Grocery_CRUD::VERSION)
            ? $this->l('list_more') : "More";

?>

<?php foreach($list as $num_row => $row){ ?>
    <tr>
        <td <?php if ($unset_delete) { ?> style="border-right: none;"<?php } ?>
            <?php if ($buttons_counter === 0) {?>class="hidden"<?php }?>>
            <?php if (!$unset_delete) { ?>
                <input type="checkbox" class="select-row" data-id="<?php echo $row->primary_key_value; ?>" />
            <?php } ?>
        </td>
        <td <?php if ($unset_delete) { ?> style="border-left: none;"<?php } ?>
            <?php if ($buttons_counter === 0) {?>class="hidden"<?php }?>>
                <div class="only-desktops"  style="white-space: nowrap">
                    <?php if(!$unset_edit){?>
                        <a class="btn btn-default" href="<?php echo $row->edit_url?>"><i class="fa fa-pencil"></i> <?php echo $this->l('list_edit'); ?></a>
                    <?php } ?>
                    <?php if (!empty($row->action_urls) || !$unset_read || !$unset_delete) { ?>

                        <?php if ($show_more_button) { ?>
                            <div class="btn-group dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <?php echo $more_string; ?>
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu">
                                    <?php
                                    if(!empty($row->action_urls)){
                                        foreach($row->action_urls as $action_unique_id => $action_url){
                                            $action = $actions[$action_unique_id];
//                                            echo $action->css_class;
                                            ?>
                                            <li>
                                                <?php 
                                                if(strpos($action->css_class, 'emodal')!==false){                                                
                                                    ?>
                                                    <a href="javascript:void(0)" onclick="emodal('<?php echo $action_url; ?>','<?php echo $action->label?>')">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }elseif(strpos($action->css_class, 'custom_function')!==false){
                                                    $getFunction = explode("/",$action_url);
                                                    $function=$getFunction[count($getFunction)-2]; //ambil dari uri urutan nomer 2 dari belakang karen nomer satu adalah primary
                                                     ?>
                                                    <a href="javascript:void(0)" onclick="<?php echo $function ;?>">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }
                                                else{ ?>
                                                <a href="<?php echo $action_url; ?>">
                                                    <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                                <?php } ?>
                                            </li>
                                        <?php }
                                    }
                                    ?>
                                    <?php if (!$unset_read) { ?>
                                        <li>
                                            <a href="<?php echo $row->read_url?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if (!$unset_delete) { ?>
                                        <li>
                                            <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row">
                                                <i class="fa fa-trash-o text-danger"></i>
                                                <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } else {
                                if(!empty($row->action_urls)){
                                    foreach($row->action_urls as $action_unique_id => $action_url){
                                        $action = $actions[$action_unique_id];
                                        ?>
                        <!-- asli dari grocery crud -->
<!--                                        <a href="<?php echo $action_url; ?>" class="btn btn-default">
                                            <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                        </a>-->
                                                <?php 
                                                if(strpos($action->css_class, 'emodal')!==false){                                                
                                                    ?>
                                                    <a class="btn btn-default" href="javascript:void(0)" onclick="emodal('<?php echo $action_url; ?>','<?php echo $action->label?>')">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }elseif(strpos($action->css_class, 'custom_function')!==false){
                                                    $getFunction = explode("/",$action_url);
                                                    $function=$getFunction[count($getFunction)-2]; //ambil dari uri urutan nomer 2 dari belakang karen nomer satu adalah primary
                                                     ?>
                                                    <a class="btn btn-default" href="javascript:void(0)" onclick="<?php echo $function ;?>">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }
                                                else{ ?>
                                                <a class="btn btn-default" href="<?php echo $action_url; ?>">
                                                    <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                                <?php } ?>
                                    <?php }
                                }

                                if (!$unset_read) { ?>
                                    <a class="btn btn-default" href="<?php echo $row->read_url?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                <?php }

                                if (!$unset_delete) { ?>
                                    <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row btn btn-default">
                                        <i class="fa fa-trash-o text-danger"></i>
                                        <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                    </a>
                                <?php } ?>
                            <?php } ?>

                    <?php } ?>
                </div>
                <div class="only-mobiles">
                    <?php if ($buttons_counter > 0) { ?>
                    <div class="btn-group dropdown">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php echo $this->l('list_actions'); ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php if (!$unset_edit) { ?>
                            <li>
                                <a href="<?php echo $row->edit_url?>">
                                    <i class="fa fa-pencil"></i> <?php echo $this->l('list_edit'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php
                            if(!empty($row->action_urls)){
                                foreach($row->action_urls as $action_unique_id => $action_url){
                                    $action = $actions[$action_unique_id];
                                    ?>
<!--                                    <li>
                                        <a href="<?php echo $action_url; ?>">
                                            <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                        </a>
                                    </li>-->
<li>
                                                <?php 
                                                if(strpos($action->css_class, 'emodal')!==false){                                                
                                                    ?>
                                                    <a href="javascript:void(0)" onclick="emodal('<?php echo $action_url; ?>','<?php echo $action->label?>')">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }elseif(strpos($action->css_class, 'custom_function')!==false){
                                                    $getFunction = explode("/",$action_url);
                                                    $function=$getFunction[count($getFunction)-2]; //ambil dari uri urutan nomer 2 dari belakang karen nomer satu adalah primary
                                                     ?>
                                                    <a href="javascript:void(0)" onclick="<?php echo $function ;?>">
                                                        <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                    </a>
                                                    <?php
                                                }
                                                else{ ?>
                                                <a href="<?php echo $action_url; ?>">
                                                    <i class="fa <?php echo $action->css_class; ?>"></i> <?php echo $action->label?>
                                                </a>
                                                <?php } ?>
                                            </li>
                                <?php }
                            }
                            ?>
                            <?php if (!$unset_read) { ?>
                                <li>
                                    <a href="<?php echo $row->read_url?>"><i class="fa fa-eye"></i> <?php echo $this->l('list_view')?></a>
                                </li>
                            <?php } ?>
                            <?php if (!$unset_delete) { ?>
                                <li>
                                    <a data-target="<?php echo $row->delete_url?>" href="javascript:void(0)" title="<?php echo $this->l('list_delete')?>" class="delete-row">
                                        <i class="fa fa-trash-o text-danger"></i> <span class="text-danger"><?php echo $this->l('list_delete')?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
        </td>
        <?php foreach($columns as $column){?>
            <td>
                <?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;' ; ?>
            </td>
        <?php }?>
    </tr>
<?php } ?>
