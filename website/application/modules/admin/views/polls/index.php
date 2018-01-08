<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;   
        $widget->run('sidebar');
        ?>


        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="73%" >
        
        <input type="hidden" id="modelwidth2" value="40%" >

        <input type="hidden" id="modelheight2" value="45%" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Polls</h2>

            </div>
             <?php echo form_open('',array('class' => 'validate_form'));?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();

                $filter_array[0] = array("Question", "input");
                $filter_array[2] = array("Date", "input_date");
            
                
                create_filter($filter_array);
                ?>


            </div>
            <?php if(access_check("polls","add")): ?>
            <div class="flat_area grid_16">
                <button  type="button"  class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/polls/add/" >
                    <span>Add Polls</span>
                </button>
            </div>  
            <?php endif; ?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close();?> 
        </div>






