<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <input type="hidden" id="modelwidth" value="95%" />
            <input type="hidden" id="modelheight" value="90%" />

            <div class="flat_area grid_16">
                <h2>Manage Questions</h2>
            </div>

            <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id ?>" />
            <?php echo form_open('', array('class' => 'validate_form')); ?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("Question", "input");
                $filter_array[1] = array("Mark", "input");
                $filter_array[2] = array("Style", "form_dropdown", $style);
                create_filter($filter_array);
                ?>
            </div>
            
            <?php if (access_check("assessment", "add_question")): ?>
                <div class="flat_area grid_16">
                    <button  type="button"  class="light text_only has_text model" id="<?php echo base_url('admin/assessment/add_question/' . $assessment_id); ?>" >
                        <span>Add Question</span>
                    </button>
                </div> 
            <?php endif; ?>
            
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