<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
        
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" />

        <input type="hidden" id="modelheight" value="99%" />
        
        <?php if(is_integer(($datatableSortBy)) && $datatableSortBy >= 0 && !empty($datatableSortDirection)){?>
            <input type="hidden" id="sortIndex" value="<?php echo $datatableSortBy;?>" />
            <input type="hidden" id="sorttype" value="<?php echo $datatableSortBy;?>" />
        <?php }?>
        
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Spelling Bee Words</h2>

            </div>
             <?php echo form_open('',array('class' => 'validate_form'));?>

                <div class="box grid_16" >
                    <?php
                        $filter_array = array();

                        $level = array(NULL => "Selcet", 1 => "Easy", 2 => "Medium", 3 => "Hard");
                        $filter_array[5] = array("Level", "form_dropdown", $level);

                        $status = array(NULL => "Selcet", 0 => "Disabled", 1 => "Enabled");
                        $filter_array[6] = array("Status", "form_dropdown", $status);

                        $source = array(NULL => "Selcet", 1 => "Word Bank", 2 => "Others", 3 => "Daily Star");
                        $filter_array[7] = array("Status", "form_dropdown", $source);

                        create_filter($filter_array);
                    ?>
                </div>

                <?php if(access_check("spellingbee", "add")): ?>
                <div class="flat_area grid_16">
                    <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/spellingbee/add/" >
                        <span>Add Words</span>
                    </button>
                </div>  
                <?php endif; ?>
                <div class="box grid_16 single_datatable">

                    <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
                </div>
            
            <?php echo form_close();?>
            
        </div>
