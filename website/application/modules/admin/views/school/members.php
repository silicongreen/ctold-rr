<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <input type="hidden" id="modelwidth" value="85%" />
            <input type="hidden" id="modelheight" value="90%" />

            <div class="flat_area grid_16">
                <h2>Manage Members</h2>
            </div>

            <input type="hidden" name="school_id" id="school_id" value="<?php echo $school_id ?>" />
            <?php echo form_open('', array('class' => 'validate_form')); ?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("Full Name", "input");
                $filter_array[2] = array("Type", "form_dropdown", $member_type);
                $filter_array[4] = array("Status", "form_dropdown", $member_status);
                create_filter($filter_array);
                ?>
            </div>

            <div class="box grid_16 single_datatable">
                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close(); ?> 

        </div>
    </div>
</div>

<style type="text/css">
    #dt1 .members_table > tbody > tr > td { 
        vertical-align: middle;
        text-align: center;
    }
    
    #dt1 .members_table > tbody > tr > td > button { 
        float: none !important;
    }
    
    #dt1 .members_table > tbody > tr > td > img { 
        display: initial;
    }
</style>