<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="50%">

        <input type="hidden" id="modelheight" value="50%">
        
        <input type="hidden" id="categoryphotomodelwidth" value="95%" />

        <input type="hidden" id="categoryphotomodelheight" value="95%" />

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Manage level</h2>

            </div>

            <?php echo form_open('', array('class' => 'validate_form')); ?>
            <div class="box grid_16" >
                <?php
                $filter_array = array();

                $filter_array[0] = array("Title", "input");
                $filter_array[1] = array("Topic", "form_dropdown", $category);
                
                
                $status = array(NULL=>"Select",0=>"Inactive",1=>"Active");
                $filter_array[5] = array("Status", "form_dropdown", $status);
                
                create_filter($filter_array);
                ?>


            </div>
            <?php if (access_check("topicandquestion", "add")): ?>
                <div class="flat_area grid_16">
                    <button  type="button"  class="light text_only has_text model" id="<?php echo base_url('admin/topicandquestion/add'); ?>" >
                        <span>Add Level</span>
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
    #dt1 .mytable > tbody > tr > td { 
        text-align: center;
    }

    #dt1 .mytable > tbody > tr > td > button { 
        float: none !important;
        margin: 5px 5px 5px 0px !important;
    }
    
    #dt1 .mytable > tbody > tr > td > img { 
        display: initial;
    }
</style>