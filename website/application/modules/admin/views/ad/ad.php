<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;        
        $widget->run('sidebar');
        ?>
        <input type="hidden" id="controllername" value="ad" >


        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="97%" >
        <?php if(is_integer(($datatableSortBy)) && $datatableSortBy >= 0 && !empty($datatableSortDirection)){?>
        <input type="hidden" id="sortIndex" value="<?php echo $datatableSortBy;?>" />
        <input type="hidden" id="sorttype" value="<?php echo $datatableSortBy;?>" />
        <?php }?>
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage Ads</h2>

            </div>
            <?php echo form_open('',array('class' => 'validate_form'));?>
            
            
            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[1] = array("Name", "input");
                $status = array(NULL=>"Select",1 => 'Active', 0 => 'Inactive');
                $filter_array[2] = array("Status", "form_dropdown", $status);
                $ad_link_location = array( NULL=>"Select", 'index' => 'Home', 'section' => 'Section', 'details' => 'Details');
                $filter_array[3] = array("Location", "form_dropdown", $ad_link_location);
                $filter_array[4] = array("Location Plan", "form_dropdown",$ad_plans );
                $forall = array(NULL=>"Select",1 => 'Yes', 0 => 'No');
                $filter_array[5] = array("For All", "form_dropdown", $forall);
                create_filter($filter_array);
                ?>


            </div>
            <?php if(access_check("ad",'add')):?>
            <div class="flat_area grid_16">
                <button type="button" class="light text_only has_text model" id="<?php echo  base_url(); ?>admin/ad/add/" >
                    <span>Add Ad</span>
                </button>
            </div>  
            <?php endif;?>
            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
           <?php echo form_close();?>  
        </div>






