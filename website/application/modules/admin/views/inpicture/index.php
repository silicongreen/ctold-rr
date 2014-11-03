<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;
       
        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="80%" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage In Pictures Theme</h2>

            </div>

            <?php echo form_open('',array('class' => 'validate_form'));?>
            <div class="box grid_16" >
                <?php
                $filter_array = array();

                $filter_array[1] = array("Name", "input");
                $filter_array[2] = array("Date", "input_date");
                
                $status = array(NULL=>"Selcet",1=>"Yes",0=>"No");
                $filter_array[3] = array("Is Current", "form_dropdown", $status);
                
                $status = array(NULL=>"Selcet",1=>"Yes",0=>"No");
                $filter_array[4] = array("Is Active", "form_dropdown", $status);
               
                
                create_filter($filter_array);
                ?>


            </div>
            <?php if(access_check("inpicture","add")): ?>
            <div class="flat_area grid_16">
                <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/inpicture/add/" >
                    <span>Add New Theme (In Picture)</span>
                </button>
            </div> 
            <?php endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 
        </div>






