<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
       
        $widget->run('sidebar');
        ?>


        <input type="hidden" id="modelwidth" value="80%" />

        <input type="hidden" id="modelheight" value="97%" />
        
        <?php if(is_integer(($datatableSortBy)) && $datatableSortBy >= 0 && !empty($datatableSortDirection)){ ?>
            <input type="hidden" id="sortIndex" value="<?php echo $datatableSortBy;?>" />
            <input type="hidden" id="sorttype" value="<?php echo $datatableSortDirection;?>" />
        <?php } ?>

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Admin Users</h2>

            </div>
            <?php echo form_open('',array('class' => 'validate_form'));?>
            
            
            <div class="box grid_16" >
                <?php
                $filter_array = array();

                $filter_array[1] = array("Email", "input");
                $filter_array[2] = array("Full_Name", "input");
                $filter_array[3] = array("Gender", "form_dropdown", $gender);
                $filter_array[4] = array("Mobile NO.", "input");
                create_filter($filter_array);
                ?>


            </div>
            <?php //if(access_check("free_user", "add")): ?>
<!--            <div class="flat_area grid_16">
                <button type="button" class="light text_only has_text model" id="<?php // echo base_url(); ?>admin/free_user/add/" >
                    <span>Add User</span>
                </button>
            </div> -->
            <?php //endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
           <?php echo form_close();?>  
        </div>






